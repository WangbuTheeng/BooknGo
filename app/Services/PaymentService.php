<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Exception;

class PaymentService
{
    public function processPayment(Booking $booking, string $paymentMethod, array $options = [])
    {
        // Placeholder for payment processing logic.
        // In a real application, this would interact with the specific payment gateway API.

        try {
            // Simulate a successful payment
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'method' => $paymentMethod,
                'status' => 'completed',
                'transaction_id' => 'txn_' . uniqid(),
            ]);

            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'completed',
            ]);

            return $payment;
        } catch (Exception $e) {
            throw new Exception('Payment processing failed: ' . $e->getMessage());
        }
    }
}
