<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trip;
use App\Models\Seat;
use App\Models\BookingSeat;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $bookings = Booking::with(['user', 'trip.route.fromCity', 'trip.route.toCity', 'trip.bus'])
                              ->orderBy('created_at', 'desc')
                              ->paginate(15);
        } else {
            $bookings = $user->bookings()
                            ->with(['trip.route.fromCity', 'trip.route.toCity', 'trip.bus'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);
        }

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking
     */
    public function create(Trip $trip)
    {
        // Check if trip is available for booking
        if (!$trip->isBookable()) {
            return redirect()->back()->withErrors(['error' => 'This trip is not available for booking.']);
        }

        $trip->load(['bus.operator.user', 'route.fromCity', 'route.toCity']);

        return view('trips.book', compact('trip'));
    }

    /**
     * Store a newly created booking in storage
     */
    public function store(Request $request, Trip $trip)
    {
        $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:20',
            'passenger_email' => 'nullable|email|max:255',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
        ]);

        // Check if trip is still available
        if ($trip->status !== 'active' || $trip->departure_datetime <= now()) {
            return back()->withErrors(['error' => 'This trip is no longer available for booking.']);
        }

        // Verify seat availability and ownership
        $seats = Seat::whereIn('id', $request->seat_ids)
                    ->where('bus_id', $trip->bus_id)
                    ->get();

        if ($seats->count() !== count($request->seat_ids)) {
            return back()->withErrors(['error' => 'Invalid seat selection.']);
        }

        // Check if any selected seats are already booked
        foreach ($seats as $seat) {
            if (!$seat->isAvailableForTrip($trip->id)) {
                return back()->withErrors(['error' => "Seat {$seat->seat_number} is no longer available."]);
            }
        }

        DB::beginTransaction();

        try {
            // Calculate total amount
            $totalAmount = $trip->price * $seats->count();

            // Create booking with expiry time (15 minutes from now)
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'trip_id' => $trip->id,
                'booking_reference' => $this->generateBookingReference(),
                'passenger_name' => $request->passenger_name,
                'passenger_phone' => $request->passenger_phone,
                'passenger_email' => $request->passenger_email,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'expires_at' => now()->addMinutes(15),
            ]);

            // Create booking seats
            foreach ($seats as $seat) {
                BookingSeat::create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seat->id,
                    'seat_number' => $seat->seat_number,
                ]);
            }

            DB::commit();

            // For testing purposes, return JSON if request expects JSON
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'booking' => $booking,
                    'message' => 'Booking created successfully.'
                ]);
            }

            return redirect()->route('bookings.payment', $booking)
                           ->with('success', 'Booking created successfully. Please proceed with payment.');

        } catch (\Exception $e) {
            DB::rollback();

            // For debugging - show actual error in test environment
            if (app()->environment('testing')) {
                throw $e;
            }

            return back()->withErrors(['error' => 'Failed to create booking. Please try again.']);
        }
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['trip.route.fromCity', 'trip.route.toCity', 'trip.bus', 'bookingSeats.seat', 'payments']);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Show booking confirmation page
     */
    public function confirmation(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load([
            'trip.route.fromCity',
            'trip.route.toCity',
            'trip.bus.operator.user',
            'bookingSeats.seat'
        ]);

        return view('bookings.confirmation', compact('booking'));
    }

    /**
     * Show payment page for booking
     */
    public function payment(Booking $booking)
    {
        $this->authorize('view', $booking);

        // Check if booking has expired
        if ($booking->isExpired()) {
            return redirect()->route('bookings.show', $booking)
                           ->withErrors(['error' => 'This booking has expired and cannot be paid for.']);
        }

        // Check if payment is already completed
        if ($booking->payment_status !== 'pending') {
            return redirect()->route('bookings.show', $booking)
                           ->withErrors(['error' => 'Payment is not required for this booking.']);
        }

        return view('bookings.payment', compact('booking'));
    }

    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking)
    {
        $this->authorize('update', $booking);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['error' => 'This booking cannot be cancelled.']);
        }

        // Check cancellation policy (e.g., 24 hours before departure)
        $hoursBeforeDeparture = now()->diffInHours($booking->trip->departure_datetime, false);
        if ($hoursBeforeDeparture < 24) {
            return back()->withErrors(['error' => 'Bookings cannot be cancelled less than 24 hours before departure.']);
        }

        $booking->update(['status' => 'cancelled']);

        // TODO: Process refund if payment was made
        // TODO: Send cancellation notification

        return redirect()->route('bookings.index')
                        ->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Delete a booking (user cancellation)
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('view', $booking);

        if (!$booking->canBeDeleted()) {
            return back()->withErrors(['error' => 'This booking cannot be deleted. It may have expired or payment is already processed.']);
        }

        DB::beginTransaction();

        try {
            // Delete associated booking seats
            $booking->bookingSeats()->delete();

            // Delete associated payments
            $booking->payments()->delete();

            // Delete the booking
            $booking->delete();

            DB::commit();

            return redirect()->route('bookings.index')
                           ->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to cancel booking. Please try again.']);
        }
    }

    /**
     * Generate a unique booking reference
     */
    private function generateBookingReference(): string
    {
        do {
            $reference = 'BNG' . strtoupper(Str::random(6));
        } while (Booking::where('booking_reference', $reference)->exists());

        return $reference;
    }
}
