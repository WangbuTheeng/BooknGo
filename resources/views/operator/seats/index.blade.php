@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Seat Map for Trip #{{ $trip->id }}</h1>

        <div class="bg-white shadow-md rounded my-6 p-6">
            <div class="grid grid-cols-5 gap-4">
                @foreach ($seats as $seat)
                    @php
                        $isBooked = in_array($seat->id, $bookedSeats);
                        $isBlocked = !$seat->is_available_for_booking;
                        $seatClass = 'p-4 border rounded text-center';
                        if ($isBooked) {
                            $seatClass .= ' bg-red-500 text-white';
                        } elseif ($isBlocked) {
                            $seatClass .= ' bg-gray-400 text-white';
                        } else {
                            $seatClass .= ' bg-green-500 text-white';
                        }
                    @endphp
                    <div class="{{ $seatClass }}">
                        {{ $seat->seat_number }}
                        @if (!$isBooked)
                            <div class="flex justify-center items-center mt-2">
                                <form action="{{ $isBlocked ? route('operator.trips.seats.unblock', ['trip' => $trip, 'seat' => $seat]) : route('operator.trips.seats.block', ['trip' => $trip, 'seat' => $seat]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs underline">
                                        {{ $isBlocked ? 'Unblock' : 'Block' }}
                                    </button>
                                </form>
                                <a href="{{ route('operator.trips.seats.edit', ['trip' => $trip, 'seat' => $seat]) }}" class="text-xs underline ml-2">Edit</a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <a href="{{ route('operator.trips.show', $trip) }}" class="text-indigo-600 hover:text-indigo-900">Back to Trip</a>
    </div>
@endsection