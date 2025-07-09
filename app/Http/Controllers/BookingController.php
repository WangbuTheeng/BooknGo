<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trip;
use App\Models\Seat;
use App\Models\Payment;
use App\Services\BookingService;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdatePassengerRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\SeatStatusUpdated;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display a listing of bookings
     */
    public function index()
    {
        $user = Auth::user();

        $query = $user->isAdmin()
            ? Booking::query()
            : $user->bookings();

        $bookings = $query->with(['user', 'trip.route.fromCity', 'trip.route.toCity', 'trip.bus'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking
     */
    public function create(Trip $trip)
    {
        if (!$trip->isBookable()) {
            return redirect()->back()->withErrors(['error' => 'This trip is not available for booking.']);
        }

        if (!Auth::check()) {
            return redirect()->route('trips.select-seats', $trip)
                           ->with('info', 'Please select your seats first, then log in to complete your booking.');
        }

        $trip->load(['bus.operator.user', 'route.fromCity', 'route.toCity']);

        return view('trips.book', compact('trip'));
    }

    /**
     * Store a newly created booking in storage
     */
    public function store(StoreBookingRequest $request, Trip $trip)
    {

        if ($trip->status !== 'active' || $trip->departure_datetime <= now()) {
            return back()->withErrors(['error' => 'This trip is no longer available for booking.']);
        }

        $seats = Seat::whereIn('id', $request->seat_ids)
                    ->where('bus_id', $trip->bus_id)
                    ->get();

        if ($seats->count() !== count($request->seat_ids)) {
            return back()->withErrors(['error' => 'Invalid seat selection.']);
        }

        $unavailableSeats = DB::table('booking_seats')
            ->join('bookings', 'booking_seats.booking_id', '=', 'bookings.id')
            ->where('bookings.trip_id', $trip->id)
            ->whereIn('booking_seats.seat_id', $request->seat_ids)
            ->where(function ($query) {
                $query->where('bookings.status', 'booked')
                      ->orWhere(function ($q) {
                          $q->where('bookings.status', 'pending')
                            ->where('bookings.expires_at', '>', now());
                      });
            })
            ->pluck('booking_seats.seat_id')
            ->toArray();

        if (!empty($unavailableSeats)) {
            $unavailableSeatNumbers = $seats->whereIn('id', $unavailableSeats)->pluck('seat_number')->join(', ');
            return back()->withErrors(['error' => "The following seats are no longer available: {$unavailableSeatNumbers}"]);
        }

        try {
            $booking = $this->bookingService->createBooking($trip, Auth::user(), $request->all());

            $redirectRoute = $request->action === 'hold' ? 'bookings.show' : 'bookings.payment';
            $message = $request->action === 'hold'
                ? 'Seats reserved for 2 hours! Complete your booking details.'
                : 'Booking created successfully. Please proceed with payment.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'booking' => $booking,
                    'message' => $message,
                    'redirect' => route($redirectRoute, $booking)
                ]);
            }

            return redirect()->route($redirectRoute, $booking)->with('success', $message);

        } catch (\Exception $e) {
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

        if ($booking->isExpired()) {
            return redirect()->route('bookings.show', $booking)
                           ->withErrors(['error' => 'This booking has expired and cannot be paid for.']);
        }

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

        $hoursBeforeDeparture = now()->diffInHours($booking->trip->departure_datetime, false);
        if ($hoursBeforeDeparture < 24) {
            return back()->withErrors(['error' => 'Bookings cannot be cancelled less than 24 hours before departure.']);
        }

        $booking->update(['status' => 'cancelled']);

        foreach ($booking->bookingSeats as $bookingSeat) {
            broadcast(new SeatStatusUpdated($booking->trip_id, $bookingSeat->seat_id, 'available'))->toOthers();
        }

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
            foreach ($booking->bookingSeats as $bookingSeat) {
                broadcast(new SeatStatusUpdated($booking->trip_id, $bookingSeat->seat_id, 'available'))->toOthers();
            }
            $booking->bookingSeats()->delete();
            $booking->payments()->delete();
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
     * Update passenger details for a booking
     */
    public function updatePassenger(UpdatePassengerRequest $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        if ($booking->status !== 'pending' || $booking->isExpired()) {
            return back()->withErrors(['error' => 'This booking cannot be modified.']);
        }

        $booking->update($request->only(['passenger_name', 'passenger_phone', 'passenger_email']));

        return redirect()->route('bookings.show', $booking)
                        ->with('success', 'Passenger details updated successfully.');
    }

    /**
     * Get user's recent bookings for ticket history
     */
    public function ticketHistory(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please log in to view your ticket history.'], 401);
        }

        $user = Auth::user();
        $limit = $request->get('limit', 5);
        
        $recentBookings = $user->bookings()
            ->with([
                'trip.route.fromCity',
                'trip.route.toCity',
                'trip.bus.operator',
                'bookingSeats.seat'
            ])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'bookings' => $recentBookings->map(fn($booking) => [
                    'id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'route' => $booking->trip->route->fromCity->name . ' → ' . $booking->trip->route->toCity->name,
                    'departure_date' => $booking->trip->departure_datetime->format('M d, Y'),
                    'departure_time' => $booking->trip->departure_datetime->format('H:i'),
                    'passenger_name' => $booking->passenger_name,
                    'seats' => $booking->bookingSeats->pluck('seat.seat_number')->join(', '),
                    'total_amount' => $booking->total_amount,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'created_at' => $booking->created_at->format('M d, Y H:i'),
                    'url' => route('bookings.show', $booking)
                ])
            ]);
        }

        return view('bookings.ticket-history', compact('recentBookings'));
    }
}
