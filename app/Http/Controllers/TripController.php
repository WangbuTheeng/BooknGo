<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Bus;
use App\Models\Route;
use App\Models\City;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TripController extends Controller
{
    /**
     * Display a listing of trips
     */
    public function index(Request $request)
    {
        $query = Trip::with(['bus.operator.user', 'route.fromCity', 'route.toCity']);

        $user = Auth::user();

        // Filter by operator if user is an operator
        if ($user->isOperator()) {
            $query->whereHas('bus', function ($q) use ($user) {
                $q->where('operator_id', $user->operator->id);
            });
        }

        // Apply filters
        if ($request->filled('from_city')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('from_city_id', $request->from_city);
            });
        }

        if ($request->filled('to_city')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('to_city_id', $request->to_city);
            });
        }

        if ($request->filled('departure_date')) {
            $query->whereDate('departure_datetime', $request->departure_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $trips = $query->orderBy('departure_datetime', 'desc')->paginate(15);

        // Get cities for filter dropdown
        $cities = City::orderBy('name')->get();

        return view('trips.index', compact('trips', 'cities'));
    }

    /**
     * Show the form for creating a new trip
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user->isOperator() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $buses = $user->isAdmin()
            ? Bus::with('operator.user')->get()
            : $user->operator->buses;

        $routes = Route::with(['fromCity', 'toCity'])->get();

        return view('trips.create', compact('buses', 'routes'));
    }

    /**
     * Store a newly created trip in storage
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isOperator() && !$user->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:routes,id',
            'departure_datetime' => 'required|date|after:now',
            'arrival_time' => 'nullable|date|after:departure_datetime',
            'price' => 'required|numeric|min:0',
            'is_festival_fare' => 'boolean',
        ]);

        // Verify bus ownership for operators
        if ($user->isOperator()) {
            $bus = Bus::findOrFail($request->bus_id);
            if ($bus->operator_id !== $user->operator->id) {
                abort(403, 'You can only create trips for your own buses');
            }
        }

        // Apply festival pricing if enabled
        $price = $request->price;
        if ($request->is_festival_fare || SystemSetting::isFestivalModeEnabled()) {
            $multiplier = SystemSetting::getFestivalFareMultiplier();
            $price = $price * $multiplier;
        }

        $trip = Trip::create([
            'bus_id' => $request->bus_id,
            'route_id' => $request->route_id,
            'departure_datetime' => $request->departure_datetime,
            'arrival_time' => $request->arrival_time,
            'price' => $price,
            'is_festival_fare' => $request->is_festival_fare ?? SystemSetting::isFestivalModeEnabled(),
            'status' => 'active',
        ]);

        return redirect()->route('trips.index')
                        ->with('success', 'Trip created successfully.');
    }

    /**
     * Display the specified trip
     */
    public function show(Trip $trip)
    {
        $trip->load(['bus.operator.user', 'route.fromCity', 'route.toCity', 'bookings.user']);

        return view('trips.show', compact('trip'));
    }

    /**
     * Show the form for editing the specified trip
     */
    public function edit(Trip $trip)
    {
        $this->authorize('update', $trip);

        $user = Auth::user();

        $buses = $user->isAdmin()
            ? Bus::with('operator.user')->get()
            : $user->operator->buses;

        $routes = Route::with(['fromCity', 'toCity'])->get();

        return view('trips.edit', compact('trip', 'buses', 'routes'));
    }

    /**
     * Update the specified trip in storage
     */
    public function update(Request $request, Trip $trip)
    {
        $this->authorize('update', $trip);

        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:routes,id',
            'departure_datetime' => 'required|date',
            'arrival_time' => 'nullable|date|after:departure_datetime',
            'price' => 'required|numeric|min:0',
            'is_festival_fare' => 'boolean',
            'status' => 'required|in:active,cancelled',
        ]);

        // Check if trip has bookings before allowing major changes
        if ($trip->bookings()->exists()) {
            // Only allow limited changes if bookings exist
            $trip->update([
                'departure_datetime' => $request->departure_datetime,
                'arrival_time' => $request->arrival_time,
                'status' => $request->status,
            ]);
        } else {
            // Apply festival pricing if enabled
            $price = $request->price;
            if ($request->is_festival_fare || SystemSetting::isFestivalModeEnabled()) {
                $multiplier = SystemSetting::getFestivalFareMultiplier();
                $price = $price * $multiplier;
            }

            $trip->update([
                'bus_id' => $request->bus_id,
                'route_id' => $request->route_id,
                'departure_datetime' => $request->departure_datetime,
                'arrival_time' => $request->arrival_time,
                'price' => $price,
                'is_festival_fare' => $request->is_festival_fare ?? SystemSetting::isFestivalModeEnabled(),
                'status' => $request->status,
            ]);
        }

        return redirect()->route('trips.index')
                        ->with('success', 'Trip updated successfully.');
    }

    /**
     * Remove the specified trip from storage
     */
    public function destroy(Trip $trip)
    {
        $this->authorize('delete', $trip);

        // Check if trip has bookings
        if ($trip->bookings()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete trip with existing bookings.']);
        }

        $trip->delete();

        return redirect()->route('trips.index')
                        ->with('success', 'Trip deleted successfully.');
    }

    /**
     * Search trips for public booking
     */
    public function search(Request $request)
    {
        $request->validate([
            'from_city_id' => 'required|exists:cities,id',
            'to_city_id' => 'required|exists:cities,id|different:from_city_id',
            'departure_date' => 'required|date|after_or_equal:today',
        ]);

        $trips = Trip::with(['bus.operator.user', 'route.fromCity', 'route.toCity'])
            ->whereHas('route', function ($q) use ($request) {
                $q->where('from_city_id', $request->from_city_id)
                  ->where('to_city_id', $request->to_city_id);
            })
            ->whereDate('departure_datetime', $request->departure_date)
            ->where('status', 'active')
            ->where('departure_datetime', '>', now())
            ->orderBy('departure_datetime')
            ->get();

        // Add available seats count to each trip
        $trips->each(function ($trip) {
            $trip->available_seats = $trip->available_seats_count;
        });

        $fromCity = City::find($request->from_city_id);
        $toCity = City::find($request->to_city_id);
        $departureDate = $request->departure_date;

        return view('trips.search-results', compact('trips', 'fromCity', 'toCity', 'departureDate'));
    }

    /**
     * Show seat selection page for a trip
     */
    public function selectSeats(Trip $trip)
    {
        // Check if trip is available for booking
        if (!$trip->isBookable()) {
            return redirect()->back()->withErrors(['error' => 'This trip is not available for booking.']);
        }

        // Check if user is authenticated - redirect to login if not
        if (!Auth::check()) {
            $currentUrl = request()->fullUrl();
            return redirect()->route('login', ['redirect' => urlencode($currentUrl)])
                           ->with('info', 'Please login to view available seats and make a booking.');
        }

        $trip->load(['bus.operator.user', 'route.fromCity', 'route.toCity', 'bus.seats']);

        return view('trips.seat-selection', compact('trip'));
    }

    /**
     * Get seat availability for a trip
     */
    public function seatAvailability(Trip $trip)
    {
        $seats = $trip->bus->seats()->orderByRaw('CAST(seat_number AS UNSIGNED)')->get()->map(function ($seat) use ($trip) {
            return [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'position' => $seat->position,
                'available' => $seat->isAvailableForTrip($trip->id),
            ];
        });

        return response()->json([
            'trip_id' => $trip->id,
            'total_seats' => $trip->bus->total_seats,
            'available_count' => $trip->available_seats_count,
            'seats' => $seats,
        ]);
    }

    /**
     * Cancel a trip
     */
    public function cancel(Trip $trip)
    {
        $this->authorize('update', $trip);

        $trip->update(['status' => 'cancelled']);

        // TODO: Send notifications to all booked passengers
        // TODO: Process refunds for existing bookings

        return redirect()->route('trips.index')
                        ->with('success', 'Trip cancelled successfully.');
    }
}
