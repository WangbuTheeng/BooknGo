<!-- Admin Dashboard -->

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Active Operators</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::where('role', 'operator')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Trips</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Trip::count() }}</p>
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
                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-900">NPR {{ number_format(\App\Models\Booking::where('status', 'confirmed')->sum('total_amount')) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('trips.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">Manage Trips</div>
                        <div class="text-sm text-gray-600">View and manage all trips</div>
                    </div>
                </a>

                <a href="{{ route('buses.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-1M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">Manage Buses</div>
                        <div class="text-sm text-gray-600">View all buses and operators</div>
                    </div>
                </a>

                <a href="{{ route('bookings.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">View Bookings</div>
                        <div class="text-sm text-gray-600">Monitor all bookings</div>
                    </div>
                </a>

                <a href="{{ route('payments.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                    <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <div>
                        <div class="font-medium text-gray-900">Payment Reports</div>
                        <div class="text-sm text-gray-600">View payment analytics</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
        </div>
        <div class="p-6">
            @php
                $recentBookings = \App\Models\Booking::with(['trip.route.fromCity', 'trip.route.toCity', 'user'])
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp
            
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
                                    New booking by {{ $booking->user->name ?? $booking->passenger_name }}
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
                <p class="text-gray-500 text-center py-4">No recent activity</p>
            @endif
        </div>
    </div>
</div>

<!-- System Overview -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">System Overview</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Today's Stats -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Today's Activity</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">New Bookings</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ \App\Models\Booking::whereDate('created_at', today())->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Active Trips</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ \App\Models\Trip::where('status', 'active')->whereDate('departure_datetime', today())->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Revenue</span>
                        <span class="text-sm font-medium text-gray-900">
                            NPR {{ number_format(\App\Models\Booking::whereDate('created_at', today())->where('status', 'confirmed')->sum('total_amount')) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- This Week's Stats -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">This Week</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Bookings</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ \App\Models\Booking::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">New Users</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ \App\Models\User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Revenue</span>
                        <span class="text-sm font-medium text-gray-900">
                            NPR {{ number_format(\App\Models\Booking::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'confirmed')->sum('total_amount')) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div>
                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">System Status</h4>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Festival Mode</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                            @if(\App\Models\SystemSetting::isFestivalModeEnabled()) bg-orange-100 text-orange-800 @else bg-gray-100 text-gray-800 @endif">
                            @if(\App\Models\SystemSetting::isFestivalModeEnabled()) Enabled @else Disabled @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Festival Multiplier</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ \App\Models\SystemSetting::getFestivalFareMultiplier() }}x
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Platform Status</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Operational
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
