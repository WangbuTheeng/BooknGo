<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('operator.booking.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Counter Booking - {{ $trip->route->fromCity->name }} → {{ $trip->route->toCity->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Booking Form -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Trip Information -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Trip Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Route</label>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $trip->route->fromCity->name }} → {{ $trip->route->toCity->name }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Departure</label>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $trip->departure_datetime->format('M d, Y • H:i A') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Bus</label>
                                <p class="text-lg font-medium text-gray-900">
                                    {{ $trip->bus->name ?: $trip->bus->registration_number }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Price per seat</label>
                                <p class="text-lg font-medium text-gray-900">NPR {{ number_format($trip->price) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Seat Selection -->
                    <div class="bg-white shadow-sm rounded-lg p-6"
                         x-data="{ 
                            selectedSeats: [],
                            totalAmount: 0,
                            pricePerSeat: {{ $trip->price }},
                            toggleSeat(seatId, seatNumber) {
                                const index = this.selectedSeats.indexOf(seatId);
                                if (index > -1) {
                                    this.selectedSeats.splice(index, 1);
                                } else {
                                    this.selectedSeats.push(seatId);
                                }
                                this.totalAmount = this.selectedSeats.length * this.pricePerSeat;
                                this.updateHiddenInputs();
                            },
                            updateHiddenInputs() {
                                const container = document.getElementById('seat-inputs');
                                container.innerHTML = '';
                                this.selectedSeats.forEach(seatId => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'seat_ids[]';
                                    input.value = seatId;
                                    container.appendChild(input);
                                });
                            }
                         }">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Seats</h3>
                        
                        <!-- Seat Layout -->
                        <div class="mb-6">
                            <div class="bg-gray-100 p-4 rounded-lg mb-4 text-center">
                                <span class="text-sm font-medium text-gray-600">🚌 Driver</span>
                            </div>
                            
                            <!-- Bus Layout: 2+2 seating arrangement -->
                            <div class="max-w-sm mx-auto space-y-2">
                                @foreach($seats->chunk(4) as $rowIndex => $row)
                                    <div class="flex justify-center items-center space-x-2">
                                        @foreach($row as $seatIndex => $seat)
                                            @php
                                                $isBooked = $seat->bookingSeats->isNotEmpty();
                                            @endphp
                                            
                                            <button type="button"
                                                    @if(!$isBooked)
                                                        @click="toggleSeat({{ $seat->id }}, '{{ $seat->seat_number }}')"
                                                        :class="selectedSeats.includes({{ $seat->id }}) ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                                    @endif
                                                    class="h-10 w-10 rounded border-2 text-xs font-medium transition-colors duration-150 {{ $isBooked ? 'bg-red-500 text-white border-red-500 cursor-not-allowed' : '' }}"
                                                    {{ $isBooked ? 'disabled' : '' }}>
                                                {{ $seat->seat_number }}
                                            </button>
                                            
                                            @if($seatIndex == 1)
                                                <!-- Aisle gap between seats 2 and 3 -->
                                                <div class="w-4"></div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Legend -->
                            <div class="flex justify-center space-x-6 mt-4 text-xs">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-white border border-gray-300 rounded mr-1"></div>
                                    <span class="text-gray-600">Available</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-600 rounded mr-1"></div>
                                    <span class="text-gray-600">Selected</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 rounded mr-1"></div>
                                    <span class="text-gray-600">Booked</span>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Seats Summary -->
                        <div x-show="selectedSeats.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Selected Seats</h4>
                            <p class="text-sm text-blue-700">
                                <span x-text="selectedSeats.length"></span> seat(s) selected
                            </p>
                            <p class="text-lg font-bold text-blue-900 mt-1">
                                Total: NPR <span x-text="totalAmount.toLocaleString()"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                        
                        <form method="POST" action="{{ route('operator.booking.store', $trip) }}" id="booking-form">
                            @csrf
                            <div id="seat-inputs"></div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="passenger_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Customer Name *
                                    </label>
                                    <input type="text" 
                                           id="passenger_name" 
                                           name="passenger_name" 
                                           value="{{ old('passenger_name') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                </div>
                                
                                <div>
                                    <label for="passenger_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Phone Number *
                                    </label>
                                    <input type="tel" 
                                           id="passenger_phone" 
                                           name="passenger_phone" 
                                           value="{{ old('passenger_phone') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="passenger_email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email Address (Optional)
                                    </label>
                                    <input type="email" 
                                           id="passenger_email" 
                                           name="passenger_email" 
                                           value="{{ old('passenger_email') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                        Notes (Optional)
                                    </label>
                                    <textarea id="notes" 
                                              name="notes" 
                                              rows="2" 
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Any special instructions or notes...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Payment Method -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                                <div class="space-y-2">
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="payment_method" value="cash" class="h-4 w-4 text-blue-600" required checked>
                                        <div class="ml-3 flex items-center">
                                            <div class="w-8 h-6 bg-green-600 rounded flex items-center justify-center text-white text-xs font-bold mr-2">
                                                💵
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Cash Payment</p>
                                                <p class="text-xs text-gray-500">Customer pays cash at counter</p>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                        <input type="radio" name="payment_method" value="card" class="h-4 w-4 text-blue-600" required>
                                        <div class="ml-3 flex items-center">
                                            <div class="w-8 h-6 bg-blue-600 rounded flex items-center justify-center text-white text-xs font-bold mr-2">
                                                💳
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Card Payment</p>
                                                <p class="text-xs text-gray-500">Customer pays by card at counter</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="mt-6 flex justify-end space-x-3">
                                <a href="{{ route('operator.booking.index') }}" 
                                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        id="submit-booking-btn"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150">
                                    Book Ticket & Print
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="space-y-6">
                    <!-- Booking Summary -->
                    <div class="bg-white shadow-sm rounded-lg p-6" x-data="{ selectedSeats: [], totalAmount: 0 }">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Route:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $trip->route->fromCity->name }} → {{ $trip->route->toCity->name }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Date:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $trip->departure_datetime->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Time:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $trip->departure_datetime->format('H:i A') }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Price per seat:</span>
                                <span class="text-sm font-medium text-gray-900">NPR {{ number_format($trip->price) }}</span>
                            </div>
                            
                            <div class="border-t pt-3" x-show="window.Alpine && window.Alpine.store">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Seats selected:</span>
                                    <span class="text-sm font-medium text-gray-900" id="seat-count">0</span>
                                </div>
                                
                                <div class="flex justify-between text-lg font-bold text-gray-900 mt-2">
                                    <span>Total Amount:</span>
                                    <span id="total-amount">NPR 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Instructions -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Quick Instructions</h4>
                        <ol class="text-xs text-blue-700 space-y-1 list-decimal list-inside">
                            <li>Select seats by clicking on available seats</li>
                            <li>Fill in customer information</li>
                            <li>Choose payment method</li>
                            <li>Click "Book Ticket & Print"</li>
                            <li>Print ticket for customer</li>
                        </ol>
                    </div>

                    <!-- Available Seats Info -->
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Seat Availability</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total seats:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $trip->bus->total_seats }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Available:</span>
                                <span class="text-sm font-medium text-green-600">{{ $trip->available_seats_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Booked:</span>
                                <span class="text-sm font-medium text-red-600">{{ $trip->bus->total_seats - $trip->available_seats_count }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            // Update summary sidebar when seats are selected
            document.addEventListener('click', function(e) {
                if (e.target.type === 'button' && e.target.hasAttribute('@click')) {
                    setTimeout(() => {
                        const selectedCount = document.querySelectorAll('[x-data] button.bg-blue-600').length;
                        const totalAmount = selectedCount * {{ $trip->price }};
                        
                        document.getElementById('seat-count').textContent = selectedCount;
                        document.getElementById('total-amount').textContent = 'NPR ' + totalAmount.toLocaleString();
                        
                        // Enable/disable submit button based on seat selection
                        const submitBtn = document.getElementById('submit-booking-btn');
                        if (selectedCount > 0) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
                            submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                        } else {
                            submitBtn.disabled = true;
                            submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                            submitBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
                        }
                    }, 100);
                }
            });
        });
        
        // Form validation before submission
        document.getElementById('booking-form').addEventListener('submit', function(e) {
            const selectedSeats = document.querySelectorAll('#seat-inputs input[name="seat_ids[]"]');
            
            if (selectedSeats.length === 0) {
                e.preventDefault();
                alert('Please select at least one seat before booking.');
                return false;
            }
            
            const passengerName = document.getElementById('passenger_name').value.trim();
            const passengerPhone = document.getElementById('passenger_phone').value.trim();
            
            if (!passengerName || !passengerPhone) {
                e.preventDefault();
                alert('Please fill in all required customer information.');
                return false;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submit-booking-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
        });
        
        // Initialize submit button state
        document.addEventListener('DOMContentLoaded', function() {
            const submitBtn = document.getElementById('submit-booking-btn');
            submitBtn.disabled = true;
            submitBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
            submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        });
    </script>
</x-app-layout>
