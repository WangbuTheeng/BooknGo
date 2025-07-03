<!-- User Dashboard -->

@php
    $userBookings = Auth::user()->bookings()->with(['trip.route.fromCity', 'trip.route.toCity', 'trip.bus'])->latest();
    $upcomingBookings = $userBookings->whereHas('trip', function($q) {
        $q->where('departure_datetime', '>', now());
    })->take(3)->get();
    $recentBookings = $userBookings->take(5)->get();
    $operators = \App\Models\Operator::latest()->take(4)->get();
@endphp

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                <p class="text-2xl font-bold text-gray-900">{{ Auth::user()->bookings()->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Upcoming Trips</p>
                <p class="text-2xl font-bold text-gray-900">{{ $upcomingBookings->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Cities Visited</p>
                <p class="text-2xl font-bold text-gray-900">
                    {{ Auth::user()->bookings()->whereHas('trip.route')->with('trip.route.toCity')->get()->pluck('trip.route.toCity.name')->unique()->count() }}
                </p>
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
                <p class="text-sm font-medium text-gray-600">Total Spent</p>
                <p class="text-2xl font-bold text-gray-900">
                    NPR {{ number_format(Auth::user()->bookings()->where('status', 'confirmed')->sum('total_amount')) }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Upcoming Trips -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ url('/') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">Search Trips</div>
                        <div class="text-sm text-gray-600">Find your next journey</div>
                    </div>
                </a>

                <a href="{{ route('bookings.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">My Bookings</div>
                        <div class="text-sm text-gray-600">View all bookings</div>
                    </div>
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">Profile</div>
                        <div class="text-sm text-gray-600">Update your details</div>
                    </div>
                </a>

                <a href="#" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75A9.75 9.75 0 0012 2.25z"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">Help & Support</div>
                        <div class="text-sm text-gray-600">Get assistance</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Upcoming Trips -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Upcoming Trips</h3>
        </div>
        <div class="p-6">
            @if($upcomingBookings->count() > 0)
                <div class="space-y-4">
                    @foreach($upcomingBookings as $booking)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium text-gray-900">
                                    {{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $booking->trip->departure_datetime->format('M j, Y \a\t g:i A') }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-1M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                    </svg>
                                    {{ $booking->trip->bus->name ?: $booking->trip->bus->registration_number }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Seats: {{ $booking->bookingSeats->pluck('seat.seat_number')->join(', ') }}
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="text-sm font-medium text-gray-900">
                                    NPR {{ number_format($booking->total_amount) }}
                                </div>
                                <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming trips</h3>
                    <p class="mt-1 text-sm text-gray-500">Book your next journey to see it here.</p>
                    <div class="mt-6">
                        <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Search Trips
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Activity & Travel Stats -->
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ $booking->trip->departure_datetime->format('M j, Y') }}
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
                <p class="text-gray-500 text-center py-4">No bookings yet</p>
            @endif
        </div>
    </div>

    <!-- Travel Statistics -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Travel Statistics</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <!-- This Year -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">This Year</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Trips Taken</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ Auth::user()->bookings()->whereYear('created_at', now()->year)->where('status', 'confirmed')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Spent</span>
                            <span class="text-sm font-medium text-gray-900">
                                NPR {{ number_format(Auth::user()->bookings()->whereYear('created_at', now()->year)->where('status', 'confirmed')->sum('total_amount')) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Favorite Route</span>
                            <span class="text-sm font-medium text-gray-900">
                                @php
                                    $favoriteRoute = Auth::user()->bookings()
                                        ->with('trip.route.fromCity', 'trip.route.toCity')
                                        ->whereYear('created_at', now()->year)
                                        ->where('status', 'confirmed')
                                        ->get()
                                        ->groupBy(function($booking) {
                                            return $booking->trip->route->fromCity->name . ' → ' . $booking->trip->route->toCity->name;
                                        })
                                        ->sortByDesc(function($group) {
                                            return $group->count();
                                        })
                                        ->keys()
                                        ->first();
                                @endphp
                                {{ $favoriteRoute ?: 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Preferences -->
                <div class="pt-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Preferences</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Preferred Payment</span>
                            <span class="text-sm font-medium text-gray-900">
                                @php
                                    $preferredPayment = Auth::user()->bookings()
                                        ->with('payment')
                                        ->whereHas('payment')
                                        ->get()
                                        ->groupBy('payment.payment_method')
                                        ->sortByDesc(function($group) {
                                            return $group->count();
                                        })
                                        ->keys()
                                        ->first();
                                @endphp
                                {{ $preferredPayment ? ucfirst($preferredPayment) : 'N/A' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Avg. Trip Cost</span>
                            <span class="text-sm font-medium text-gray-900">
                                NPR {{ number_format(Auth::user()->bookings()->where('status', 'confirmed')->avg('total_amount') ?: 0) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Member Since</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ Auth::user()->created_at->format('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Operators -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Featured Operators</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($operators as $operator)
                <a href="{{ route('operators.show', $operator) }}" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <img src="{{ $operator->logo_url }}" alt="{{ $operator->company_name }}" class="h-16 w-16 mx-auto mb-3 rounded-full object-cover">
                    <div class="text-center">
                        <div class="font-medium text-gray-900">{{ $operator->company_name }}</div>
                        <div class="text-sm text-gray-600">{{ $operator->buses->count() }} Buses</div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6 text-center">
            <a href="{{ route('operators.index') }}" class="text-blue-600 hover:text-blue-900 font-medium">
                View All Operators &rarr;
            </a>
        </div>
    </div>
</div>
