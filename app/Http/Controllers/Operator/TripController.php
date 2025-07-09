<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trips = auth()->user()->operator->trips()->with(['bus', 'route.fromCity', 'route.toCity'])->latest()->paginate(10);

        return view('operator.trips.index', compact('trips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $buses = auth()->user()->operator->buses;
        $routes = auth()->user()->operator->routes()->with(['fromCity', 'toCity'])->get();

        return view('operator.trips.create', compact('buses', 'routes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_id' => [
                'required',
                'exists:routes,id',
                function ($attribute, $value, $fail) {
                    $route = \App\Models\Route::find($value);
                    if ($route->operator_id !== auth()->user()->operator->id) {
                        $fail('The selected route does not belong to this operator.');
                    }
                },
            ],
            'departure_datetime' => 'required|date',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $bus = auth()->user()->operator->buses()->findOrFail($request->bus_id);

        $bus->trips()->create($request->all());

        return redirect()->route('operator.trips.index')->with('success', 'Trip created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $trip = auth()->user()->operator->trips()->with(['bus', 'route.fromCity', 'route.toCity'])->findOrFail($id);

        return view('operator.trips.show', compact('trip'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $trip = auth()->user()->operator->trips()->findOrFail($id);
        $buses = auth()->user()->operator->buses;
        $routes = auth()->user()->operator->routes()->with(['fromCity', 'toCity'])->get();

        return view('operator.trips.edit', compact('trip', 'buses', 'routes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $trip = auth()->user()->operator->trips()->findOrFail($id);

        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_id' => [
                'required',
                'exists:routes,id',
                function ($attribute, $value, $fail) {
                    $route = \App\Models\Route::find($value);
                    if ($route->operator_id !== auth()->user()->operator->id) {
                        $fail('The selected route does not belong to this operator.');
                    }
                },
            ],
            'departure_datetime' => 'required|date',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $bus = auth()->user()->operator->buses()->findOrFail($request->bus_id);

        $trip->update($request->all());

        return redirect()->route('operator.trips.index')->with('success', 'Trip updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $trip = auth()->user()->operator->trips()->findOrFail($id);
        $trip->delete();

        return redirect()->route('operator.trips.index')->with('success', 'Trip deleted successfully.');
    }
}
