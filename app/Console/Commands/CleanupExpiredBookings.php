<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Events\SeatStatusUpdated;

class CleanupExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired bookings that have not been paid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of expired bookings...');

        // Find expired bookings that are still pending payment
        $expiredBookings = Booking::where('expires_at', '<', now())
            ->where('payment_status', 'pending')
            ->where('status', 'pending')
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No expired bookings found.');
            return 0;
        }

        $this->info("Found {$expiredBookings->count()} expired bookings to clean up.");

        $cleanedCount = 0;

        foreach ($expiredBookings as $booking) {
            DB::beginTransaction();

            try {
                // Dispatch event for each seat
                foreach ($booking->bookingSeats as $bookingSeat) {
                    broadcast(new SeatStatusUpdated($booking->trip_id, $bookingSeat->seat_id, 'available'))->toOthers();
                }

                // Delete associated booking seats
                $booking->bookingSeats()->delete();

                // Delete associated payments (if any)
                $booking->payments()->delete();

                // Delete the booking
                $booking->delete();

                DB::commit();
                $cleanedCount++;

                $this->line("Cleaned up booking: {$booking->booking_reference}");

            } catch (\Exception $e) {
                DB::rollback();
                $this->error("Failed to clean up booking {$booking->booking_reference}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully cleaned up {$cleanedCount} expired bookings.");

        return 0;
    }
}
