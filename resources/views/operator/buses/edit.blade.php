@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Edit Bus</h1>

        <form action="{{ route('operator.buses.update', $bus) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="registration_number" class="block text-gray-700 text-sm font-bold mb-2">Registration Number</label>
                <input type="text" name="registration_number" id="registration_number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ $bus->registration_number }}" required>
            </div>

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Bus Name</label>
                <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ $bus->name }}" required>
            </div>

            <div class="mb-4">
                <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Bus Type</label>
                <input type="text" name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ $bus->type }}" required>
            </div>

            <div class="mb-4">
                <label for="total_seats" class="block text-gray-700 text-sm font-bold mb-2">Total Seats</label>
                <input type="number" name="total_seats" id="total_seats" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ $bus->total_seats }}" required>
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Bus</button>
        </form>
    </div>
@endsection