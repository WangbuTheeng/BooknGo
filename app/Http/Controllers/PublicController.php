<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\Bus;
use App\Models\Trip;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Display all operators
     */
    public function operators()
    {
        $operators = Operator::with(['user', 'buses'])
            ->where('verified', true)
            ->whereHas('user', function($query) {
                $query->where('status', 'active');
            })
            ->paginate(12);

        return view('public.operators.index', compact('operators'));
    }

    /**
     * Display operator details
     */
    public function showOperator(Operator $operator)
    {
        // Check if operator is verified and user is active
        if (!$operator->verified || $operator->user->status !== 'active') {
            abort(404, 'Operator not found');
        }

        $operator->load(['user', 'buses.trips' => function($query) {
            $query->with(['route.fromCity', 'route.toCity'])
                  ->where('departure_datetime', '>=', now())
                  ->where('status', 'active')
                  ->orderBy('departure_datetime');
        }]);

        return view('public.operators.show', compact('operator'));
    }

    /**
     * Display buses for a specific operator
     */
    public function operatorBuses(Operator $operator)
    {
        // Check if operator is verified and user is active
        if (!$operator->verified || $operator->user->status !== 'active') {
            abort(404, 'Operator not found');
        }

        $buses = $operator->buses()
            ->with(['trips' => function($query) {
                $query->with(['route.fromCity', 'route.toCity'])
                      ->where('departure_datetime', '>=', now())
                      ->orderBy('departure_datetime');
            }])
            ->paginate(12);

        return view('public.operators.buses', compact('operator', 'buses'));
    }

    /**
     * Display trips for a specific bus
     */
    public function busTrips(Bus $bus)
    {
        // Check if operator is verified and user is active
        if (!$bus->operator->verified || $bus->operator->user->status !== 'active') {
            abort(404, 'Bus not found');
        }

        $trips = $bus->trips()
            ->with(['route.fromCity', 'route.toCity'])
            ->where('departure_datetime', '>=', now()->subDay()) // Show trips from yesterday onwards
            ->orderBy('departure_datetime')
            ->paginate(15);

        return view('public.buses.trips', compact('bus', 'trips'));
    }
}
