<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    public function index(string $tripId)
    {
        $trip = auth()->user()->operator->trips()->with('bus.seats')->findOrFail($tripId);
        $bus = $trip->bus;
        $seats = $bus->seats()->get()->keyBy('id');
        $bookedSeats = $trip->bookings()->with('seats')->get()->flatMap->seats->pluck('id')->toArray();

        return view('operator.seats.index', compact('trip', 'bus', 'seats', 'bookedSeats'));
    }

    public function block(Request $request, string $tripId, string $seatId)
    {
        $trip = auth()->user()->operator->trips()->findOrFail($tripId);
        $seat = $trip->bus->seats()->findOrFail($seatId);

        $seat->update(['is_available_for_booking' => false]);

        return back()->with('success', 'Seat blocked successfully.');
    }

    public function unblock(Request $request, string $tripId, string $seatId)
    {
        $trip = auth()->user()->operator->trips()->findOrFail($tripId);
        $seat = $trip->bus->seats()->findOrFail($seatId);

        $seat->update(['is_available_for_booking' => true]);

        return back()->with('success', 'Seat unblocked successfully.');
    }

    public function edit(string $tripId, string $seatId)
    {
        $trip = auth()->user()->operator->trips()->findOrFail($tripId);
        $seat = $trip->bus->seats()->findOrFail($seatId);

        return view('operator.seats.edit', compact('trip', 'seat'));
    }

    public function update(Request $request, string $tripId, string $seatId)
    {
        $trip = auth()->user()->operator->trips()->findOrFail($tripId);
        $bus = $trip->bus;
        $seat = $bus->seats()->findOrFail($seatId);

        $request->validate([
            'seat_number' => 'required|string|max:10',
            'position' => 'nullable|string|max:50',
        ]);

        if ($bus->seats()->where('seat_number', $request->seat_number)->where('id', '!=', $seat->id)->exists()) {
            return back()->withErrors(['seat_number' => 'Seat number already exists for this bus.']);
        }

        $seat->update([
            'seat_number' => $request->seat_number,
            'position' => $request->position,
        ]);

        return redirect()->route('operator.trips.seats.index', $trip)
                        ->with('success', 'Seat updated successfully.');
    }
}
