<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

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

        try {
            $payment = $this->paymentService->processPayment($booking, $request->payment_method, $request->all());

            if ($request->payment_method === 'Cash') {
                return redirect()->route('bookings.show', $booking)
                               ->with('success', 'Booking confirmed! Please pay cash to the operator.');
            }

            return $this->redirectToPaymentGateway($payment, $request->payment_method);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process payment. Please try again.']);
        }
    }

    /**
     * Handle payment gateway callback
     */
    public function callback(Request $request, Payment $payment, string $gateway)
    {
        $gatewayService = app("App\\Services\\{$gateway}Service");
        $result = $gatewayService->handleCallback($request, $payment);

        if ($result['status'] === 'success') {
            return redirect()->route('bookings.confirmation', $payment->booking)
                           ->with('success', 'Payment successful! Your booking is confirmed.');
        }

        return redirect()->route('bookings.payment', $payment->booking)
                       ->withErrors(['error' => $result['message']]);
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
    private function redirectToPaymentGateway(Payment $payment, string $method)
    {
        $gateway = app("App\\Services\\{$method}Service");
        return $gateway->redirect($payment);
    }
}
