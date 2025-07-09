@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Edit Trip</h1>

        <form action="{{ route('operator.trips.update', $trip) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="bus_id" class="block text-gray-700 text-sm font-bold mb-2">Bus</label>
                <select name="bus_id" id="bus_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @foreach ($buses as $bus)
                        <option value="{{ $bus->id }}" @if($bus->id == $trip->bus_id) selected @endif>{{ $bus->name }} ({{ $bus->registration_number }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="route_id" class="block text-gray-700 text-sm font-bold mb-2">Route</label>
                <select name="route_id" id="route_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @foreach ($routes as $route)
                        <option value="{{ $route->id }}" @if($route->id == $trip->route_id) selected @endif>{{ $route->routeName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="departure_datetime" class="block text-gray-700 text-sm font-bold mb-2">Departure Time</label>
                <input type="datetime-local" name="departure_datetime" id="departure_datetime" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ $trip->departure_datetime->format('Y-m-d\TH:i') }}" required>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price</label>
                <input type="number" step="0.01" name="price" id="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ $trip->price }}" required>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="active" @if($trip->status == 'active') selected @endif>Active</option>
                    <option value="inactive" @if($trip->status == 'inactive') selected @endif>Inactive</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Trip</button>
        </form>
    </div>
@endsection