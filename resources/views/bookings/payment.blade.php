<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Payment - {{ $booking->booking_reference }} | BooknGo</title>

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
                <div class="flex items-center space-x-4">
                    <a href="{{ route('bookings.show', $booking) }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Complete Payment</h1>
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
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                            ✓
                        </div>
                        <span class="ml-2 text-sm font-medium text-green-600">Passenger Details</span>
                    </div>
                    <div class="w-16 h-px bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                            3
                        </div>
                        <span class="ml-2 text-sm font-medium text-blue-600">Payment</span>
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

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" 
             x-data="{ 
                expiresAt: new Date('{{ $booking->expires_at }}').getTime(),
                now: Date.now(),
                remainingTime: '',
                updateRemainingTime() {
                    const diff = this.expiresAt - Date.now();
                    if (diff <= 0) {
                        this.remainingTime = 'EXPIRED';
                        return;
                    }
                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    this.remainingTime = hours + 'h ' + minutes + 'm ' + seconds + 's';
                }
             }" 
             x-init="updateRemainingTime(); setInterval(() => updateRemainingTime(), 1000)">
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

            <!-- Expiry Warning -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">
                            Complete payment within 2 hours
                        </h3>
                        <div class="mt-1 text-sm text-yellow-700">
                            <p>Your booking will expire in <span x-text="remainingTime" class="font-medium"></span> if payment is not completed.</p>
                            <p class="mt-1">Expires at: {{ $booking->expires_at->format('M j, Y • H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Payment Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Complete Payment</h2>
                            <p class="text-gray-600">Choose your preferred payment method to complete the booking.</p>
                        </div>

                        <form method="POST" action="{{ route('payments.process', $booking) }}" id="payment-form">
                            @csrf

                            <input type="hidden" name="amount" value="{{ $booking->total_amount }}">

                            <!-- Payment Methods -->
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
                                <div class="space-y-4">
                                    <!-- eSewa -->
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                        <input type="radio" name="payment_method" value="eSewa" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" required>
                                        <div class="ml-4 flex items-center">
                                            <div class="w-12 h-8 bg-green-600 rounded flex items-center justify-center text-white text-xs font-bold">
                                                eSewa
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">eSewa Digital Wallet</p>
                                                <p class="text-xs text-gray-500">Pay securely with your eSewa account</p>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Khalti -->
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                        <input type="radio" name="payment_method" value="Khalti" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" required>
                                        <div class="ml-4 flex items-center">
                                            <div class="w-12 h-8 bg-purple-600 rounded flex items-center justify-center text-white text-xs font-bold">
                                                Khalti
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">Khalti Digital Wallet</p>
                                                <p class="text-xs text-gray-500">Pay with Khalti mobile banking</p>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Stripe -->
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                        <input type="radio" name="payment_method" value="Stripe" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" required>
                                        <div class="ml-4 flex items-center">
                                            <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center text-white text-xs font-bold">
                                                Stripe
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">Credit/Debit Card</p>
                                                <p class="text-xs text-gray-500">Pay securely with your card via Stripe</p>
                                            </div>
                                        </div>
                                    </label>

                                    <!-- Cash Payment -->
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-150">
                                        <input type="radio" name="payment_method" value="Cash" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" required>
                                        <div class="ml-4 flex items-center">
                                            <div class="w-12 h-8 bg-gray-600 rounded flex items-center justify-center text-white text-xs font-bold">
                                                Cash
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">Cash Payment</p>
                                                <p class="text-xs text-gray-500">Pay cash at the bus counter</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                                <a
                                    href="{{ route('trips.book', $booking->trip) }}"
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Back to Details
                                </a>

                                <button
                                    type="submit"
                                    class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                                >
                                    Complete Payment
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h3>

                        <!-- Trip Info -->
                        <div class="mb-6">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $booking->trip->departure_datetime->format('M j, Y • H:i') }}</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">{{ $booking->trip->bus->name }}</p>
                        </div>

                        <!-- Passenger Info -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Passenger</h4>
                            <p class="text-sm text-gray-900">{{ $booking->passenger_name }}</p>
                            <p class="text-sm text-gray-600">{{ $booking->passenger_phone }}</p>
                        </div>

                        <!-- Seats -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Seats</h4>
                            <p class="text-sm text-gray-900">{{ $booking->seat_numbers }}</p>
                        </div>

                        <!-- Booking Reference -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Booking Reference</h4>
                            <p class="text-sm font-mono text-gray-900">{{ $booking->booking_reference }}</p>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Seat Price</span>
                                <span class="text-gray-900">Rs. {{ number_format($booking->trip->price) }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Number of Seats</span>
                                <span class="text-gray-900">{{ $booking->bookingSeats->count() }}</span>
                            </div>
                            @if($booking->trip->is_festival_fare)
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-orange-600">Festival Fare Applied</span>
                                    <span class="text-orange-600">✓</span>
                                </div>
                            @endif
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <div class="flex justify-between font-semibold">
                                    <span class="text-gray-900">Total Amount</span>
                                    <span class="text-gray-900">Rs. {{ number_format($booking->total_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Secure Payment</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Your payment information is encrypted and secure. We do not store your payment details.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
