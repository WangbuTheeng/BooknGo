<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
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
            'payment_method' => 'required|in:eSewa,Khalti,Cash',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($booking->status !== 'booked') {
            return back()->withErrors(['error' => 'Payment is not required for this booking.']);
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
                'payment_status' => $request->payment_method === 'Cash' ? 'pending' : 'pending',
            ]);

            // For cash payments, mark as pending (to be confirmed by operator)
            if ($request->payment_method === 'Cash') {
                // Keep booking status as 'booked' since that's the only valid confirmed status
                $payment->update(['payment_status' => 'pending']);

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
                    'status' => 'completed',
                    'gateway_transaction_id' => $request->get('transaction_id'),
                    'gateway_response' => json_encode($request->all()),
                ]);

                $payment->booking->update(['status' => 'confirmed']);

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
        $payment->update(['status' => 'failed']);
        
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

        if ($payment->payment_method !== 'cash' || $payment->status !== 'pending') {
            return back()->withErrors(['error' => 'This payment cannot be confirmed.']);
        }

        $payment->update(['status' => 'completed']);

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
        // This would integrate with actual payment gateways
        // For now, we'll simulate the process
        
        $callbackUrl = route('payments.callback', $payment);
        
        if ($method === 'esewa') {
            // eSewa integration would go here
            return view('payments.esewa', compact('payment', 'callbackUrl'));
        } elseif ($method === 'khalti') {
            // Khalti integration would go here
            return view('payments.khalti', compact('payment', 'callbackUrl'));
        }

        return back()->withErrors(['error' => 'Payment method not supported yet.']);
    }
}
