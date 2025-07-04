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
    <body class="bg-gray-800 text-white font-sans antialiased">
    <div class="min-h-screen">
        <!-- Modern Navbar -->
        @include('components.modern-navbar')

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
             x-data="seatSelection({{ $trip->id }}, {{ $trip->price }})"
             x-init="loadSeats()">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Seat Map -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-900 rounded-lg shadow-lg p-6">
                        <div class="text-center mb-4">
                            <h2 class="text-2xl font-bold text-green-400">SCREEN SIDE</h2>
                        </div>
                        <div class="bg-gray-800 rounded-lg p-6">
                            <div class="flex justify-between items-center mb-4 px-4">
                                <div class="w-16 h-16"></div>
                                <div class="text-gray-500 font-bold">Driver</div>
                            </div>
                            <!-- Seat Grid -->
                            <div class="space-y-4">
                                <template x-for="row in seatRows" :key="row[0] ? row[0].id : Math.random()">
                                    <div class="flex justify-between items-center">
                                        <!-- Left side -->
                                        <div class="flex space-x-2">
                                            <div x-show="row[0]" @click="toggleSeat(row[0])" :class="getSeatClass(row[0])" class="w-12 h-12 rounded-lg flex items-center justify-center text-sm font-medium transition duration-150" x-text="row[0] ? row[0].seat_number : ''"></div>
                                            <div x-show="row[1]" @click="toggleSeat(row[1])" :class="getSeatClass(row[1])" class="w-12 h-12 rounded-lg flex items-center justify-center text-sm font-medium transition duration-150" x-text="row[1] ? row[1].seat_number : ''"></div>
                                        </div>
                                        <!-- Right side -->
                                        <div class="flex space-x-2">
                                            <div x-show="row[2]" @click="toggleSeat(row[2])" :class="getSeatClass(row[2])" class="w-12 h-12 rounded-lg flex items-center justify-center text-sm font-medium transition duration-150" x-text="row[2] ? row[2].seat_number : ''"></div>
                                            <div x-show="row[3]" @click="toggleSeat(row[3])" :class="getSeatClass(row[3])" class="w-12 h-12 rounded-lg flex items-center justify-center text-sm font-medium transition duration-150" x-text="row[3] ? row[3].seat_number : ''"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                             <div class="flex justify-between items-center mt-4 px-4">
                                <div class="w-16 h-16"></div>
                                <div class="text-gray-500 font-bold">Door</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-900 rounded-lg shadow-lg p-6 sticky top-8">
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-300 mb-2">Seat Status</h4>
                            <div class="flex items-center mb-2">
                                <div class="w-4 h-4 bg-gray-600 rounded mr-2"></div>
                                <span class="text-gray-400">Unavailable</span>
                            </div>
                            <div class="flex items-center mb-2">
                                <div class="w-4 h-4 bg-red-600 rounded mr-2"></div>
                                <span class="text-gray-400">Sold Out</span>
                            </div>
                            <div class="flex items-center mb-2">
                                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                                <span class="text-gray-400">Available</span>
                            </div>
                            <div class="flex items-center mb-2">
                                <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                                <span class="text-gray-400">Your Seat</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                                <span class="text-gray-400">Reserved</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-700 pt-4 mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-400">Number of Seats</span>
                                <span x-text="selectedSeats.length" class="text-white font-bold"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Total Cost</span>
                                <span x-text="'Rs. ' + (selectedSeats.length * seatPrice).toLocaleString()" class="text-green-400 font-bold text-xl"></span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button @click="proceedToPayment()" :disabled="selectedSeats.length === 0"
                                    class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-medium transition duration-150 disabled:bg-gray-600 disabled:cursor-not-allowed">
                                BUY TICKETS
                            </button>
                            <button @click="bookNow()" :disabled="selectedSeats.length === 0"
                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-lg font-medium transition duration-150 disabled:bg-gray-600 disabled:cursor-not-allowed">
                                RESERVE
                            </button>
                            <button @click="resetSelection()"
                                    class="w-full bg-gray-700 hover:bg-gray-600 text-white py-3 px-4 rounded-lg font-medium transition duration-150">
                                RESET
                            </button>
                            
                            <!-- Ticket History Button -->
                            @auth
                                <div class="pt-3 border-t border-gray-700">
                                    @include('components.ticket-history-modal')
                                </div>
                            @endauth
                        </div>
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
                    seatRows: [],
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
                            this.generateSeatRows();
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

                    generateSeatRows() {
                        const rows = [];
                        let currentRow = [];
                        if (!this.seats) return;
                        for (let i = 0; i < this.seats.length; i++) {
                            currentRow.push(this.seats[i]);
                            if (currentRow.length === 4) {
                                rows.push(currentRow);
                                currentRow = [];
                            }
                        }
                        if (currentRow.length > 0) {
                            rows.push(currentRow);
                        }
                        this.seatRows = rows;
                    },

                    getSeatClass(seat) {
                        if (!seat) return '';
                        let baseClass = 'cursor-pointer hover:scale-105 ';

                        if (!seat.available) {
                            return baseClass + 'bg-red-600 text-white cursor-not-allowed hover:scale-100';
                        } else if (this.selectedSeats.some(s => s.id === seat.id)) {
                            return baseClass + 'bg-blue-500 text-white hover:bg-blue-600';
                        } else {
                            return baseClass + 'bg-green-500 text-white hover:bg-green-600';
                        }
                    },

                    resetSelection() {
                        this.selectedSeats = [];
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
