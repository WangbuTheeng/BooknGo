<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Booking Details</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        @include('layouts.navigation')

        <!-- Page Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('bookings.index') }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">Booking Details</h1>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($booking->status === 'booked')
                            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print Ticket
                            </button>
                        @endif
                        @if($booking->status === 'booked' && $booking->trip->departure_datetime > now())
                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancel Booking
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Booking Reference Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">{{ $booking->booking_reference }}</h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $booking->status === 'booked' ? 'bg-green-100 text-green-800' : ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            @if($booking->status === 'booked')
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($booking->status === 'cancelled')
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>

                <!-- Trip and Passenger Information -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Trip Details -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0l-1 12a2 2 0 002 2h8a2 2 0 002-2L13 7M9 7h6"></path>
                                    </svg>
                                    Trip Information
                                </h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Route</span>
                                        <span class="text-sm text-gray-900 font-semibold">{{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Departure</span>
                                        <span class="text-sm text-gray-900">{{ $booking->trip->departure_datetime->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Time</span>
                                        <span class="text-sm text-gray-900">{{ $booking->trip->departure_datetime->format('H:i') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Bus</span>
                                        <span class="text-sm text-gray-900">{{ $booking->trip->bus->name }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Type</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $booking->trip->bus->type }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Seats Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Selected Seats
                                </h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($booking->bookingSeats as $bookingSeat)
                                            <div class="inline-flex items-center px-3 py-2 rounded-lg bg-green-100 text-green-800 text-sm font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Seat {{ $bookingSeat->seat->seat_number }}
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3 text-sm text-gray-600">
                                        Total {{ $booking->bookingSeats->count() }} seat(s) selected
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Passenger and Payment Details -->
                        <div class="space-y-6">
                            <!-- Passenger Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Passenger Details
                                </h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Name</span>
                                        <span class="text-sm text-gray-900 font-semibold">{{ $booking->passenger_name }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Phone</span>
                                        <span class="text-sm text-gray-900">{{ $booking->passenger_phone }}</span>
                                    </div>
                                    @if($booking->passenger_email)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-500">Email</span>
                                            <span class="text-sm text-gray-900">{{ $booking->passenger_email }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Payment Summary -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Payment Information
                                </h3>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Total Amount</span>
                                        <span class="text-lg font-bold text-gray-900">Rs. {{ number_format($booking->total_amount, 2) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-500">Booking Date</span>
                                        <span class="text-sm text-gray-900">{{ $booking->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if($booking->payments->count() > 0)
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Payment History
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($booking->payments as $payment)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-150">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                @if($payment->method === 'eSewa')
                                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                                        </svg>
                                                    </div>
                                                @elseif($payment->method === 'Khalti')
                                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $payment->method }}</p>
                                                <p class="text-sm text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-semibold text-gray-900">Rs. {{ number_format($payment->amount, 2) }}</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->payment_status === 'completed' ? 'bg-green-100 text-green-800' : ($payment->payment_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($payment->payment_status) }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($payment->transaction_id)
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">Transaction ID:</span> {{ $payment->transaction_id }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <!-- Include notification component -->
    <x-notification />
</body>
</html>
