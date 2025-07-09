<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Trip;
use App\Models\User;
use App\Events\SeatStatusUpdated;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingService
{
    /**
     * Create a new booking.
     *
     * @param Trip $trip
     * @param User $user
     * @param array $data
     * @return Booking
     * @throws Exception
     */
    public function createBooking(Trip $trip, User $user, array $data): Booking
    {
        DB::beginTransaction();

        try {
            $totalAmount = count($data['seat_ids']) * $trip->price;

            $booking = Booking::create([
                'user_id' => $user->id,
                'trip_id' => $trip->id,
                'booking_reference' => Booking::generateReference(),
                'passenger_name' => $data['passenger_name'] ?? 'Temporary Booking',
                'passenger_phone' => $data['passenger_phone'] ?? 'TBD',
                'passenger_email' => $data['passenger_email'] ?? null,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'expires_at' => now()->addHours(2),
            ]);

            foreach ($data['seat_ids'] as $seatId) {
                $booking->bookingSeats()->create(['seat_id' => $seatId]);
                broadcast(new SeatStatusUpdated($trip->id, $seatId, 'booked'))->toOthers();
            }

            DB::commit();

            return $booking;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create booking: ' . $e->getMessage());
        }
    }
}
