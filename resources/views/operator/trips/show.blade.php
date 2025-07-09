@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Trip Details</h1>

        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-full table-auto">
                <tbody class="bg-white">
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Bus</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $trip->bus->name }} ({{ $trip->bus->registration_number }})</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Route</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $trip->route->routeName }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Departure</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $trip->departure_datetime }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Price</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $trip->price }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $trip->status }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <a href="{{ route('operator.trips.index') }}" class="text-indigo-600 hover:text-indigo-900">Back to Trips</a>
    </div>
@endsection