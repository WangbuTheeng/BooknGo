<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routes = \App\Models\Route::with(['fromCity', 'toCity'])->latest()->paginate(10);

        return view('operator.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cities = \App\Models\City::orderBy('name')->get();
        return view('operator.routes.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'from_city_id' => 'required|exists:cities,id',
            'to_city_id' => 'required|exists:cities,id|different:from_city_id',
            'estimated_km' => 'required|integer|min:1',
            'estimated_time' => 'required|date_format:H:i',
        ]);

        \App\Models\Route::create($request->all());

        return redirect()->route('operator.routes.index')->with('success', 'Route created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $route = \App\Models\Route::with(['fromCity', 'toCity'])->findOrFail($id);

        return view('operator.routes.show', compact('route'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $route = \App\Models\Route::findOrFail($id);
        $cities = \App\Models\City::orderBy('name')->get();

        return view('operator.routes.edit', compact('route', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $route = \App\Models\Route::findOrFail($id);

        $request->validate([
            'from_city_id' => 'required|exists:cities,id',
            'to_city_id' => 'required|exists:cities,id|different:from_city_id',
            'estimated_km' => 'required|integer|min:1',
            'estimated_time' => 'required|date_format:H:i',
        ]);

        $route->update($request->all());

        return redirect()->route('operator.routes.index')->with('success', 'Route updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $route = \App\Models\Route::findOrFail($id);
        $route->delete();

        return redirect()->route('operator.routes.index')->with('success', 'Route deleted successfully.');
    }
}
