<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Payment;

class TestPaymentConfirmation extends Command
{
    protected $signature = 'test:payment-confirmation';
    protected $description = 'Test payment confirmation functionality';

    public function handle()
    {
        $this->info('Testing payment confirmation functionality...');

        // Find a pending booking from operator (counter booking)
        $booking = Booking::with('payments')
                         ->where('payment_status', 'pending')
                         ->where('booking_type', 'counter')
                         ->first();
                         
        if (!$booking) {
            $this->info('No pending operator bookings found. Looking for any pending booking...');
            $booking = Booking::with('payments')->where('payment_status', 'pending')->first();
        }
        
        if (!$booking) {
            $this->info('No pending bookings found.');
            return;
        }

        $this->info("Found booking: {$booking->booking_reference}");
        $this->info("Current payment status: {$booking->payment_status}");
        $this->info("Payments count: {$booking->payments->count()}");
        
        if ($booking->payments->first()) {
            $payment = $booking->payments->first();
            $this->info("First payment status: {$payment->payment_status}");
            $this->info("First payment method: {$payment->method}");
        } else {
            $this->info('No payment records found for this booking!');
            $this->info('Attempting to create a payment record...');
            
            try {
                $payment = \App\Models\Payment::create([
                    'booking_id' => $booking->id,
                    'method' => 'Cash',
                    'amount' => $booking->total_amount,
                    'transaction_id' => 'TXN' . strtoupper(\Illuminate\Support\Str::random(8)),
                    'payment_status' => 'pending',
                    'processed_by_user_id' => $booking->user_id,
                ]);
                
                $this->info("Created payment record with ID: {$payment->id}");
                $booking->refresh();
                $booking->load('payments');
                
            } catch (\Exception $e) {
                $this->error("Failed to create payment record: {$e->getMessage()}");
                return;
            }
        }

        // Test updating the payment status
        $this->info('Testing payment status update...');
        
        try {
            $updated = $booking->payments()->update([
                'payment_status' => 'completed',
                'confirmed_at' => now(),
            ]);
            
            $booking->update(['payment_status' => 'completed']);
            
            $this->info("Updated {$updated} payment(s)");
            
            // Refresh and check
            $booking->refresh();
            $booking->load('payments');
            
            $this->info("After update:");
            $this->info("Booking payment status: {$booking->payment_status}");
            if ($booking->payments->first()) {
                $this->info("First payment status: {$booking->payments->first()->payment_status}");
            }
            
        } catch (\Exception $e) {
            $this->error("Error updating payment: {$e->getMessage()}");
        }
    }
}
