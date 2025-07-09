@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">My Buses</h1>

        <a href="{{ route('operator.buses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Add New Bus</a>

        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Registration</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Total Seats</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300"></th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($buses as $bus)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $bus->registration_number }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $bus->name }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $bus->type }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $bus->total_seats }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-right">
                                <a href="{{ route('operator.buses.show', $bus) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                <a href="{{ route('operator.buses.edit', $bus) }}" class="text-indigo-600 hover:text-indigo-900 ml-4">Edit</a>
                                <form action="{{ route('operator.buses.destroy', $bus) }}" method="POST" class="inline-block ml-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center">No buses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $buses->links() }}
    </div>
@endsection