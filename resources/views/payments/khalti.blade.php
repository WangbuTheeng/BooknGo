@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z"/>
                    <path d="M6 8h8v2H6V8zm0 3h8v1H6v-1z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Khalti Payment</h1>
            <p class="text-gray-600">You will be redirected to Khalti to complete your payment</p>
        </div>

        <!-- Payment Details -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Payment Details</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Booking Reference:</span>
                    <span class="font-medium">{{ $payment->booking->booking_reference }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Route:</span>
                    <span class="font-medium">
                        {{ $payment->booking->trip->route->fromCity->name }} 
                        → {{ $payment->booking->trip->route->toCity->name }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Departure:</span>
                    <span class="font-medium">
                        {{ $payment->booking->trip->departure_date->format('M j, Y') }} 
                        at {{ $payment->booking->trip->departure_time->format('g:i A') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Seats:</span>
                    <span class="font-medium">
                        {{ $payment->booking->bookingSeats->pluck('seat.seat_number')->join(', ') }}
                    </span>
                </div>
                <div class="flex justify-between border-t pt-2 mt-2">
                    <span class="text-gray-600 font-medium">Total Amount:</span>
                    <span class="font-bold text-lg text-purple-600">NPR {{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>
        </div>

        @if(isset($result) && $result['status'] === 'success')
            <!-- Auto-redirect form to Khalti -->
            <form id="khalti-form" action="{{ $result['payment_url'] }}" method="GET" style="display: none;">
                <input type="hidden" name="pidx" value="{{ $result['pidx'] }}">
            </form>

            <div class="text-center">
                <div class="bg-purple-100 border border-purple-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-center mb-2">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-600 mr-3"></div>
                        <span class="text-purple-800 font-medium">Redirecting to Khalti...</span>
                    </div>
                    <p class="text-purple-700 text-sm">Please wait while we redirect you to complete your payment.</p>
                </div>

                <button onclick="document.getElementById('khalti-form').submit()" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"/>
                        </svg>
                        Continue to Khalti
                    </div>
                </button>
            </div>

            <script>
                // Auto-redirect after 3 seconds
                setTimeout(function() {
                    document.getElementById('khalti-form').submit();
                }, 3000);
            </script>
        @else
            <!-- Error state -->
            <div class="bg-red-100 border border-red-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-red-800 font-medium">Payment initialization failed</span>
                </div>
                <p class="text-red-700 text-sm mt-1">
                    {{ isset($result) ? $result['message'] : 'Unable to initialize payment with Khalti.' }}
                </p>
            </div>
        @endif

        <!-- Payment Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <h4 class="font-medium text-blue-900 mb-2">Payment Instructions</h4>
            <ul class="text-blue-800 text-sm space-y-1">
                <li>• You can pay using Khalti wallet, e-banking, mobile banking</li>
                <li>• Credit/Debit cards and ConnectIPS are also supported</li>
                <li>• Your booking will be confirmed after successful payment</li>
                <li>• Keep your transaction details safe for future reference</li>
            </ul>
        </div>

        <!-- Support Info -->
        <div class="text-center text-sm text-gray-500">
            <p>Having trouble? <a href="#" class="text-purple-600 hover:text-purple-700">Contact Support</a></p>
        </div>

        <!-- Back to booking button -->
        <div class="mt-6 text-center">
            <a href="{{ route('bookings.show', $payment->booking) }}" 
               class="text-gray-600 hover:text-gray-700 text-sm font-medium">
                ← Back to Booking Details
            </a>
        </div>
    </div>
</div>

<!-- Khalti Test Information (only show in development) -->
@if(config('app.env') === 'local')
<div class="container mx-auto px-4 pb-8">
    <div class="max-w-md mx-auto bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h4 class="font-medium text-yellow-900 mb-2">Test Mode - Khalti Test Credentials</h4>
        <div class="text-yellow-800 text-sm space-y-1">
            <p><strong>Test Mobile:</strong> 9800000000 to 9800000005</p>
            <p><strong>Test MPIN:</strong> 1111</p>
            <p><strong>Test OTP:</strong> 987654</p>
            <p class="text-xs mt-2 text-yellow-700">Use these credentials for testing payments</p>
        </div>
    </div>
</div>
@endif
@endsection
