<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()->operator->bookings()->with(['user', 'trip.bus', 'trip.route.fromCity', 'trip.route.toCity'])->latest()->paginate(10);

        return view('operator.bookings.index', compact('bookings'));
    }

    public function show(string $id)
    {
        $booking = auth()->user()->operator->bookings()->with(['user', 'trip.bus', 'trip.route.fromCity', 'trip.route.toCity', 'seats'])->findOrFail($id);

        return view('operator.bookings.show', compact('booking'));
    }

    public function generateManifest(string $tripId)
    {
        $trip = auth()->user()->operator->trips()->with(['bookings.user', 'bookings.seats'])->findOrFail($tripId);
        $bookings = $trip->bookings;

        return view('operator.trips.manifest', compact('trip', 'bookings'));
    }
}
