<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $operator->company_name }} - Buses - BooknGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-blue-600">BooknGo</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-600">Home</a>
                    <a href="{{ route('trips.search') }}" class="text-gray-700 hover:text-blue-600">Search Trips</a>
                    <a href="{{ route('public.operators.index') }}" class="text-gray-700 hover:text-blue-600">Operators</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-4 py-3">
                <a href="{{ route('public.operators.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Operators</a>
                <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-sm text-gray-900">{{ $operator->company_name }}</span>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $operator->company_name }}</h1>
                    <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $operator->address }}
                        </div>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            {{ $operator->phone }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if($buses->count() > 0)
            <!-- Buses Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($buses as $bus)
                    <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">{{ $bus->name }}</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $bus->type }}
                                </span>
                            </div>
                            
                            <div class="mt-4 space-y-2">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ $bus->total_seats }} seats
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ $bus->registration_number }}
                                </div>
                            </div>

                            <!-- Upcoming Trips -->
                            @if($bus->trips->count() > 0)
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Upcoming Trips</h4>
                                    <div class="space-y-2">
                                        @foreach($bus->trips->take(3) as $trip)
                                            <div class="flex items-center justify-between text-xs bg-gray-50 rounded p-2">
                                                <div>
                                                    <span class="font-medium">{{ $trip->route->fromCity->name }} → {{ $trip->route->toCity->name }}</span>
                                                    <div class="text-gray-500">{{ $trip->departure_datetime->format('M d, H:i') }}</div>
                                                </div>
                                                <div class="text-right">
                                                    @if($trip->isFinished())
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Finished
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Active
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mt-6">
                                <a href="{{ route('public.buses.trips', $bus) }}" class="w-full bg-blue-600 border border-transparent rounded-md py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    View All Trips
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $buses->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2a2 2 0 00-2-2H8z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No buses found</h3>
                <p class="mt-1 text-sm text-gray-500">This operator currently has no buses available.</p>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">
                © {{ date('Y') }} BooknGo. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
