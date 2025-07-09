@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Operator Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white shadow-md rounded p-4">
                <h2 class="text-lg font-semibold">Total Buses</h2>
                <p class="text-3xl">{{ $busesCount }}</p>
            </div>
            <div class="bg-white shadow-md rounded p-4">
                <h2 class="text-lg font-semibold">Total Routes</h2>
                <p class="text-3xl">{{ $routesCount }}</p>
            </div>
            <div class="bg-white shadow-md rounded p-4">
                <h2 class="text-lg font-semibold">Total Trips</h2>
                <p class="text-3xl">{{ $tripsCount }}</p>
            </div>
            <div class="bg-white shadow-md rounded p-4">
                <h2 class="text-lg font-semibold">Total Bookings</h2>
                <p class="text-3xl">{{ $bookingsCount }}</p>
            </div>
        </div>
    </div>
@endsection