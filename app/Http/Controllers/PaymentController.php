<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use App\Services\ESewaService;
use App\Services\StripeService;
use App\Services\KhaltiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $payments = Payment::with(['booking.user', 'booking.trip.route.fromCity', 'booking.trip.route.toCity'])
                              ->orderBy('created_at', 'desc')
                              ->paginate(15);
        } else {
            $payments = Payment::whereHas('booking', function ($query) use ($user) {
                                $query->where('user_id', $user->id);
                            })
                            ->with(['booking.trip.route.fromCity', 'booking.trip.route.toCity'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);
        }

        return view('payments.index', compact('payments'));
    }

    /**
     * Process payment for a booking
     */
    public function process(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $request->validate([
            'payment_method' => 'required|in:eSewa,Khalti,Cash,Stripe',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($booking->payment_status !== 'pending') {
            return back()->withErrors(['error' => 'Payment is not required for this booking.']);
        }

        if ($booking->isExpired()) {
            return back()->withErrors(['error' => 'This booking has expired. Please create a new booking.']);
        }

        if ($request->amount != $booking->total_amount) {
            return back()->withErrors(['error' => 'Payment amount does not match booking total.']);
        }

        DB::beginTransaction();
        
        try {
            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'method' => $request->payment_method,
                'amount' => $request->amount,
                'transaction_id' => $this->generateTransactionId(),
                'payment_status' => 'pending',
            ]);

            // For cash payments, mark as pending (to be confirmed by operator)
            if ($request->payment_method === 'Cash') {
                $booking->update(['status' => 'booked']);

                DB::commit();

                return redirect()->route('bookings.show', $booking)
                               ->with('success', 'Booking confirmed! Please pay cash to the operator.');
            }

            // For digital payments, redirect to payment gateway
            DB::commit();
            
            return $this->redirectToPaymentGateway($payment, $request->payment_method);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to process payment. Please try again.']);
        }
    }

    /**
     * Handle payment gateway callback
     */
    public function callback(Request $request, Payment $payment)
    {
        // This would handle callbacks from eSewa, Khalti, etc.
        // For now, we'll simulate successful payment
        
        if ($request->has('success') && $request->success == 'true') {
            DB::beginTransaction();
            
            try {
                $payment->update([
                    'payment_status' => 'success',
                    'gateway_transaction_id' => $request->get('transaction_id'),
                    'gateway_response' => json_encode($request->all()),
                ]);

                $payment->booking->update(['payment_status' => 'paid', 'status' => 'bought']);

                DB::commit();

                return redirect()->route('bookings.confirmation', $payment->booking)
                               ->with('success', 'Payment successful! Your booking is confirmed.');

            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('bookings.payment', $payment->booking)
                               ->withErrors(['error' => 'Payment verification failed. Please try again.']);
            }
        }

        // Payment failed
        $payment->update(['payment_status' => 'failed']);
        
        return redirect()->route('bookings.payment', $payment->booking)
                       ->withErrors(['error' => 'Payment failed. Please try again.']);
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment->booking);
        
        $payment->load(['booking.trip.route.fromCity', 'booking.trip.route.toCity', 'booking.bookingSeats.seat']);
        
        return view('payments.show', compact('payment'));
    }

    /**
     * Confirm cash payment (for operators/admin)
     */
    public function confirmCash(Payment $payment)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin() && !$user->isOperator()) {
            abort(403, 'Unauthorized access');
        }

        if ($payment->method !== 'Cash' || $payment->payment_status !== 'pending') {
            return back()->withErrors(['error' => 'This payment cannot be confirmed.']);
        }

        $payment->update(['payment_status' => 'success']);

        return back()->with('success', 'Cash payment confirmed successfully.');
    }

    /**
     * Generate a unique transaction ID
     */
    private function generateTransactionId(): string
    {
        do {
            $transactionId = 'TXN' . strtoupper(Str::random(8));
        } while (Payment::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    /**
     * Redirect to payment gateway
     */
    private function redirectToPaymentGateway(Payment $payment, string $method)
    {
        if ($method === 'eSewa') {
            $esewaService = new ESewaService();
            $formData = $esewaService->generatePaymentForm($payment->booking, $payment);
            $paymentUrl = $esewaService->getPaymentUrl();

            return view('payments.esewa', compact('payment', 'formData', 'paymentUrl'));
        } elseif ($method === 'Stripe') {
            $stripeService = new StripeService();
            try {
                $paymentIntent = $stripeService->createPaymentIntent($payment->booking, $payment);
                $publishableKey = $stripeService->getPublishableKey();

                return view('payments.stripe', compact('payment', 'paymentIntent', 'publishableKey'));
            } catch (\Exception $e) {
                return back()->withErrors(['error' => 'Failed to initialize Stripe payment: ' . $e->getMessage()]);
            }
        } elseif ($method === 'Khalti') {
            $khaltiService = new KhaltiService();
            $result = $khaltiService->initiatePayment($payment->booking, $payment);
            
            if ($result['status'] === 'success') {
                return view('payments.khalti', compact('payment', 'result'));
            } else {
                return back()->withErrors(['error' => 'Failed to initialize Khalti payment: ' . $result['message']]);
            }
        }

        return back()->withErrors(['error' => 'Payment method not supported yet.']);
    }

    /**
     * Handle eSewa success callback
     */
    public function esewaSuccess(Request $request)
    {
        $esewaService = new ESewaService();
        $result = $esewaService->handleSuccessCallback($request);

        if ($result['status'] === 'success') {
            return redirect()->route('bookings.show', $result['payment']->booking)
                           ->with('success', 'Payment completed successfully!');
        }

        return redirect()->route('bookings.index')
                       ->withErrors(['error' => $result['message']]);
    }

    /**
     * Handle eSewa failure callback
     */
    public function esewaFailure(Request $request)
    {
        $esewaService = new ESewaService();
        $result = $esewaService->handleFailureCallback($request);

        return redirect()->route('bookings.index')
                       ->withErrors(['error' => $result['message']]);
    }

    /**
     * Handle Stripe success callback
     */
    public function stripeSuccess(Request $request)
    {
        $request->validate([
            'payment_intent' => 'required|string',
        ]);

        $stripeService = new StripeService();
        $result = $stripeService->handleSuccessfulPayment($request->payment_intent);

        if ($result['status'] === 'success') {
            return redirect()->route('bookings.show', $result['payment']->booking)
                           ->with('success', 'Payment completed successfully!');
        }

        return redirect()->route('bookings.index')
                       ->withErrors(['error' => $result['message']]);
    }

    /**
     * Handle Stripe failure callback
     */
    public function stripeFailure(Request $request)
    {
        $request->validate([
            'payment_intent' => 'required|string',
            'error' => 'nullable|string',
        ]);

        $stripeService = new StripeService();
        $result = $stripeService->handleFailedPayment($request->payment_intent, $request->error);

        return redirect()->route('bookings.index')
                       ->withErrors(['error' => $result['message']]);
    }

    /**
     * Handle Khalti payment callback
     */
    public function khaltiCallback(Request $request)
    {
        $khaltiService = new KhaltiService();
        
        // Check if payment was successful
        if ($request->get('status') === 'Completed') {
            $result = $khaltiService->handleSuccessCallback($request);
            
            if ($result['status'] === 'success') {
                return redirect()->route('bookings.show', $result['payment']->booking)
                               ->with('success', 'Payment completed successfully!');
            }
        } else {
            $result = $khaltiService->handleFailureCallback($request);
        }

        return redirect()->route('bookings.index')
                       ->withErrors(['error' => $result['message']]);
    }
}
