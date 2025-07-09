<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - {{ $booking->id }}</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8 max-w-3xl">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-blue-600 text-white p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold">BooknGo</h1>
                        <p class="text-blue-200">Your travel partner</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-semibold">Boarding Pass</p>
                        <p class="text-sm">Booking ID: {{ $booking->id }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <p class="text-gray-500 text-sm">Passenger</p>
                        <p class="font-semibold text-lg">{{ $booking->passenger_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Contact</p>
                        <p class="font-semibold text-lg">{{ $booking->passenger_phone }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <div class="grid grid-cols-3 gap-6 text-center">
                        <div>
                            <p class="text-gray-500 text-sm">From</p>
                            <p class="font-bold text-xl">{{ $booking->trip->route->origin->name }}</p>
                        </div>
                        <div class="flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">To</p>
                            <p class="font-bold text-xl">{{ $booking->trip->route->destination->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 mt-6 pt-6">
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <p class="text-gray-500 text-sm">Date</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->trip->trip_date)->format('D, M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Departure Time</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->trip->departure_time)->format('h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Arrival Time</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($booking->trip->arrival_time)->format('h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 mt-6 pt-6">
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <p class="text-gray-500 text-sm">Bus</p>
                            <p class="font-semibold">{{ $booking->trip->bus->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Bus Number</p>
                            <p class="font-semibold">{{ $booking->trip->bus->registration_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Seat(s)</p>
                            <p class="font-semibold">{{ $booking->seats->pluck('seat_number')->implode(', ') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 border-t border-gray-200">
                <p class="text-xs text-gray-600">
                    <strong>Terms & Conditions:</strong> Please arrive at the boarding point at least 30 minutes before departure. All passengers must carry a valid ID proof. BooknGo is not responsible for any loss of baggage.
                </p>
            </div>
        </div>

        <div class="text-center mt-8 no-print">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Print Ticket
            </button>
        </div>
    </div>
</body>
</html>
