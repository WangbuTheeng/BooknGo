<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Bookings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($bookings as $booking)
                            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-indigo-600">{{ $booking->booking_reference }}</p>
                                            <h3 class="text-lg font-semibold text-gray-900 mt-1">
                                                {{ $booking->trip->route->fromCity->name }} to {{ $booking->trip->route->toCity->name }}
                                            </h3>
                                        </div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @switch($booking->status)
                                                @case('booked') bg-green-100 text-green-800 @break
                                                @case('pending') bg-yellow-100 text-yellow-800 @break
                                                @case('cancelled') bg-red-100 text-red-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch
                                        ">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-600">
                                            <strong>Date:</strong> {{ $booking->trip->departure_datetime->format('d M Y, H:i') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <strong>Bus:</strong> {{ $booking->trip->bus->bus_name }} ({{ $booking->trip->bus->bus_reg_number }})
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <strong>Passenger:</strong> {{ $booking->passenger_name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                                    <a href="{{ route('bookings.show', $booking) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">View Details</a>
                                </div>
                            </div>
                        @empty
                            <div class="md:col-span-2 lg:col-span-3 text-center py-12">
                                <p class="text-gray-500 text-lg">You have no bookings.</p>
                                <a href="{{ url('/') }}" class="mt-4 inline-block bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition duration-300">
                                    Book a Trip
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
