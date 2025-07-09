@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Edit Seat for Trip #{{ $trip->id }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('operator.trips.seats.update', ['trip' => $trip->id, 'seat' => $seat->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="seat_number" class="block text-gray-700 font-semibold mb-2">Seat Number</label>
                <input type="text" name="seat_number" id="seat_number" value="{{ old('seat_number', $seat->seat_number) }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('seat_number') border-red-500 @enderror" required>
                @error('seat_number')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="position" class="block text-gray-700 font-semibold mb-2">Position (Optional)</label>
                <input type="text" name="position" id="position" value="{{ old('position', $seat->position) }}" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('position') border-red-500 @enderror">
                @error('position')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Update Seat
                </button>
                <a href="{{ route('operator.trips.seats.index', $trip) }}" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection