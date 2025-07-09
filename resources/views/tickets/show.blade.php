<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket - {{ $booking->id }}</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 800px; margin: auto; background: #fff; border: 1px solid #ddd; border-radius: 8px; }
        .header { background: #0d6efd; color: white; padding: 20px; border-top-left-radius: 8px; border-top-right-radius: 8px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; font-size: 14px; }
        .content { padding: 20px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 5px; }
        .section-content { font-size: 18px; font-weight: bold; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .text-center { text-align: center; }
        .arrow { display: flex; align-items: center; justify-content: center; font-size: 24px; color: #999; }
        .footer { background: #f8f9fa; padding: 20px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; font-size: 12px; color: #666; }
        hr { border: 0; border-top: 1px solid #eee; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1>BooknGo</h1>
                    <p>Your travel partner</p>
                </div>
                <div style="text-align: right;">
                    <p style="font-size: 18px; font-weight: 600;">Boarding Pass</p>
                    <p style="font-size: 14px;">Booking ID: {{ $booking->id }}</p>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="grid section">
                <div>
                    <div class="section-title">Passenger</div>
                    <div class="section-content">{{ $booking->passenger_name }}</div>
                </div>
                <div>
                    <div class="section-title">Contact</div>
                    <div class="section-content">{{ $booking->passenger_phone }}</div>
                </div>
            </div>

            <hr>

            <div class="grid-3 section text-center">
                <div>
                    <div class="section-title">From</div>
                    <div class="section-content">{{ $booking->trip->route->origin->name }}</div>
                </div>
                <div class="arrow">&rarr;</div>
                <div>
                    <div class="section-title">To</div>
                    <div class="section-content">{{ $booking->trip->route->destination->name }}</div>
                </div>
            </div>

            <hr>

            <div class="grid-3 section">
                <div>
                    <div class="section-title">Date</div>
                    <div class="section-content">{{ \Carbon\Carbon::parse($booking->trip->trip_date)->format('D, M d, Y') }}</div>
                </div>
                <div>
                    <div class="section-title">Departure Time</div>
                    <div class="section-content">{{ \Carbon\Carbon::parse($booking->trip->departure_time)->format('h:i A') }}</div>
                </div>
                <div>
                    <div class="section-title">Arrival Time</div>
                    <div class="section-content">{{ \Carbon\Carbon::parse($booking->trip->arrival_time)->format('h:i A') }}</div>
                </div>
            </div>

            <hr>

            <div class="grid-3 section">
                <div>
                    <div class="section-title">Bus</div>
                    <div class="section-content">{{ $booking->trip->bus->name }}</div>
                </div>
                <div>
                    <div class="section-title">Bus Number</div>
                    <div class="section-content">{{ $booking->trip->bus->registration_number }}</div>
                </div>
                <div>
                    <div class="section-title">Seat(s)</div>
                    <div class="section-content">{{ $booking->seats->pluck('seat_number')->implode(', ') }}</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <strong>Terms & Conditions:</strong> Please arrive at the boarding point at least 30 minutes before departure. All passengers must carry a valid ID proof. BooknGo is not responsible for any loss of baggage.
        </div>
    </div>
</body>
</html>
