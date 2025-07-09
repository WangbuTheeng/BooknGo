@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">{{ $bus->name }}</h1>

        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-full table-auto">
                <tbody class="bg-white">
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Registration Number</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $bus->registration_number }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Bus Name</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $bus->name }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Bus Type</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $bus->type }}</td>
                    </tr>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Total Seats</th>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $bus->total_seats }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <a href="{{ route('operator.buses.index') }}" class="text-indigo-600 hover:text-indigo-900">Back to Buses</a>
    </div>
@endsection