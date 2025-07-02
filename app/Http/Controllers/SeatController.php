<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeatController extends Controller
{
    /**
     * Display a listing of seats for a specific bus
     */
    public function index(Bus $bus)
    {
        $this->authorize('view', $bus);

        $bus->load(['seats', 'operator.user']);

        return view('buses.seats.index', compact('bus'));
    }

    /**
     * Show the form for creating a new seat
     */
    public function create(Bus $bus)
    {
        $this->authorize('update', $bus);

        return view('buses.seats.create', compact('bus'));
    }

    /**
     * Store a newly created seat in storage
     */
    public function store(Request $request, Bus $bus)
    {
        $this->authorize('update', $bus);

        // Handle bulk creation
        if ($request->has('bulk_create')) {
            return $this->storeBulkSeats($request, $bus);
        }

        $request->validate([
            'seat_number' => 'required|string|max:10',
            'position' => 'nullable|string|max:50',
        ]);

        // Check if seat number already exists for this bus
        if ($bus->seats()->where('seat_number', $request->seat_number)->exists()) {
            return back()->withErrors(['seat_number' => 'Seat number already exists for this bus.']);
        }

        Seat::create([
            'bus_id' => $bus->id,
            'seat_number' => $request->seat_number,
            'position' => $request->position,
        ]);

        return redirect()->route('buses.seats.index', $bus)
                        ->with('success', 'Seat created successfully.');
    }

    /**
     * Store multiple seats at once
     */
    private function storeBulkSeats(Request $request, Bus $bus)
    {
        $request->validate([
            'start_number' => 'required|integer|min:1',
            'end_number' => 'required|integer|min:1|gte:start_number',
        ]);

        $startNumber = $request->start_number;
        $endNumber = $request->end_number;
        $createdSeats = 0;
        $skippedSeats = [];

        for ($i = $startNumber; $i <= $endNumber; $i++) {
            // Check if seat number already exists
            if (!$bus->seats()->where('seat_number', $i)->exists()) {
                Seat::create([
                    'bus_id' => $bus->id,
                    'seat_number' => $i,
                    'position' => null,
                ]);
                $createdSeats++;
            } else {
                $skippedSeats[] = $i;
            }
        }

        $message = "Created {$createdSeats} seats successfully.";
        if (!empty($skippedSeats)) {
            $message .= " Skipped existing seats: " . implode(', ', $skippedSeats);
        }

        return redirect()->route('buses.seats.index', $bus)
                        ->with('success', $message);
    }

    /**
     * Display the specified seat
     */
    public function show(Bus $bus, Seat $seat)
    {
        $this->authorize('view', $bus);

        if ($seat->bus_id !== $bus->id) {
            abort(404);
        }

        $seat->load(['bookingSeats.booking']);

        return view('buses.seats.show', compact('bus', 'seat'));
    }

    /**
     * Show the form for editing the specified seat
     */
    public function edit(Bus $bus, Seat $seat)
    {
        $this->authorize('update', $bus);

        if ($seat->bus_id !== $bus->id) {
            abort(404);
        }

        return view('buses.seats.edit', compact('bus', 'seat'));
    }

    /**
     * Update the specified seat in storage
     */
    public function update(Request $request, Bus $bus, Seat $seat)
    {
        $this->authorize('update', $bus);

        if ($seat->bus_id !== $bus->id) {
            abort(404);
        }

        $request->validate([
            'seat_number' => 'required|string|max:10',
            'position' => 'nullable|string|max:50',
        ]);

        // Check if seat number already exists for this bus (excluding current seat)
        if ($bus->seats()->where('seat_number', $request->seat_number)->where('id', '!=', $seat->id)->exists()) {
            return back()->withErrors(['seat_number' => 'Seat number already exists for this bus.']);
        }

        $seat->update([
            'seat_number' => $request->seat_number,
            'position' => $request->position,
        ]);

        return redirect()->route('buses.seats.index', $bus)
                        ->with('success', 'Seat updated successfully.');
    }

    /**
     * Remove the specified seat from storage
     */
    public function destroy(Bus $bus, Seat $seat)
    {
        $this->authorize('update', $bus);

        if ($seat->bus_id !== $bus->id) {
            abort(404);
        }

        // Check if seat has any bookings
        if ($seat->bookingSeats()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete seat with existing bookings.']);
        }

        $seat->delete();

        return redirect()->route('buses.seats.index', $bus)
                        ->with('success', 'Seat deleted successfully.');
    }

    /**
     * Get seat availability for a specific bus
     */
    public function availability(Bus $bus)
    {
        $this->authorize('view', $bus);

        $seats = $bus->seats()->get()->map(function ($seat) {
            return [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'position' => $seat->position,
                'total_bookings' => $seat->bookingSeats()->count(),
            ];
        });

        return response()->json([
            'bus_id' => $bus->id,
            'total_seats' => $bus->total_seats,
            'seats' => $seats,
        ]);
    }
}
