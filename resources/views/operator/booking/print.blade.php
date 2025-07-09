<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Ticket - {{ $booking->booking_reference }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body { 
                font-size: 10pt !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }
            .no-print { display: none !important; }
            .print-section { 
                page-break-inside: avoid;
                max-height: 100vh;
                overflow: hidden;
            }
            .ticket { 
                border: 2px solid #000;
                margin-bottom: 15px !important;
                padding: 15px !important;
                font-size: 9pt !important;
                line-height: 1.2 !important;
            }
            .max-w-4xl {
                max-width: none !important;
                margin: 0 !important;
                padding: 10px !important;
            }
            .grid {
                gap: 10px !important;
            }
            .mb-6, .mb-8 {
                margin-bottom: 10px !important;
            }
            .p-6 {
                padding: 10px !important;
            }
            .p-4 {
                padding: 8px !important;
            }
            .my-6 {
                margin: 8px 0 !important;
            }
            .space-y-1 > * + * {
                margin-top: 2px !important;
            }
            .text-lg {
                font-size: 12pt !important;
            }
            .text-sm {
                font-size: 8pt !important;
            }
            .text-xs {
                font-size: 7pt !important;
            }
            /* Ensure operator copy is more compact */
            .operator-copy {
                font-size: 8pt !important;
                padding: 8px !important;
            }
            /* Hide shadow effects in print */
            .shadow-lg {
                box-shadow: none !important;
            }
            /* Ensure single page printing */
            @page {
                margin: 0.5in;
                size: A4;
            }
            /* Force everything to fit on one page */
            .print-container {
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                transform: scale(0.85) !important;
                transform-origin: top left !important;
            }
        }
        .ticket-dash { 
            border-top: 2px dashed #666;
            margin: 10px 0;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header (No Print) -->
    <div class="no-print bg-white shadow-sm border-b border-gray-200 mb-6">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('operator.booking.index') }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Print Ticket</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $booking->booking_reference }}
                    </span>
                </div>
                <div class="flex space-x-3">
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Ticket
                    </button>
                    <a href="{{ route('operator.booking.download-ticket', $booking) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Content -->
    <div class="max-w-4xl mx-auto px-4 print-container">
        <div class="print-section">
            <!-- Original Ticket (For Passenger) -->
            <div class="ticket bg-white border-2 border-gray-800 rounded-lg p-6 mb-8 shadow-lg">
                <div class="border-b-2 border-gray-200 pb-4 mb-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">BooknGo</h1>
                            <p class="text-sm text-gray-600">Bus Ticket</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">{{ $booking->booking_reference }}</div>
                            <div class="text-sm text-gray-600">{{ $booking->created_at->format('M d, Y • H:i') }}</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Passenger Information -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Passenger Details</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-medium text-gray-900">{{ $booking->passenger_name }}</p>
                            <p class="text-sm text-gray-600">{{ $booking->passenger_phone }}</p>
                            @if($booking->passenger_email)
                                <p class="text-sm text-gray-600">{{ $booking->passenger_email }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Trip Information -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Trip Details</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-medium text-gray-900">
                                {{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Date:</span> {{ $booking->trip->departure_datetime->format('M d, Y') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Departure:</span> {{ $booking->trip->departure_datetime->format('H:i A') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Bus:</span> {{ $booking->trip->bus->name ?: $booking->trip->bus->registration_number }}
                            </p>
                             <p class="text-sm text-gray-600">
                                <span class="font-medium">Bus Number:</span> {{ $booking->trip->bus->bus_number }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Seat Information -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Seat Details</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($booking->bookingSeats as $bookingSeat)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Seat {{ $bookingSeat->seat_number }}
                                </span>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-600 mt-2">
                            {{ $booking->bookingSeats->count() }} seat(s) • NPR {{ number_format($booking->trip->price) }} each
                        </p>
                    </div>

                    <!-- Payment Information -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-2">Payment Details</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-bold text-gray-900">NPR {{ number_format($booking->total_amount) }}</p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Method:</span> 
                                {{ $booking->payments->first()->method ?? 'Cash' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Status:</span>
                                @if($booking->payment_status === 'completed')
                                    <span class="text-green-600">Paid</span>
                                @elseif($booking->payment_status === 'pending')
                                    @if($booking->payments->first() && strtolower($booking->payments->first()->method) === 'card')
                                        <span class="text-yellow-600">Card Pending</span>
                                    @else
                                        <span class="text-yellow-600">Cash Pending</span>
                                    @endif
                                @else
                                    <span class="text-red-600">{{ ucfirst($booking->payment_status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Operator Information -->
                <div class="border-t-2 border-gray-200 pt-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Operator</h3>
                            <p class="text-sm font-medium text-gray-900">{{ $booking->trip->bus->operator->company_name }}</p>
                            @if($booking->trip->bus->operator->contact_info && isset($booking->trip->bus->operator->contact_info['phone']))
                                <p class="text-xs text-gray-600">{{ $booking->trip->bus->operator->contact_info['phone'] }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">
                                Booked at: {{ $booking->created_at->format('M d, Y • H:i A') }}
                            </p>
                            @if($booking->payment_status === 'pending')
                                <p class="text-xs text-yellow-600 font-medium mt-1">
                                    @if($booking->payments->first() && strtolower($booking->payments->first()->method) === 'card')
                                        ⚠️ Card payment pending confirmation
                                    @else
                                        ⚠️ Cash payment pending confirmation
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <h4 class="text-xs font-semibold text-yellow-800 uppercase tracking-wider mb-1">Important Notes</h4>
                    <ul class="text-xs text-yellow-700 space-y-1">
                        <li>• Please arrive at least 30 minutes before departure</li>
                        <li>• Keep this ticket safe - it's required for boarding</li>
                        <li>• Contact operator for any changes or cancellations</li>
                        @if($booking->notes)
                            <li>• Special note: {{ $booking->notes }}</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Operator Copy (Dashed line separator) -->
            <div class="ticket-dash my-6"></div>

            <!-- Operator Copy -->
            <div class="ticket operator-copy bg-white border-2 border-gray-800 rounded-lg p-4 shadow-lg">
                <div class="border-b border-gray-200 pb-2 mb-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">BooknGo - Operator Copy</h2>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">{{ $booking->booking_reference }}</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="font-medium text-gray-700">Passenger:</p>
                        <p class="text-gray-900">{{ $booking->passenger_name }}</p>
                        <p class="text-gray-600">{{ $booking->passenger_phone }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Route:</p>
                        <p class="text-gray-900">{{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}</p>
                        <p class="text-gray-600">{{ $booking->trip->departure_datetime->format('M d, Y • H:i') }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Bus Number:</p>
                        <p class="text-gray-900">{{ $booking->trip->bus->bus_number }}</p>
                    </div>
                    <div>
                        <p class="font-medium text-gray-700">Seats & Payment:</p>
                        <p class="text-gray-900">
                            Seats: 
                            @foreach($booking->bookingSeats as $seat)
                                {{ $seat->seat_number }}@if(!$loop->last), @endif
                            @endforeach
                        </p>
                        <p class="text-gray-600">NPR {{ number_format($booking->total_amount) }} - 
                            @if($booking->payment_status === 'completed')
                                <span class="text-green-600">Paid</span>
                            @elseif($booking->payment_status === 'pending')
                                @if($booking->payments->first() && strtolower($booking->payments->first()->method) === 'card')
                                    <span class="text-yellow-600">Card Pending</span>
                                @else
                                    <span class="text-yellow-600">Cash Pending</span>
                                @endif
                            @else
                                <span class="text-red-600">{{ ucfirst($booking->payment_status) }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($booking->payment_status === 'pending')
                    <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="text-xs text-yellow-800 font-medium">
                            @if($booking->payments->first() && strtolower($booking->payments->first()->method) === 'card')
                                ⚠️ Remember to confirm card payment in system after processing
                            @else
                                ⚠️ Remember to confirm cash payment in system after collection
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Success Message for Operator (No Print) -->
    @if(session('success'))
        <div class="no-print fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Cash Payment Confirmation (No Print) -->
    @if($booking->payment_status === 'pending')
        <div class="no-print fixed bottom-4 left-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded max-w-sm">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-4 w-4 text-yellow-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    @if($booking->payments->first() && strtolower($booking->payments->first()->method) === 'card')
                        <p class="font-bold text-sm">Card Payment Pending</p>
                        <p class="text-xs">Don't forget to confirm payment in the system after processing card.</p>
                        <form method="POST" action="{{ route('operator.booking.confirm-payment', $booking) }}" class="mt-2">
                            @csrf
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded text-xs"
                                    onclick="return confirm('Confirm that card payment has been processed?')">
                                Confirm Payment
                            </button>
                        </form>
                    @else
                        <p class="font-bold text-sm">Cash Payment Pending</p>
                        <p class="text-xs">Don't forget to confirm payment in the system after collecting cash.</p>
                        <form method="POST" action="{{ route('operator.booking.confirm-payment', $booking) }}" class="mt-2">
                            @csrf
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded text-xs"
                                    onclick="return confirm('Confirm that cash payment has been received?')">
                                Confirm Payment
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <script>
        // Auto-focus on print dialog when page loads
        window.addEventListener('load', function() {
            // Small delay to ensure page is fully loaded
            setTimeout(function() {
                // Only auto-print if this is the first visit (not a refresh)
                if (!sessionStorage.getItem('printShown')) {
                    sessionStorage.setItem('printShown', 'true');
                    window.print();
                }
            }, 500);
        });

        // Clear the print flag when leaving the page
        window.addEventListener('beforeunload', function() {
            sessionStorage.removeItem('printShown');
        });
    </script>
</body>
</html>
