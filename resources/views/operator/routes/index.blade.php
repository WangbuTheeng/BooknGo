@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Routes</h1>

        <a href="{{ route('operator.routes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Add New Route</a>

        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">From</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">To</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Distance (km)</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300"></th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($routes as $route)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $route->fromCity->name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $route->toCity->name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $route->estimated_km }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $route->estimated_time }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-right">
                                <a href="{{ route('operator.routes.show', $route) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('operator.routes.edit', $route) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                <form action="{{ route('operator.routes.destroy', $route) }}" method="POST" class="inline-block ml-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center">No routes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $routes->links() }}
    </div>
@endsection