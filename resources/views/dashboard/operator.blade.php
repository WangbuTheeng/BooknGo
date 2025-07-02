<!-- Operator Dashboard -->

@php
    $operator = Auth::user()->operator;
    $operatorBuses = $operator->buses;
    $operatorTrips = $operator->trips();
    $todayTrips = $operatorTrips->whereDate('departure_datetime', today());
    $recentBookings = \App\Models\Booking::whereHas('trip.bus', function($q) use ($operator) {
        $q->where('operator_id', $operator->id);
    })->with(['trip.route.fromCity', 'trip.route.toCity'])->latest()->take(5)->get();
@endphp

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-1M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">My Buses</p>
                <p class="text-2xl font-bold text-gray-900">{{ $operatorBuses->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Active Trips</p>
                <p class="text-2xl font-bold text-gray-900">{{ $operatorTrips->where('status', 'active')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Today's Trips</p>
                <p class="text-2xl font-bold text-gray-900">{{ $todayTrips->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">This Month's Revenue</p>
                <p class="text-2xl font-bold text-gray-900">
                    NPR {{ number_format(\App\Models\Booking::whereHas('trip.bus', function($q) use ($operator) {
                        $q->where('operator_id', $operator->id);
                    })->whereMonth('created_at', now()->month)->where('status', 'confirmed')->sum('total_amount')) }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Today's Schedule -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('trips.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">Schedule Trip</div>
                        <div class="text-sm text-gray-600">Create new trip</div>
                    </div>
                </a>

                <a href="{{ route('trips.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">Manage Trips</div>
                        <div class="text-sm text-gray-600">View all trips</div>
                    </div>
                </a>

                <a href="{{ route('buses.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">Add Bus</div>
                        <div class="text-sm text-gray-600">Register new bus</div>
                    </div>
                </a>

                <a href="{{ route('buses.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-1M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">Manage Buses</div>
                        <div class="text-sm text-gray-600">View fleet</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Today's Schedule</h3>
        </div>
        <div class="p-6">
            @if($todayTrips->count() > 0)
                <div class="space-y-4">
                    @foreach($todayTrips->take(5)->get() as $trip)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">
                                    {{ $trip->route->fromCity->name }} → {{ $trip->route->toCity->name }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $trip->departure_datetime->format('H:i') }} • {{ $trip->bus->name ?: $trip->bus->registration_number }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($trip->status === 'active') bg-green-100 text-green-800
                                    @elseif($trip->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($trip->status) }}
                                </span>
                                <a href="{{ route('trips.show', $trip) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($todayTrips->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('trips.index', ['date_from' => today()->format('Y-m-d')]) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                            View all {{ $todayTrips->count() }} trips today
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No trips scheduled today</h3>
                    <p class="mt-1 text-sm text-gray-500">Schedule your first trip for today.</p>
                    <div class="mt-6">
                        <a href="{{ route('trips.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Schedule Trip
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Bookings & Performance -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Bookings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
        </div>
        <div class="p-6">
            @if($recentBookings->count() > 0)
                <div class="space-y-4">
                    @foreach($recentBookings as $booking)
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $booking->passenger_name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}
                                    • {{ $booking->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No recent bookings</p>
            @endif
        </div>
    </div>

    <!-- Performance Overview -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Performance Overview</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <!-- This Week -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">This Week</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Trips Completed</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $operatorTrips->whereBetween('departure_datetime', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'completed')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Bookings</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ \App\Models\Booking::whereHas('trip.bus', function($q) use ($operator) {
                                    $q->where('operator_id', $operator->id);
                                })->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Revenue</span>
                            <span class="text-sm font-medium text-gray-900">
                                NPR {{ number_format(\App\Models\Booking::whereHas('trip.bus', function($q) use ($operator) {
                                    $q->where('operator_id', $operator->id);
                                })->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'confirmed')->sum('total_amount')) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Fleet Status -->
                <div class="pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Fleet Status</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Buses</span>
                            <span class="text-sm font-medium text-gray-900">{{ $operatorBuses->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Seats</span>
                            <span class="text-sm font-medium text-gray-900">{{ $operatorBuses->sum('total_seats') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Avg. Occupancy</span>
                            <span class="text-sm font-medium text-gray-900">
                                @php
                                    $totalSeats = $operatorBuses->sum('total_seats');
                                    $bookedSeats = \App\Models\BookingSeat::whereHas('booking.trip.bus', function($q) use ($operator) {
                                        $q->where('operator_id', $operator->id);
                                    })->whereHas('booking', function($q) {
                                        $q->where('status', 'confirmed');
                                    })->count();
                                    $occupancy = $totalSeats > 0 ? round(($bookedSeats / $totalSeats) * 100, 1) : 0;
                                @endphp
                                {{ $occupancy }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
