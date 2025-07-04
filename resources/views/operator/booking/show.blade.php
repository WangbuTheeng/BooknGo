<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('operator.booking.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Booking Details - {{ $booking->booking_reference }}
                </h2>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('operator.booking.print', $booking) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Ticket
                </a>
                @if($booking->payment_status === 'pending')
                    <form method="POST" action="{{ route('operator.booking.confirm-payment', $booking) }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700"
                                onclick="return confirm('Confirm that cash payment has been received?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Confirm Payment
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors:</h3>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Booking Details -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Status Overview -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Booking Status</h3>
                            <div class="flex space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if($booking->status === 'booked') bg-green-100 text-green-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if($booking->payment_status === 'completed') bg-green-100 text-green-800
                                    @elseif($booking->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    Payment: {{ ucfirst($booking->payment_status) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Booking Reference</label>
                                <p class="text-lg font-bold text-gray-900">{{ $booking->booking_reference }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Booking Type</label>
                                <p class="text-lg font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($booking->booking_type ?? 'counter') }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Booked On</label>
                                <p class="text-lg font-medium text-gray-900">{{ $booking->created_at->format('M d, Y H:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Trip Information -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Trip Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Route</label>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Departure</label>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $booking->trip->departure_datetime->format('M d, Y • H:i A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Bus</label>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $booking->trip->bus->name ?: $booking->trip->bus->registration_number }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Price per Seat</label>
                                <p class="text-lg font-medium text-gray-900">NPR {{ number_format($booking->trip->price) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger Information -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Passenger Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Name</label>
                                <p class="text-lg font-medium text-gray-900">{{ $booking->passenger_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Phone</label>
                                <p class="text-lg font-medium text-gray-900">{{ $booking->passenger_phone }}</p>
                            </div>
                            @if($booking->passenger_email)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Email</label>
                                <p class="text-lg font-medium text-gray-900">{{ $booking->passenger_email }}</p>
                            </div>
                            @endif
                            @if($booking->notes)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Notes</label>
                                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">{{ $booking->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Seat Information -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Seat Information</h3>
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($booking->bookingSeats as $bookingSeat)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Seat {{ $bookingSeat->seat_number }}
                                </span>
                            @endforeach
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $booking->bookingSeats->count() }} seat(s) • NPR {{ number_format($booking->trip->price) }} each
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    
                    <!-- Payment Summary -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">{{ $booking->bookingSeats->count() }} × NPR {{ number_format($booking->trip->price) }}</span>
                                <span class="text-sm font-medium text-gray-900">NPR {{ number_format($booking->total_amount) }}</span>
                            </div>
                            
                            <div class="border-t pt-3">
                                <div class="flex justify-between text-lg font-bold text-gray-900">
                                    <span>Total Amount</span>
                                    <span>NPR {{ number_format($booking->total_amount) }}</span>
                                </div>
                            </div>
                        </div>

                        @if($booking->payments->count() > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Payment History</h4>
                                @foreach($booking->payments as $payment)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded text-sm">
                                        <div>
                                            <div class="font-medium">{{ $payment->method }}</div>
                                            <div class="text-gray-500">{{ $payment->created_at->format('M d, H:i') }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium">NPR {{ number_format($payment->amount) }}</div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @if($payment->payment_status === 'completed') bg-green-100 text-green-800
                                                @elseif($payment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($payment->payment_status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('operator.booking.print', $booking) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print Ticket
                            </a>
                            
                            <a href="{{ route('operator.booking.download-ticket', $booking) }}" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download PDF
                            </a>

                            @if($booking->payment_status === 'pending')
                                <form method="POST" action="{{ route('operator.booking.confirm-payment', $booking) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700"
                                            onclick="return confirm('Confirm that cash payment has been received from the customer?')">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Confirm Cash Payment
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if($booking->payment_status === 'pending')
                        <!-- Payment Reminder -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Cash Payment Pending</h3>
                                    <div class="mt-1 text-sm text-yellow-700">
                                        <p>Remember to collect cash payment from the customer and confirm it in the system.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
