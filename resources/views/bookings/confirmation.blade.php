<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Booking Confirmed - {{ $booking->booking_reference }} | BooknGo</title>
        <meta name="description" content="Your bus booking has been confirmed. Booking reference: {{ $booking->booking_reference }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 font-sans antialiased">
        <!-- Modern Navbar -->
        @include('components.modern-navbar')

        <!-- Success Banner -->
        <div class="bg-green-600 text-white py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex items-center justify-center mb-2">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h1 class="text-2xl font-bold">Booking Confirmed!</h1>
                </div>
                <p class="text-green-100">Your bus ticket has been successfully booked. Please save this confirmation for your records.</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Digital Ticket -->
            <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden mb-8">
                <!-- Ticket Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-bold mb-1">Bus Ticket</h2>
                            <p class="text-blue-100">{{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-blue-100">Booking Reference</p>
                            <p class="text-lg font-bold">{{ $booking->booking_reference }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ticket Body -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Trip Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Trip Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date</span>
                                    <span class="font-medium">{{ $booking->trip->departure_datetime->format('l, F j, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Departure Time</span>
                                    <span class="font-medium">{{ $booking->trip->departure_datetime->format('H:i') }}</span>
                                </div>
                                @if($booking->trip->arrival_time)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Arrival Time</span>
                                        <span class="font-medium">{{ $booking->trip->arrival_time->format('H:i') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Bus</span>
                                    <span class="font-medium">{{ $booking->trip->bus->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Operator</span>
                                    <span class="font-medium">{{ $booking->trip->bus->operator->user->name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Passenger Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Passenger Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Name</span>
                                    <span class="font-medium">{{ $booking->passenger_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phone</span>
                                    <span class="font-medium">{{ $booking->passenger_phone }}</span>
                                </div>
                                @if($booking->passenger_email)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Email</span>
                                        <span class="font-medium">{{ $booking->passenger_email }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Seats</span>
                                    <span class="font-medium">{{ $booking->seat_numbers }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Amount</span>
                                    <span class="font-bold text-lg text-green-600">Rs. {{ number_format($booking->total_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="mb-4 md:mb-0">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Booking Code</h4>
                                <div class="bg-gray-100 rounded-lg px-4 py-2 font-mono text-lg font-bold text-center">
                                    {{ $booking->booking_code }}
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Show this code to the conductor</p>
                            </div>
                            
                            <!-- QR Code Placeholder -->
                            <div class="flex flex-col items-center">
                                <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center mb-2">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-500">QR Code</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-2 sm:space-y-0">
                        <div class="text-sm text-gray-600">
                            <p>Booked on {{ $booking->created_at->format('F j, Y \a\t H:i') }}</p>
                            <p>Status: <span class="font-medium text-green-600">{{ ucfirst($booking->status) }}</span></p>
                        </div>
                        <div class="flex space-x-3">
                            <button 
                                onclick="window.print()" 
                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print Ticket
                            </button>
                            <button 
                                onclick="downloadTicket()" 
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Important Information -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Important Travel Information</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Please arrive at the departure point at least 30 minutes before departure time</li>
                                <li>Carry a valid government-issued ID proof during travel</li>
                                <li>Show your booking code ({{ $booking->booking_code }}) to the conductor</li>
                                <li>Keep this ticket with you throughout the journey</li>
                                <li>Cancellation is allowed up to 2 hours before departure</li>
                                @if($booking->trip->is_festival_fare)
                                    <li>Festival fare rates are applicable for this booking</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:justify-center sm:space-x-4 space-y-4 sm:space-y-0">
                <a 
                    href="{{ url('/') }}" 
                    class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                >
                    Book Another Trip
                </a>
                
                @auth
                    <a 
                        href="{{ route('bookings.index') }}" 
                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                    >
                        View All Bookings
                    </a>
                @endauth

                @if($booking->canBeCancelled())
                    <button 
                        onclick="cancelBooking()" 
                        class="inline-flex items-center justify-center px-6 py-3 border border-red-300 text-base font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                    >
                        Cancel Booking
                    </button>
                @endif
            </div>
        </div>

        <!-- JavaScript -->
        <script>
            function downloadTicket() {
                // This would generate and download a PDF ticket
                alert('PDF download functionality will be implemented soon.');
            }

            function cancelBooking() {
                if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
                    // This would handle booking cancellation
                    alert('Booking cancellation functionality will be implemented soon.');
                }
            }

            // Clear session storage after successful booking
            sessionStorage.removeItem('selectedSeats');
        </script>

        <!-- Print Styles -->
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }
                .bg-white, .bg-white * {
                    visibility: visible;
                }
                .bg-white {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }
                header, .bg-yellow-50, .flex.flex-col.sm\\:flex-row.sm\\:justify-center {
                    display: none !important;
                }
            }
        </style>
    </body>
</html>
