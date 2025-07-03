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

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <!-- Book Now Button -->
                            <button
                                @click="bookNow()"
                                :disabled="selectedSeats.length === 0"
                                :class="selectedSeats.length > 0 ?
                                    'w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition duration-150 cursor-pointer' :
                                    'w-full bg-gray-300 text-gray-500 py-3 px-4 rounded-lg font-medium cursor-not-allowed transition duration-150'"
                            >
                                <div class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span x-text="selectedSeats.length > 0 ? 'Book Now (2 Hours Hold)' : 'Select seats to book'"></span>
                                </div>
                            </button>
                            
                            <!-- Proceed to Payment Button -->
                            <button
                                @click="proceedToPayment()"
                                :disabled="selectedSeats.length === 0"
                                :class="selectedSeats.length > 0 ?
                                    'w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition duration-150 cursor-pointer' :
                                    'w-full bg-gray-300 text-gray-500 py-3 px-4 rounded-lg font-medium cursor-not-allowed transition duration-150'"
                            >
                                <div class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span x-text="selectedSeats.length > 0 ? 'Proceed to Payment' : 'Select seats to continue'"></span>
                                </div>
                            </button>
                        </div>
                        
                        <!-- Explanation Text -->
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-600">
                            <p class="mb-2"><strong>Book Now:</strong> Reserve seats for 2 hours without payment. You can pay later.</p>
                            <p><strong>Proceed to Payment:</strong> Complete booking with immediate payment.</p>
                        </div>

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

                    async bookNow() {
                        if (this.selectedSeats.length === 0) {
                            window.showNotification('Please select at least one seat.', 'warning');
                            return;
                        }

                        // Check if user is authenticated
                        if (!this.isAuthenticated) {
                            this.handleAuthRequired();
                            return;
                        }

                        // Create booking with 2-hour hold
                        await this.createBooking('hold');
                    },

                    async proceedToPayment() {
                        if (this.selectedSeats.length === 0) {
                            window.showNotification('Please select at least one seat.', 'warning');
                            return;
                        }

                        // Check if user is authenticated
                        if (!this.isAuthenticated) {
                            this.handleAuthRequired();
                            return;
                        }

                        // Create booking and go to payment
                        await this.createBooking('payment');
                    },

                    handleAuthRequired() {
                        // Store selected seats in session storage to preserve selection after login
                        sessionStorage.setItem('selectedSeats', JSON.stringify(this.selectedSeats));
                        sessionStorage.setItem('selectedTripId', this.tripId);

                        // Show booking options modal
                        this.showBookingOptionsModal();
                    },

                    showBookingOptionsModal() {
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
                                    <p class="text-gray-600">You need to be logged in to book tickets. Please choose an option:</p>
                                </div>
                                
                                <div class="space-y-3">
                                    <button onclick="this.redirectToLogin()" 
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition duration-150 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 0v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                        Login to Existing Account
                                    </button>
                                    
                                    <button onclick="this.redirectToRegister()" 
                                            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition duration-150 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                        </svg>
                                        Create New Account
                                    </button>
                                    
                                    <button onclick="this.closeModal()" 
                                            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 px-4 rounded-lg font-medium transition duration-150">
                                        Cancel
                                    </button>
                                </div>
                                
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Your seat selection will be preserved
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Add event listeners
                        modalOverlay.querySelector('[onclick="this.redirectToLogin()"]').onclick = () => this.redirectToLogin();
                        modalOverlay.querySelector('[onclick="this.redirectToRegister()"]').onclick = () => this.redirectToRegister();
                        modalOverlay.querySelector('[onclick="this.closeModal()"]').onclick = () => this.closeModal(modalOverlay);
                        
                        // Close modal when clicking outside
                        modalOverlay.onclick = (e) => {
                            if (e.target === modalOverlay) {
                                this.closeModal(modalOverlay);
                            }
                        };
                        
                        document.body.appendChild(modalOverlay);
                    },

                    redirectToLogin() {
                        const returnUrl = encodeURIComponent(window.location.href);
                        window.location.href = `/login?redirect=${returnUrl}`;
                    },

                    redirectToRegister() {
                        const returnUrl = encodeURIComponent(window.location.href);
                        window.location.href = `/register?redirect=${returnUrl}`;
                    },

                    closeModal(modalOverlay) {
                        if (modalOverlay) {
                            document.body.removeChild(modalOverlay);
                        }
                    },

                    async createBooking(action) {
                        // Show loading state
                        const loadingMessage = action === 'hold' ? 'Creating booking...' : 'Creating booking and proceeding to payment...';
                        
                        try {
                            // Get CSRF token
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            
                            // Prepare form data
                            const formData = new FormData();
                            formData.append('_token', csrfToken);
                            formData.append('passenger_name', 'Temporary Booking'); // Will be updated in booking form
                            formData.append('passenger_phone', 'TBD'); // Will be updated in booking form
                            formData.append('action', action); // 'hold' or 'payment'
                            this.selectedSeats.forEach(seat => {
                                formData.append('seat_ids[]', seat.id);
                            });

                            const response = await fetch(`/trips/${this.tripId}/book`, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const data = await response.json();

                            if (!response.ok) {
                                throw new Error(data.message || 'Failed to create booking');
                            }

                            if (data.success) {
                                if (action === 'hold') {
                                    // Show success message and redirect to booking details
                                    window.showNotification('Seats reserved for 2 hours! Complete your booking details.', 'success');
                                    window.location.href = `/bookings/${data.booking.id}`;
                                } else {
                                    // Redirect to payment page
                                    window.location.href = `/bookings/${data.booking.id}/payment`;
                                }
                            } else {
                                throw new Error(data.message || 'Failed to create booking');
                            }
                        } catch (error) {
                            console.error('Error creating booking:', error);
                            window.showNotification(error.message || 'Failed to create booking. Please try again.', 'error');
                        }
                    },

                    // Legacy method for backward compatibility
                    proceedToBooking() {
                        this.proceedToPayment();
                    }
                }
            }
        </script>
    </body>
</html>
