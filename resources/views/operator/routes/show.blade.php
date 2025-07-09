@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Route Details</h1>

        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-full table-auto">
                <tbody class="bg-white">
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">From</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $route->fromCity->name }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">To</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $route->toCity->name }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Distance (km)</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $route->estimated_km }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Time</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $route->estimated_time }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <a href="{{ route('operator.routes.index') }}" class="text-indigo-600 hover:text-indigo-900">Back to Routes</a>
    </div>
@endsection