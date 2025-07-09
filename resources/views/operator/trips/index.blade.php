@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Trips</h1>

        <a href="{{ route('operator.trips.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Add New Trip</a>

        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Bus</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Route</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Departure</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300"></th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($trips as $trip)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $trip->bus->name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $trip->route->routeName }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $trip->departure_datetime }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $trip->price }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $trip->status }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-right">
                                <a href="{{ route('operator.trips.show', $trip) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('operator.trips.edit', $trip) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                <form action="{{ route('operator.trips.destroy', $trip) }}" method="POST" class="inline-block ml-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center">No trips found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $trips->links() }}
    </div>
@endsection