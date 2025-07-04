<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class KhaltiService
{
    private $publicKey;
    private $secretKey;
    private $baseUrl;
    private $websiteUrl;

    public function __construct()
    {
        $this->publicKey = config('services.khalti.public_key');
        $this->secretKey = config('services.khalti.secret_key');
        $this->baseUrl = config('services.khalti.base_url');
        $this->websiteUrl = config('services.khalti.website_url');
    }

    /**
     * Initiate payment with Khalti
     */
    public function initiatePayment(Booking $booking, Payment $payment)
    {
        try {
            $payload = [
                'return_url' => route('payments.khalti.callback'),
                'website_url' => $this->websiteUrl,
                'amount' => $booking->total_amount * 100, // Convert to paisa
                'purchase_order_id' => $payment->transaction_id,
                'purchase_order_name' => "Bus Booking - {$booking->trip->route->fromCity->name} to {$booking->trip->route->toCity->name}",
                'customer_info' => [
                    'name' => $booking->user->name,
                    'email' => $booking->user->email,
                    'phone' => $booking->user->phone ?? '',
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . 'initiate/', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Store pidx in payment record for later verification
                $payment->update([
                    'gateway_transaction_id' => $responseData['pidx'],
                    'gateway_response' => json_encode($responseData),
                ]);

                return [
                    'status' => 'success',
                    'pidx' => $responseData['pidx'],
                    'payment_url' => $responseData['payment_url'],
                ];
            } else {
                Log::error('Khalti payment initiation failed', [
                    'response' => $response->body(),
                    'status' => $response->status(),
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                ]);

                return [
                    'status' => 'error',
                    'message' => 'Failed to initiate payment with Khalti',
                ];
            }
        } catch (Exception $e) {
            Log::error('Khalti payment initiation exception', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
            ]);

            return [
                'status' => 'error',
                'message' => 'Payment initiation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment with Khalti lookup API
     */
    public function verifyPayment($pidx)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . 'lookup/', [
                'pidx' => $pidx,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                return [
                    'status' => 'success',
                    'payment_status' => $responseData['status'],
                    'transaction_id' => $responseData['transaction_id'] ?? null,
                    'amount' => $responseData['total_amount'] / 100, // Convert back from paisa
                    'data' => $responseData,
                ];
            } else {
                Log::error('Khalti payment verification failed', [
                    'response' => $response->body(),
                    'status' => $response->status(),
                    'pidx' => $pidx,
                ]);

                return [
                    'status' => 'error',
                    'message' => 'Payment verification failed',
                ];
            }
        } catch (Exception $e) {
            Log::error('Khalti payment verification exception', [
                'error' => $e->getMessage(),
                'pidx' => $pidx,
            ]);

            return [
                'status' => 'error',
                'message' => 'Payment verification failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Handle successful payment callback
     */
    public function handleSuccessCallback($request)
    {
        $pidx = $request->get('pidx');
        $status = $request->get('status');
        $transactionId = $request->get('transaction_id');
        $amount = $request->get('amount');

        // Find payment by pidx
        $payment = Payment::where('gateway_transaction_id', $pidx)->first();

        if (!$payment) {
            return [
                'status' => 'error',
                'message' => 'Payment record not found',
            ];
        }

        // Verify payment with Khalti
        $verification = $this->verifyPayment($pidx);

        if ($verification['status'] === 'success' && $verification['payment_status'] === 'Completed') {
            // Update payment status
            $payment->update([
                'payment_status' => 'success',
                'payment_time' => now(),
                'gateway_response' => json_encode($verification['data']),
            ]);

            // Update booking status
            $payment->booking->update([
                'status' => 'booked',
                'payment_status' => 'paid',
            ]);

            return [
                'status' => 'success',
                'payment' => $payment,
                'message' => 'Payment completed successfully',
            ];
        } else {
            // Payment verification failed
            $payment->update([
                'payment_status' => 'failed',
                'gateway_response' => json_encode($verification),
            ]);

            return [
                'status' => 'error',
                'message' => 'Payment verification failed',
            ];
        }
    }

    /**
     * Handle failed payment callback
     */
    public function handleFailureCallback($request)
    {
        $pidx = $request->get('pidx');
        $status = $request->get('status');

        // Find payment by pidx
        $payment = Payment::where('gateway_transaction_id', $pidx)->first();

        if ($payment) {
            $payment->update([
                'payment_status' => 'failed',
                'gateway_response' => json_encode($request->all()),
            ]);
        }

        return [
            'status' => 'error',
            'message' => 'Payment was cancelled or failed',
        ];
    }

    /**
     * Get public key for frontend
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }
}
