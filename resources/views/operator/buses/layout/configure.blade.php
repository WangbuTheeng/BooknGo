@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Configure Seat Layout for {{ $bus->name }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('operator.buses.layout.store', $bus) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="layout_pattern" class="block text-gray-700 font-semibold mb-2">Layout Pattern</label>
                    <select name="layout_pattern" id="layout_pattern" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="2x2" @if($bus->layout_pattern == '2x2') selected @endif>2x2</option>
                        <option value="2x1" @if($bus->layout_pattern == '2x1') selected @endif>2x1</option>
                        <option value="1x1" @if($bus->layout_pattern == '1x1') selected @endif>1x1</option>
                        <option value="custom" @if($bus->layout_pattern == 'custom') selected @endif>Custom</option>
                    </select>
                </div>

                <div>
                    <label for="total_seats" class="block text-gray-700 font-semibold mb-2">Total Seats</label>
                    <input type="number" name="total_seats" id="total_seats" value="{{ old('total_seats', $bus->total_seats) }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label for="bus_category" class="block text-gray-700 font-semibold mb-2">Bus Category</label>
                    <select name="bus_category" id="bus_category" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="standard" @if($bus->bus_category == 'standard') selected @endif>Standard</option>
                        <option value="deluxe" @if($bus->bus_category == 'deluxe') selected @endif>Deluxe</option>
                        <option value="sleeper" @if($bus->bus_category == 'sleeper') selected @endif>Sleeper</option>
                        <option value="semi_sleeper" @if($bus->bus_category == 'semi_sleeper') selected @endif>Semi-Sleeper</option>
                        <option value="vip" @if($bus->bus_category == 'vip') selected @endif>VIP</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Save Configuration
                </button>
                <a href="{{ route('operator.buses.show', $bus) }}" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection