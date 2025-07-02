<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Select Seats - {{ $trip->route->fromCity->name }} to {{ $trip->route->toCity->name }} | BooknGo</title>
        <meta name="description" content="Select your seats for {{ $trip->bus->name }} from {{ $trip->route->fromCity->name }} to {{ $trip->route->toCity->name }}">

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
                        <span class="ml-2 text-sm text-gray-500">Festival Bus Booking</span>
                    </div>
                    @if (Route::has('login'))
                        <nav class="flex items-center space-x-4">
                            @auth
                                <a
                                    href="{{ url('/dashboard') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                                >
                                    Dashboard
                                </a>
                            @else
                                <a
                                    href="{{ route('login') }}"
                                    class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out"
                                >
                                    Log in
                                </a>
                                @if (Route::has('register'))
                                    <a
                                        href="{{ route('register') }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                                    >
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </div>
        </header>

        <!-- Trip Info Bar -->
        <div class="bg-blue-600 text-white py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center space-x-6">
                        <div>
                            <h1 class="text-lg font-semibold">{{ $trip->route->fromCity->name }} → {{ $trip->route->toCity->name }}</h1>
                            <p class="text-blue-100 text-sm">{{ $trip->departure_datetime->format('l, F j, Y • H:i') }}</p>
                        </div>
                        <div class="hidden md:block w-px h-8 bg-blue-400"></div>
                        <div>
                            <p class="text-sm font-medium">{{ $trip->bus->name }}</p>
                            <p class="text-blue-100 text-sm">{{ $trip->bus->operator->user->name }}</p>
                        </div>
                    </div>
                    <div class="mt-2 md:mt-0">
                        <span class="text-2xl font-bold">Rs. {{ number_format($trip->price) }}</span>
                        <span class="text-blue-100 text-sm ml-1">per seat</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
             x-data="seatSelection({{ $trip->id }}, {{ $trip->price }})"
             x-init="loadSeats()">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Seat Map -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Select Your Seats</h2>
                            <div class="flex items-center space-x-4 text-sm">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                                    <span class="text-gray-600">Available</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                                    <span class="text-gray-600">Selected</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-gray-400 rounded mr-2"></div>
                                    <span class="text-gray-600">Booked</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bus Layout -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <!-- Driver Section -->
                            <div class="flex justify-end mb-4">
                                <div class="bg-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700">
                                    Driver
                                </div>
                            </div>

                            <!-- Seat Grid -->
                            <div class="grid gap-2" style="grid-template-columns: repeat(4, 1fr);">
                                <template x-for="seat in seats" :key="seat.id">
                                    <div
                                        @click="toggleSeat(seat)"
                                        :class="getSeatClass(seat)"
                                        class="w-12 h-12 rounded-lg flex items-center justify-center text-sm font-medium transition duration-150"
                                        :title="seat.available ? 'Click to select' : 'Seat not available'"
                                        x-text="seat.seat_number">
                                    </div>
                                </template>
                            </div>

                            <!-- Loading State -->
                            <div x-show="loading" class="text-center py-8">
                                <div class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading seats...
                                </div>
                            </div>

                            <!-- Error State -->
                            <div x-show="error" class="text-center py-8">
                                <div class="text-red-600 mb-4" x-text="error"></div>
                                <button @click="loadSeats()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150">
                                    Retry
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h3>

                        <!-- Selected Seats -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Seats</h4>
                            <div x-show="selectedSeats.length === 0" class="text-gray-500 text-sm">
                                No seats selected
                            </div>
                            <div x-show="selectedSeats.length > 0" class="text-blue-600 text-sm font-medium">
                                Seat <span x-text="selectedSeats.map(s => s.seat_number).join(', ')"></span>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="border-t border-gray-200 pt-4 mb-6">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Seat Price</span>
                                <span class="text-gray-900">Rs. {{ number_format($trip->price) }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Number of Seats</span>
                                <span x-text="selectedSeats.length" class="text-gray-900"></span>
                            </div>
                            @if($trip->is_festival_fare)
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-orange-600">Festival Fare Applied</span>
                                    <span class="text-orange-600">✓</span>
                                </div>
                            @endif
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <div class="flex justify-between font-semibold">
                                    <span class="text-gray-900">Total Amount</span>
                                    <span x-text="'Rs. ' + (selectedSeats.length * seatPrice).toLocaleString()" class="text-gray-900"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Continue Button -->
                        <button
                            @click="proceedToBooking()"
                            :disabled="selectedSeats.length === 0"
                            :class="selectedSeats.length > 0 ?
                                'w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition duration-150 cursor-pointer' :
                                'w-full bg-gray-300 text-gray-500 py-3 px-4 rounded-lg font-medium cursor-not-allowed transition duration-150'"
                            x-text="selectedSeats.length > 0 ? 'Continue to Passenger Details' : 'Select seats to continue'"
                        >
                        </button>

                        <!-- Trip Details -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Trip Details</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Bus Type</span>
                                    <span class="text-gray-900">{{ ucfirst($trip->bus->type) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Seats</span>
                                    <span class="text-gray-900">{{ $trip->bus->total_seats }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Available</span>
                                    <span class="text-green-600">{{ $trip->available_seats_count }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bus Features -->
                        @if($trip->bus->features && count($trip->bus->features) > 0)
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Bus Features</h4>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($trip->bus->features as $feature)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Alpine.js Component -->
        <script>
            function seatSelection(tripId, seatPrice) {
                return {
                    tripId: tripId,
                    seatPrice: seatPrice,
                    selectedSeats: [],
                    seats: [],
                    loading: true,
                    error: null,
                    isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},

                    async loadSeats() {
                        this.loading = true;
                        this.error = null;

                        try {
                            const response = await fetch(`/trips/${this.tripId}/seat-availability`);
                            if (!response.ok) {
                                throw new Error('Failed to load seats');
                            }
                            const data = await response.json();
                            this.seats = data.seats;
                        } catch (error) {
                            console.error('Error loading seats:', error);
                            this.error = 'Error loading seats. Please try again.';
                        } finally {
                            this.loading = false;
                            // Restore selected seats if user came back after login
                            this.restoreSelectedSeats();
                        }
                    },

                    restoreSelectedSeats() {
                        // Check if there are stored selected seats for this trip
                        const storedSeats = sessionStorage.getItem('selectedSeats');
                        const storedTripId = sessionStorage.getItem('selectedTripId');

                        if (storedSeats && storedTripId && parseInt(storedTripId) === this.tripId) {
                            try {
                                const seatIds = JSON.parse(storedSeats).map(seat => seat.id);
                                this.selectedSeats = this.seats.filter(seat =>
                                    seatIds.includes(seat.id) && seat.available
                                );

                                // Clear stored data after restoration
                                sessionStorage.removeItem('selectedSeats');
                                sessionStorage.removeItem('selectedTripId');

                                if (this.selectedSeats.length > 0) {
                                    window.showNotification('Your previously selected seats have been restored.', 'success');
                                }
                            } catch (error) {
                                console.error('Error restoring selected seats:', error);
                            }
                        }
                    },

                    toggleSeat(seat) {
                        if (!seat.available) return;

                        const seatIndex = this.selectedSeats.findIndex(s => s.id === seat.id);

                        if (seatIndex > -1) {
                            // Remove seat
                            this.selectedSeats.splice(seatIndex, 1);
                        } else {
                            // Add seat (limit to 4 seats per booking)
                            if (this.selectedSeats.length < 4) {
                                this.selectedSeats.push(seat);
                            } else {
                                window.showNotification('You can select maximum 4 seats per booking.', 'warning');
                                return;
                            }
                        }
                    },

                    getSeatClass(seat) {
                        let baseClass = 'cursor-pointer hover:scale-105 ';

                        if (!seat.available) {
                            return baseClass + 'bg-gray-400 text-white cursor-not-allowed hover:scale-100';
                        } else if (this.selectedSeats.some(s => s.id === seat.id)) {
                            return baseClass + 'bg-blue-500 text-white hover:bg-blue-600';
                        } else {
                            return baseClass + 'bg-green-500 text-white hover:bg-green-600';
                        }
                    },

                    proceedToBooking() {
                        if (this.selectedSeats.length === 0) {
                            window.showNotification('Please select at least one seat.', 'warning');
                            return;
                        }

                        // Check if user is authenticated
                        if (!this.isAuthenticated) {
                            // Store selected seats in session storage to preserve selection after login
                            sessionStorage.setItem('selectedSeats', JSON.stringify(this.selectedSeats));
                            sessionStorage.setItem('selectedTripId', this.tripId);

                            // Show login prompt
                            if (confirm('You need to login to proceed with booking. Would you like to login now?')) {
                                // Redirect to login with return URL
                                const returnUrl = encodeURIComponent(window.location.href);
                                window.location.href = `/login?redirect=${returnUrl}`;
                            }
                            return;
                        }

                        // Store selected seats in session storage for the booking form
                        sessionStorage.setItem('selectedSeats', JSON.stringify(this.selectedSeats));

                        // Redirect to booking form
                        window.location.href = `/trips/${this.tripId}/book?seats=${this.selectedSeats.map(s => s.id).join(',')}`;
                    }
                }
            }
        </script>
    </body>
</html>
