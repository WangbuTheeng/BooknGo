<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ESewaService
{
    private $merchantId;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('services.esewa.merchant_id', 'EPAYTEST');
        $this->secretKey = config('services.esewa.secret_key', '8gBm/:&EnhH.1/q');
        $this->baseUrl = config('services.esewa.base_url', 'https://rc-epay.esewa.com.np');
    }

    /**
     * Generate payment form data for eSewa v2 API
     */
    public function generatePaymentForm(Booking $booking, Payment $payment)
    {
        $amount = $booking->total_amount;
        $taxAmount = 0;
        $serviceCharge = 0;
        $deliveryCharge = 0;
        $totalAmount = $amount + $taxAmount + $serviceCharge + $deliveryCharge;

        $successUrl = route('payments.esewa.success');
        $failureUrl = route('payments.esewa.failure');

        // Generate signature for v2 API
        $signedFieldNames = 'total_amount,transaction_uuid,product_code';
        $signature = $this->generateSignature($totalAmount, $payment->transaction_id, $this->merchantId);

        return [
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'transaction_uuid' => $payment->transaction_id,
            'product_code' => $this->merchantId,
            'product_service_charge' => $serviceCharge,
            'product_delivery_charge' => $deliveryCharge,
            'success_url' => $successUrl,
            'failure_url' => $failureUrl,
            'signed_field_names' => $signedFieldNames,
            'signature' => $signature,
        ];
    }

    /**
     * Verify payment with eSewa v2 API
     */
    public function verifyPayment($transactionId, $amount, $referenceId = null)
    {
        try {
            // Use status check API for v2
            $url = $this->baseUrl . '/api/epay/transaction/status/';
            $response = Http::get($url, [
                'product_code' => $this->merchantId,
                'total_amount' => $amount,
                'transaction_uuid' => $transactionId,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['status']) && $responseData['status'] === 'COMPLETE') {
                    return [
                        'status' => 'success',
                        'message' => 'Payment verified successfully',
                        'data' => $responseData
                    ];
                } else {
                    return [
                        'status' => 'failed',
                        'message' => 'Payment verification failed: ' . ($responseData['status'] ?? 'Unknown status'),
                        'data' => $responseData
                    ];
                }
            }

            return [
                'status' => 'failed',
                'message' => 'Unable to verify payment with eSewa',
                'data' => null
            ];

        } catch (\Exception $e) {
            Log::error('eSewa payment verification failed: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Payment verification error: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get eSewa payment URL for v2 API
     */
    public function getPaymentUrl()
    {
        return $this->baseUrl . '/api/epay/main/v2/form';
    }

    /**
     * Generate HMAC-SHA256 signature for v2 API
     */
    private function generateSignature($totalAmount, $transactionUuid, $productCode)
    {
        $message = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$productCode}";
        return base64_encode(hash_hmac('sha256', $message, $this->secretKey, true));
    }

    /**
     * Process successful payment callback for v2 API
     */
    public function handleSuccessCallback($request)
    {
        // v2 API returns data parameter with base64 encoded response
        $data = $request->get('data');

        if (!$data) {
            return [
                'status' => 'error',
                'message' => 'No payment data received'
            ];
        }

        // Decode the base64 response
        $decodedData = json_decode(base64_decode($data), true);

        if (!$decodedData) {
            return [
                'status' => 'error',
                'message' => 'Invalid payment data format'
            ];
        }

        $transactionId = $decodedData['transaction_uuid'] ?? null;
        $amount = $decodedData['total_amount'] ?? null;
        $transactionCode = $decodedData['transaction_code'] ?? null;
        $status = $decodedData['status'] ?? null;

        // Find the payment record
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return [
                'status' => 'error',
                'message' => 'Payment record not found'
            ];
        }

        // Verify the signature
        $receivedSignature = $decodedData['signature'] ?? '';
        $expectedSignature = $this->generateSignature($amount, $transactionId, $this->merchantId);

        if ($receivedSignature !== $expectedSignature) {
            Log::warning('eSewa signature mismatch', [
                'received' => $receivedSignature,
                'expected' => $expectedSignature
            ]);
        }

        // Check if payment is complete
        if ($status === 'COMPLETE') {
            // Update payment status
            $payment->update([
                'status' => 'success',
                'gateway_transaction_id' => $transactionCode,
                'gateway_response' => json_encode($decodedData),
                'paid_at' => now(),
            ]);

            // Update booking payment status
            $payment->booking->update([
                'payment_status' => 'paid'
            ]);

            return [
                'status' => 'success',
                'message' => 'Payment processed successfully',
                'payment' => $payment
            ];
        }

        return [
            'status' => 'failed',
            'message' => 'Payment not completed: ' . $status,
            'payment' => $payment
        ];
    }

    /**
     * Process failed payment callback for v2 API
     */
    public function handleFailureCallback($request)
    {
        $transactionId = $request->get('transaction_uuid') ?? $request->get('pid');

        // Find the payment record
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => 'Payment failed or cancelled by user',
            ]);
        }

        return [
            'status' => 'failed',
            'message' => 'Payment was cancelled or failed',
            'payment' => $payment
        ];
    }
}
