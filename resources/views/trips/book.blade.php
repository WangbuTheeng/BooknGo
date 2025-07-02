<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Passenger Details - {{ $trip->route->fromCity->name }} to {{ $trip->route->toCity->name }} | BooknGo</title>
        <meta name="description" content="Enter passenger details for your bus booking from {{ $trip->route->fromCity->name }} to {{ $trip->route->toCity->name }}">

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

        <!-- Progress Bar -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-center space-x-8">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                            ✓
                        </div>
                        <span class="ml-2 text-sm font-medium text-green-600">Select Seats</span>
                    </div>
                    <div class="w-16 h-px bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                            2
                        </div>
                        <span class="ml-2 text-sm font-medium text-blue-600">Passenger Details</span>
                    </div>
                    <div class="w-16 h-px bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-500 text-sm font-medium">
                            3
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Payment</span>
                    </div>
                    <div class="w-16 h-px bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-500 text-sm font-medium">
                            4
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Confirmation</span>
                    </div>
                </div>
            </div>
        </div>

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
                        <div class="hidden md:block w-px h-8 bg-blue-400"></div>
                        <div>
                            <p class="text-sm font-medium">Selected Seats</p>
                            <p class="text-blue-100 text-sm" id="selected-seats-display">Loading...</p>
                        </div>
                    </div>
                    <div class="mt-2 md:mt-0">
                        <span class="text-2xl font-bold" id="total-amount-display">Rs. 0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Passenger Information</h2>
                    <p class="text-gray-600">Please provide the passenger details for your booking.</p>
                </div>

                <form method="POST" action="{{ route('trips.book.store', $trip) }}" id="booking-form">
                    @csrf
                    
                    <!-- Hidden fields for selected seats (as array) -->
                    <div id="seat-ids-container"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Passenger Name -->
                        <div class="md:col-span-2">
                            <label for="passenger_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="passenger_name" 
                                id="passenger_name" 
                                required
                                value="{{ old('passenger_name', auth()->user()->name ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                placeholder="Enter passenger's full name"
                            >
                            @error('passenger_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="passenger_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                name="passenger_phone" 
                                id="passenger_phone" 
                                required
                                value="{{ old('passenger_phone') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                placeholder="98XXXXXXXX"
                                pattern="[0-9]{10}"
                            >
                            @error('passenger_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">10-digit mobile number</p>
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="passenger_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-gray-400">(Optional)</span>
                            </label>
                            <input 
                                type="email" 
                                name="passenger_email" 
                                id="passenger_email"
                                value="{{ old('passenger_email', auth()->user()->email ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                placeholder="passenger@example.com"
                            >
                            @error('passenger_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">For booking confirmation and updates</p>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mt-8">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    id="terms" 
                                    name="terms" 
                                    type="checkbox" 
                                    required
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                >
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="text-gray-700">
                                    I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms and Conditions</a> 
                                    and <a href="#" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>
                                    <span class="text-red-500">*</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Important Information</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Please arrive at the departure point at least 30 minutes before departure time</li>
                                        <li>Carry a valid ID proof during travel</li>
                                        <li>Cancellation is allowed up to 2 hours before departure</li>
                                        <li>Festival fare rates are applicable during peak season</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                        <a 
                            href="{{ route('trips.select-seats', $trip) }}" 
                            class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Seat Selection
                        </a>
                        
                        <button 
                            type="submit"
                            class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                        >
                            Proceed to Payment
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get selected seats from URL parameters or session storage
                const urlParams = new URLSearchParams(window.location.search);
                const seatIds = urlParams.get('seats');
                
                if (seatIds) {
                    // Convert comma-separated string to array for form submission
                    const seatIdArray = seatIds.split(',');
                    setSeatIdsAsArray(seatIdArray);
                    loadSeatDetails(seatIdArray);
                } else {
                    // Fallback to session storage
                    const selectedSeats = JSON.parse(sessionStorage.getItem('selectedSeats') || '[]');
                    if (selectedSeats.length > 0) {
                        const seatIdArray = selectedSeats.map(seat => seat.id);
                        setSeatIdsAsArray(seatIdArray);
                        displaySeatInfo(selectedSeats);
                    } else {
                        // Redirect back to seat selection if no seats selected
                        window.location.href = '{{ route("trips.select-seats", $trip) }}';
                    }
                }
            });

            function loadSeatDetails(seatIds) {
                // This would typically fetch seat details from the server
                // For now, we'll use the session storage data
                const selectedSeats = JSON.parse(sessionStorage.getItem('selectedSeats') || '[]');
                displaySeatInfo(selectedSeats);
            }

            function setSeatIdsAsArray(seatIdArray) {
                // Clear existing hidden inputs
                const container = document.getElementById('seat-ids-container');
                container.innerHTML = '';

                // Create hidden input for each seat ID
                seatIdArray.forEach(seatId => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'seat_ids[]';
                    input.value = seatId;
                    container.appendChild(input);
                });
            }

            function displaySeatInfo(seats) {
                const seatNumbers = seats.map(seat => seat.seat_number).join(', ');
                const totalAmount = seats.length * {{ $trip->price }};

                document.getElementById('selected-seats-display').textContent = `Seat ${seatNumbers}`;
                document.getElementById('total-amount-display').textContent = `Rs. ${totalAmount.toLocaleString()}`;
            }
        </script>
    </body>
</html>
