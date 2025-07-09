@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Booking Details</h1>

        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-full table-auto">
                <tbody class="bg-white">
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Customer</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $booking->user->name }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Trip</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $booking->trip->route->routeName }} @ {{ $booking->trip->departure_datetime }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Seats</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $booking->seats->pluck('seat_number')->join(', ') }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $booking->status }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Payment Status</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $booking->payment_status }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <a href="{{ route('operator.bookings.index') }}" class="text-indigo-600 hover:text-indigo-900">Back to Bookings</a>
    </div>
@endsection