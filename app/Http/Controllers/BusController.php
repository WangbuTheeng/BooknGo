<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Operator;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusController extends Controller
{
    /**
     * Display a listing of buses
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $buses = Bus::with(['operator.user'])->paginate(15);
        } elseif ($user->isOperator()) {
            $buses = $user->operator->buses()->with(['operator.user'])->paginate(15);
        } else {
            abort(403, 'Unauthorized access');
        }

        return view('buses.index', compact('buses'));
    }

    /**
     * Show the form for creating a new bus
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->isOperator() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        return view('buses.create');
    }

    /**
     * Store a newly created bus in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isOperator() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'registration_number' => 'required|string|max:50|unique:buses',
            'name' => 'nullable|string|max:100',
            'type' => 'required|in:AC,Deluxe,Normal,Sleeper',
            'total_seats' => 'required|integer|min:1|max:100',
            'features' => 'nullable|array',
        ]);

        $operatorId = $user->isAdmin() ? $request->operator_id : $user->operator->id;

        $bus = Bus::create([
            'operator_id' => $operatorId,
            'registration_number' => $request->registration_number,
            'name' => $request->name,
            'type' => $request->type,
            'total_seats' => $request->total_seats,
            'features' => $request->features ?? [],
        ]);

        // Auto-generate seats for the bus
        $this->generateSeats($bus, $request->total_seats);

        return redirect()->route('buses.index')
                        ->with('success', 'Bus created successfully with ' . $request->total_seats . ' seats.');
    }

    /**
     * Display the specified bus
     */
    public function show(Bus $bus)
    {
        $this->authorize('view', $bus);

        $bus->load(['operator.user', 'seats']);

        return view('buses.show', compact('bus'));
    }

    /**
     * Show the form for editing the specified bus
     */
    public function edit(Bus $bus)
    {
        $this->authorize('update', $bus);

        return view('buses.edit', compact('bus'));
    }

    /**
     * Update the specified bus in storage
     */
    public function update(Request $request, Bus $bus)
    {
        $this->authorize('update', $bus);

        $request->validate([
            'registration_number' => 'required|string|max:50|unique:buses,registration_number,' . $bus->id,
            'name' => 'nullable|string|max:100',
            'type' => 'required|in:AC,Deluxe,Normal,Sleeper',
            'total_seats' => 'required|integer|min:1|max:100',
            'features' => 'nullable|array',
        ]);

        $oldSeatCount = $bus->total_seats;
        $newSeatCount = $request->total_seats;

        $bus->update([
            'registration_number' => $request->registration_number,
            'name' => $request->name,
            'type' => $request->type,
            'total_seats' => $request->total_seats,
            'features' => $request->features ?? [],
        ]);

        // Adjust seats if total_seats changed
        if ($oldSeatCount != $newSeatCount) {
            $this->adjustSeats($bus, $oldSeatCount, $newSeatCount);
        }

        return redirect()->route('buses.index')
                        ->with('success', 'Bus updated successfully.');
    }

    /**
     * Remove the specified bus from storage
     */
    public function destroy(Bus $bus)
    {
        $this->authorize('delete', $bus);

        // Check if bus has any trips
        if ($bus->trips()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete bus with existing trips.']);
        }

        $bus->delete();

        return redirect()->route('buses.index')
                        ->with('success', 'Bus deleted successfully.');
    }

    /**
     * Generate seats for a bus
     */
    private function generateSeats(Bus $bus, int $totalSeats)
    {
        for ($i = 1; $i <= $totalSeats; $i++) {
            Seat::create([
                'bus_id' => $bus->id,
                'seat_number' => (string) $i,
                'position' => $this->calculateSeatPosition($i, $totalSeats),
            ]);
        }
    }

    /**
     * Adjust seats when total_seats changes
     */
    private function adjustSeats(Bus $bus, int $oldCount, int $newCount)
    {
        if ($newCount > $oldCount) {
            // Add new seats
            for ($i = $oldCount + 1; $i <= $newCount; $i++) {
                Seat::create([
                    'bus_id' => $bus->id,
                    'seat_number' => (string) $i,
                    'position' => $this->calculateSeatPosition($i, $newCount),
                ]);
            }
        } elseif ($newCount < $oldCount) {
            // Remove excess seats (only if they have no bookings)
            $seatsToRemove = $bus->seats()
                                ->where('seat_number', '>', $newCount)
                                ->whereDoesntHave('bookingSeats')
                                ->get();

            foreach ($seatsToRemove as $seat) {
                $seat->delete();
            }
        }
    }

    /**
     * Calculate seat position based on seat number and total seats
     */
    private function calculateSeatPosition(int $seatNumber, int $totalSeats): string
    {
        // Simple logic: assume 4 seats per row (2+2 configuration)
        $row = ceil($seatNumber / 4);
        $position = ($seatNumber - 1) % 4;

        $positions = ['window-left', 'aisle-left', 'aisle-right', 'window-right'];

        return "Row {$row} - {$positions[$position]}";
    }
}
