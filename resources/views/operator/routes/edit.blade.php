@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Edit Route</h1>

        <form action="{{ route('operator.routes.update', $route) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="from_city_id" class="block text-gray-700 text-sm font-bold mb-2">From City</label>
                <select name="from_city_id" id="from_city_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" @if($city->id == $route->from_city_id) selected @endif>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="to_city_id" class="block text-gray-700 text-sm font-bold mb-2">To City</label>
                <select name="to_city_id" id="to_city_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" @if($city->id == $route->to_city_id) selected @endif>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="estimated_km" class="block text-gray-700 text-sm font-bold mb-2">Estimated Distance (km)</label>
                <input type="number" name="estimated_km" id="estimated_km" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ $route->estimated_km }}" required>
            </div>

            <div class="mb-4">
                <label for="estimated_time" class="block text-gray-700 text-sm font-bold mb-2">Estimated Time (HH:MM)</label>
                <input type="text" name="estimated_time" id="estimated_time" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="HH:MM" value="{{ $route->estimated_time }}" required>
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Route</button>
        </form>
    </div>
@endsection