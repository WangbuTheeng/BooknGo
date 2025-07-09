@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Seat Layout Preview for {{ $bus->name }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Layout Summary</h2>
            <p><strong>Pattern:</strong> {{ $layoutSummary['pattern'] }}</p>
            <p><strong>Total Seats:</strong> {{ $layoutSummary['total_seats'] }}</p>
            <p><strong>Rows:</strong> {{ $layoutSummary['rows_count'] }}</p>
        </div>

        <div class="grid grid-cols-5 gap-4">
            @foreach ($bus->seats as $seat)
                <div class="p-4 border rounded text-center bg-gray-200">
                    {{ $seat->seat_number }}
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <a href="{{ route('operator.buses.layout.configure', $bus) }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                Reconfigure Layout
            </a>
            <a href="{{ route('operator.buses.show', $bus) }}" class="ml-4 text-gray-600 hover:text-gray-800">Back to Bus Details</a>
        </div>
    </div>
</div>
@endsection