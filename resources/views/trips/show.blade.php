<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Trip Details | BooknGo</title>
        <meta name="description" content="View trip details and manage bookings">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 font-sans antialiased">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="text-2xl font-bold text-blue-600">BooknGo</a>
                        <span class="ml-2 text-sm text-gray-500">Trip Details</span>
                    </div>
                    <nav class="flex items-center space-x-4">
                        <a href="{{ route('trips.index') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            Back to Trips
                        </a>
                        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            {{ $trip->route->fromCity->name }} → {{ $trip->route->toCity->name }}
                        </h1>
                        <p class="mt-2 text-gray-600">
                            {{ $trip->departure_datetime->format('l, F j, Y \a\t g:i A') }}
                            @if($trip->arrival_time)
                                → {{ $trip->arrival_time->format('g:i A') }}
                            @endif
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        @can('update', $trip)
                            <a href="{{ route('trips.edit', $trip) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Trip
                            </a>
                        @endcan
                        <a href="{{ route('trips.select-seats', $trip) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Seats
                        </a>
                    </div>
                </div>
            </div>

            <!-- Trip Status Banner -->
            <div class="mb-8">
                <div class="rounded-lg p-4 
                    @if($trip->status === 'active') bg-green-50 border border-green-200
                    @elseif($trip->status === 'cancelled') bg-red-50 border border-red-200
                    @elseif($trip->status === 'completed') bg-gray-50 border border-gray-200
                    @else bg-blue-50 border border-blue-200 @endif">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($trip->status === 'active')
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            @elseif($trip->status === 'cancelled')
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium 
                                @if($trip->status === 'active') text-green-800
                                @elseif($trip->status === 'cancelled') text-red-800
                                @elseif($trip->status === 'completed') text-gray-800
                                @else text-blue-800 @endif">
                                Trip Status: {{ ucfirst($trip->status) }}
                                @if($trip->status === 'active')
                                    - Accepting bookings
                                @elseif($trip->status === 'cancelled')
                                    - This trip has been cancelled
                                @elseif($trip->status === 'completed')
                                    - This trip has been completed
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Trip Information -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Route & Schedule Details -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Route & Schedule</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Departure</h4>
                                    <div class="text-lg font-semibold text-gray-900">{{ $trip->route->fromCity->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $trip->departure_datetime->format('M j, Y \a\t g:i A') }}</div>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Arrival</h4>
                                    <div class="text-lg font-semibold text-gray-900">{{ $trip->route->toCity->name }}</div>
                                    <div class="text-sm text-gray-600">
                                        @if($trip->arrival_time)
                                            {{ $trip->arrival_time->format('M j, Y \a\t g:i A') }}
                                        @else
                                            <span class="text-gray-400">Time not specified</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if($trip->route->estimated_km)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Distance:</span> {{ $trip->route->estimated_km }} km
                                        @if($trip->arrival_time)
                                            <span class="ml-4 font-medium">Duration:</span> 
                                            {{ $trip->departure_datetime->diffForHumans($trip->arrival_time, true) }}
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bus Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Bus Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Bus Details</h4>
                                    <div class="text-lg font-semibold text-gray-900">{{ $trip->bus->name ?: $trip->bus->registration_number }}</div>
                                    <div class="text-sm text-gray-600">{{ $trip->bus->type }} • {{ $trip->bus->total_seats }} seats</div>
                                    @if($trip->bus->features)
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach($trip->bus->features as $feature)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $feature }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Operator</h4>
                                    <div class="text-lg font-semibold text-gray-900">{{ $trip->bus->operator->user->name }}</div>
                                    @if($trip->bus->operator->company_name)
                                        <div class="text-sm text-gray-600">{{ $trip->bus->operator->company_name }}</div>
                                    @endif
                                    @if($trip->bus->operator->contact_info && isset($trip->bus->operator->contact_info['phone']))
                                        <div class="text-sm text-gray-600">{{ $trip->bus->operator->contact_info['phone'] }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bookings List -->
                    @if($trip->bookings->count() > 0)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Passenger Bookings</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passenger</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Code</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($trip->bookings as $booking)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $booking->passenger_name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $booking->passenger_phone }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $booking->booking_code }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $booking->bookingSeats->pluck('seat.seat_number')->join(', ') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    NPR {{ number_format($booking->total_amount) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @else bg-blue-100 text-blue-800 @endif">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Pricing Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Pricing</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-3xl font-bold text-gray-900">NPR {{ number_format($trip->price) }}</div>
                            <div class="text-sm text-gray-600">per seat</div>
                            @if($trip->is_festival_fare)
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Festival Pricing Applied
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Seat Availability -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Seat Availability</h3>
                        </div>
                        <div class="p-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600">{{ $trip->available_seats_count }}</div>
                                <div class="text-sm text-gray-600">of {{ $trip->bus->total_seats }} seats available</div>
                                
                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-blue-600 h-3 rounded-full" style="width: {{ ($trip->available_seats_count / $trip->bus->total_seats) * 100 }}%"></div>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $trip->bus->total_seats - $trip->available_seats_count }}</div>
                                        <div class="text-gray-600">Booked</div>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $trip->available_seats_count }}</div>
                                        <div class="text-gray-600">Available</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('trips.select-seats', $trip) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Seat Map
                            </a>
                            
                            @can('update', $trip)
                                <a href="{{ route('trips.edit', $trip) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Trip Details
                                </a>
                            @endcan

                            @if($trip->status === 'active')
                                <form method="POST" action="{{ route('trips.cancel', $trip) }}" class="w-full" onsubmit="return confirm('Are you sure you want to cancel this trip? This action cannot be undone.')">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Cancel Trip
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
