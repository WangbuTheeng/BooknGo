<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret');
        Stripe::setApiKey($this->secretKey);
    }

    /**
     * Create a payment intent for Stripe
     */
    public function createPaymentIntent(Booking $booking, Payment $payment)
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $booking->total_amount * 100, // Convert to cents
                'currency' => 'npr', // Nepalese Rupee
                'metadata' => [
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    'booking_reference' => $booking->booking_reference,
                ],
                'description' => "Bus booking for {$booking->trip->route->fromCity->name} to {$booking->trip->route->toCity->name}",
            ]);

            return [
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment intent creation failed', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
            ]);

            throw new \Exception('Failed to create payment intent: ' . $e->getMessage());
        }
    }

    /**
     * Verify payment with Stripe
     */
    public function verifyPayment($paymentIntentId)
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100, // Convert back from cents
                'currency' => $paymentIntent->currency,
                'metadata' => $paymentIntent->metadata->toArray(),
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment verification failed', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
            ]);

            return [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle successful payment
     */
    public function handleSuccessfulPayment($paymentIntentId)
    {
        $verification = $this->verifyPayment($paymentIntentId);

        if ($verification['status'] !== 'succeeded') {
            return [
                'status' => 'error',
                'message' => 'Payment verification failed'
            ];
        }

        $paymentId = $verification['metadata']['payment_id'] ?? null;
        $payment = Payment::find($paymentId);

        if (!$payment) {
            return [
                'status' => 'error',
                'message' => 'Payment record not found'
            ];
        }

        // Update payment status
        $payment->update([
            'payment_status' => 'success',
            'payment_time' => now(),
            'response_log' => $verification,
        ]);

        // Update booking status
        $payment->booking->update([
            'status' => 'booked',
            'payment_status' => 'paid',
        ]);

        return [
            'status' => 'success',
            'payment' => $payment,
            'message' => 'Payment completed successfully'
        ];
    }

    /**
     * Handle failed payment
     */
    public function handleFailedPayment($paymentIntentId, $errorMessage = null)
    {
        $verification = $this->verifyPayment($paymentIntentId);
        $paymentId = $verification['metadata']['payment_id'] ?? null;
        $payment = Payment::find($paymentId);

        if ($payment) {
            $payment->update([
                'payment_status' => 'failed',
                'response_log' => array_merge($verification, ['error' => $errorMessage]),
            ]);
        }

        return [
            'status' => 'error',
            'message' => $errorMessage ?: 'Payment failed'
        ];
    }

    /**
     * Get Stripe publishable key
     */
    public function getPublishableKey()
    {
        return config('services.stripe.key');
    }
}
