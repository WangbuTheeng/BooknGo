<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Search Results - {{ $fromCity->name }} to {{ $toCity->name }} | BooknGo</title>
        <meta name="description" content="Bus search results from {{ $fromCity->name }} to {{ $toCity->name }} on {{ $departureDate }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/notifications.js'])
        
        <!-- Flash message meta tags for notifications -->
        @if(session('success'))
            <meta name="flash-success" content="{{ session('success') }}">
        @endif
        @if(session('error'))
            <meta name="flash-error" content="{{ session('error') }}">
        @endif
        @if(session('info'))
            <meta name="flash-info" content="{{ session('info') }}">
        @endif
        @if(session('warning'))
            <meta name="flash-warning" content="{{ session('warning') }}">
        @endif
    </head>
    <body class="bg-gray-50 font-sans antialiased">
        <!-- Modern Navbar -->
        @include('components.modern-navbar')

        <!-- Search Summary -->
        <div class="bg-blue-600 text-white py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">
                            {{ $fromCity->name }} → {{ $toCity->name }}
                        </h1>
                        <p class="text-blue-100">
                            {{ \Carbon\Carbon::parse($departureDate)->format('l, F j, Y') }}
                            • {{ $trips->count() }} {{ Str::plural('bus', $trips->count()) }} found
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a 
                            href="{{ url('/') }}" 
                            class="inline-flex items-center px-4 py-2 border border-blue-400 text-sm font-medium rounded-md text-blue-100 hover:bg-blue-700 hover:border-blue-300 transition duration-150 ease-in-out"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            New Search
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($trips->count() > 0)
                <!-- Filters and Sort -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="text-sm font-medium text-gray-700">Filter by:</span>
                            <select class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Bus Types</option>
                                <option value="ac">AC Buses</option>
                                <option value="non-ac">Non-AC Buses</option>
                                <option value="deluxe">Deluxe</option>
                                <option value="super-deluxe">Super Deluxe</option>
                            </select>
                            <select class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Prices</option>
                                <option value="0-500">Under Rs. 500</option>
                                <option value="500-1000">Rs. 500 - 1000</option>
                                <option value="1000-1500">Rs. 1000 - 1500</option>
                                <option value="1500+">Above Rs. 1500</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-medium text-gray-700">Sort by:</span>
                            <select class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="departure">Departure Time</option>
                                <option value="price-low">Price: Low to High</option>
                                <option value="price-high">Price: High to Low</option>
                                <option value="duration">Duration</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Trip Results -->
                <div class="space-y-4">
                    @foreach($trips as $trip)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition duration-200">
                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                                    <!-- Bus Info -->
                                    <div class="lg:col-span-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2a2 2 0 00-2-2H8z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900">{{ $trip->bus->name }}</h3>
                                                <p class="text-sm text-gray-500">{{ $trip->bus->operator->user->name }}</p>
                                                <div class="flex items-center mt-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ ucfirst($trip->bus->type) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Time Info -->
                                    <div class="lg:col-span-3">
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-gray-900">
                                                {{ $trip->departure_datetime->format('H:i') }}
                                            </div>
                                            <div class="text-sm text-gray-500 mb-2">
                                                {{ $fromCity->name }}
                                            </div>
                                            <div class="flex items-center justify-center">
                                                <div class="w-8 h-px bg-gray-300"></div>
                                                <svg class="w-4 h-4 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                                <div class="w-8 h-px bg-gray-300"></div>
                                            </div>
                                            <div class="text-lg font-semibold text-gray-900 mt-2">
                                                {{ $trip->arrival_time ? $trip->arrival_time->format('H:i') : 'TBA' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $toCity->name }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Duration & Route Info -->
                                    <div class="lg:col-span-2">
                                        <div class="text-center">
                                            @if($trip->arrival_time)
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $trip->departure_datetime->diffInHours($trip->arrival_time) }}h 
                                                    {{ $trip->departure_datetime->diffInMinutes($trip->arrival_time) % 60 }}m
                                                </div>
                                            @else
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $trip->route->estimated_time ? $trip->route->estimated_time->format('H:i') : 'TBA' }}
                                                </div>
                                            @endif
                                            <div class="text-xs text-gray-500">Duration</div>
                                            @if($trip->route->estimated_km)
                                                <div class="text-xs text-gray-500 mt-1">{{ $trip->route->estimated_km }} km</div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Seats & Price -->
                                    <div class="lg:col-span-2">
                                        <div class="text-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $trip->available_seats }} seats left
                                            </div>
                                            <div class="text-xs text-gray-500">of {{ $trip->bus->total_seats }} total</div>
                                            <div class="mt-2">
                                                <span class="text-2xl font-bold text-green-600">
                                                    Rs. {{ number_format($trip->price) }}
                                                </span>
                                                @if($trip->is_festival_fare)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 ml-2">
                                                        Festival Fare
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="lg:col-span-2">
                                        <div class="text-center">
                                            @if($trip->isBookable())
                                                @auth
                                                    <button 
                                                        onclick="selectSeats({{ $trip->id }})"
                                                        class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                                                    >
                                                        Select Seats
                                                    </button>
                                                @else
                                                    <button 
                                                        onclick="showLoginModal({{ $trip->id }})"
                                                        class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out"
                                                    >
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        Login to Book
                                                    </button>
                                                @endauth
                                            @else
                                                <button 
                                                    disabled
                                                    class="w-full inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed"
                                                >
                                                    Not Available
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Bus Features -->
                                @if($trip->bus->features)
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($trip->bus->features as $feature)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $feature }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Results -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.608-4.785-3.709M6.343 6.343A8 8 0 1017.657 17.657 8 8 0 006.343 6.343z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No buses found</h3>
                    <p class="text-gray-600 mb-6">
                        Sorry, we couldn't find any buses for your selected route and date.
                    </p>
                    <div class="space-y-3">
                        <p class="text-sm text-gray-500">Try:</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Selecting a different date</li>
                            <li>• Checking nearby cities</li>
                            <li>• Booking for a later time</li>
                        </ul>
                    </div>
                    <div class="mt-8">
                        <a 
                            href="{{ url('/') }}" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            Search Again
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- JavaScript for seat selection -->
        <script>
            function selectSeats(tripId) {
                window.location.href = `/trips/${tripId}/seats`;
            }

            function showLoginModal(tripId) {
                // Create modal overlay
                const modalOverlay = document.createElement('div');
                modalOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                modalOverlay.innerHTML = `
                    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 shadow-2xl">
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Login Required</h3>
                            <p class="text-gray-600">You need to be logged in to view available seats and book tickets. Please choose an option:</p>
                        </div>
                        
                        <div class="space-y-3">
                            <button onclick="redirectToLogin(${tripId})" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition duration-150 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 0v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Login to Existing Account
                            </button>
                            
                            <button onclick="redirectToRegister(${tripId})" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition duration-150 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                Create New Account
                            </button>
                            
                            <button onclick="closeModal()" 
                                    class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-medium transition duration-150">
                                Cancel
                            </button>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                After login, you'll be able to view and select seats
                            </div>
                        </div>
                    </div>
                `;
                
                // Close modal when clicking outside
                modalOverlay.onclick = (e) => {
                    if (e.target === modalOverlay) {
                        closeModal();
                    }
                };
                
                document.body.appendChild(modalOverlay);
                window.currentModal = modalOverlay;
            }

            function redirectToLogin(tripId) {
                const seatSelectionUrl = `/trips/${tripId}/seats`;
                const returnUrl = encodeURIComponent(seatSelectionUrl);
                window.location.href = `/login?redirect=${returnUrl}`;
            }

            function redirectToRegister(tripId) {
                const seatSelectionUrl = `/trips/${tripId}/seats`;
                const returnUrl = encodeURIComponent(seatSelectionUrl);
                window.location.href = `/register?redirect=${returnUrl}`;
            }

            function closeModal() {
                if (window.currentModal) {
                    document.body.removeChild(window.currentModal);
                    window.currentModal = null;
                }
            }
        </script>
    </body>
</html>
