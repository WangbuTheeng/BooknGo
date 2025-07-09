<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $operator = auth()->user()->operator;
        $buses = $operator->buses()->latest()->paginate(10);

        return view('operator.buses.index', compact('buses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('operator.buses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string|max:255|unique:buses',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'total_seats' => 'required|integer|min:1',
        ]);

        $operator = auth()->user()->operator;

        $bus = $operator->buses()->create($request->all());

        return redirect()->route('operator.buses.index')->with('success', 'Bus created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bus = auth()->user()->operator->buses()->findOrFail($id);

        return view('operator.buses.show', compact('bus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bus = auth()->user()->operator->buses()->findOrFail($id);

        return view('operator.buses.edit', compact('bus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bus = auth()->user()->operator->buses()->findOrFail($id);

        $request->validate([
            'registration_number' => 'required|string|max:255|unique:buses,registration_number,' . $bus->id,
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'total_seats' => 'required|integer|min:1',
        ]);

        $bus->update($request->all());

        return redirect()->route('operator.buses.index')->with('success', 'Bus updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bus = auth()->user()->operator->buses()->findOrFail($id);
        $bus->delete();

        return redirect()->route('operator.buses.index')->with('success', 'Bus deleted successfully.');
    }
}
