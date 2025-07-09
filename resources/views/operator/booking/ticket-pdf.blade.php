<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bus Ticket - {{ $booking->booking_reference }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }
        .ticket {
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .ticket-type {
            font-size: 10px;
            color: #666;
        }
        .booking-ref {
            float: right;
            font-size: 14px;
            font-weight: bold;
        }
        .clear {
            clear: both;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
            padding-bottom: 2px;
        }
        .info-grid {
            width: 100%;
        }
        .info-grid td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 30%;
        }
        .info-value {
            width: 70%;
        }
        .route {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        .seats {
            text-align: center;
            margin: 10px 0;
        }
        .seat-badge {
            display: inline-block;
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 3px 6px;
            margin: 0 2px;
            border-radius: 3px;
            font-weight: bold;
        }
        .amount {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
        }
        .notes {
            background: #fffbf0;
            border: 1px solid #f0c674;
            padding: 8px;
            margin-top: 10px;
            border-radius: 3px;
        }
        .notes-title {
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .notes ul {
            margin: 0;
            padding-left: 15px;
        }
        .notes li {
            margin-bottom: 2px;
            font-size: 10px;
        }
        .operator-copy {
            border-style: dashed;
            border-width: 1px;
            padding: 10px;
            margin-top: 30px;
        }
        .operator-copy .header {
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .compact-info {
            font-size: 10px;
        }
        .compact-info table {
            width: 100%;
        }
        .compact-info td {
            padding: 2px 5px;
            border-right: 1px solid #eee;
        }
        .compact-info td:last-child {
            border-right: none;
        }
        .status-pending {
            color: #856404;
            background: #fff3cd;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-paid {
            color: #155724;
            background: #d4edda;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Passenger Copy -->
    <div class="ticket">
        <div class="header">
            <div class="booking-ref">{{ $booking->booking_reference }}</div>
            <div class="company-name">BooknGo</div>
            <div class="ticket-type">Bus Ticket</div>
            <div class="clear"></div>
        </div>

        <div class="route">
            {{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}
        </div>

        <div class="section">
            <div class="section-title">Passenger Details</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Name:</td>
                    <td class="info-value">{{ $booking->passenger_name }}</td>
                </tr>
                <tr>
                    <td class="info-label">Phone:</td>
                    <td class="info-value">{{ $booking->passenger_phone }}</td>
                </tr>
                @if($booking->passenger_email)
                <tr>
                    <td class="info-label">Email:</td>
                    <td class="info-value">{{ $booking->passenger_email }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="section">
            <div class="section-title">Journey Details</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Date:</td>
                    <td class="info-value">{{ $booking->trip->departure_datetime->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Departure:</td>
                    <td class="info-value">{{ $booking->trip->departure_datetime->format('H:i A') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Bus:</td>
                    <td class="info-value">{{ $booking->trip->bus->name ?: $booking->trip->bus->registration_number }}</td>
                </tr>
                <tr>
                    <td class="info-label">Bus Number:</td>
                    <td class="info-value">{{ $booking->trip->bus->bus_number }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Seat Information</div>
            <div class="seats">
                @foreach($booking->bookingSeats as $bookingSeat)
                    <span class="seat-badge">{{ $bookingSeat->seat_number }}</span>
                @endforeach
            </div>
            <div style="text-align: center; font-size: 10px; color: #666;">
                {{ $booking->bookingSeats->count() }} seat(s) • NPR {{ number_format($booking->trip->price) }} each
            </div>
        </div>

        <div class="section">
            <div class="section-title">Payment Information</div>
            <div class="amount">NPR {{ number_format($booking->total_amount) }}</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Method:</td>
                    <td class="info-value">{{ $booking->payments->first()->method ?? 'Cash' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Status:</td>
                    <td class="info-value">
                        @if($booking->payment_status === 'completed')
                            <span class="status-paid">PAID</span>
                        @else
                            <span class="status-pending">CASH PENDING</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Operator Information</div>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Company:</td>
                    <td class="info-value">{{ $booking->trip->bus->operator->company_name }}</td>
                </tr>
                @if($booking->trip->bus->operator->contact_info && isset($booking->trip->bus->operator->contact_info['phone']))
                <tr>
                    <td class="info-label">Contact:</td>
                    <td class="info-value">{{ $booking->trip->bus->operator->contact_info['phone'] }}</td>
                </tr>
                @endif
                <tr>
                    <td class="info-label">Booked:</td>
                    <td class="info-value">{{ $booking->created_at->format('M d, Y H:i A') }}</td>
                </tr>
            </table>
        </div>

        <div class="notes">
            <div class="notes-title">Important Instructions</div>
            <ul>
                <li>Please arrive at least 30 minutes before departure time</li>
                <li>Keep this ticket safe - it is required for boarding</li>
                <li>Contact the operator for any changes or cancellations</li>
                @if($booking->notes)
                <li>Special Note: {{ $booking->notes }}</li>
                @endif
                @if($booking->payment_status === 'pending')
                <li><strong>Cash payment pending - please pay operator before boarding</strong></li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Operator Copy -->
    <div class="ticket operator-copy">
        <div class="header">
            <div class="booking-ref">{{ $booking->booking_reference }}</div>
            <div class="company-name">BooknGo - Operator Copy</div>
            <div class="clear"></div>
        </div>

        <div class="compact-info">
            <table>
                <tr>
                    <td style="width: 33%;">
                        <strong>Passenger:</strong><br>
                        {{ $booking->passenger_name }}<br>
                        {{ $booking->passenger_phone }}
                    </td>
                    <td style="width: 34%;">
                        <strong>Journey:</strong><br>
                        {{ $booking->trip->route->fromCity->name }} → {{ $booking->trip->route->toCity->name }}<br>
                        {{ $booking->trip->departure_datetime->format('M d, Y H:i') }}<br>
                        Bus Number: {{ $booking->trip->bus->bus_number }}
                    </td>
                    <td style="width: 33%;">
                        <strong>Seats & Amount:</strong><br>
                        Seats: @foreach($booking->bookingSeats as $seat){{ $seat->seat_number }}@if(!$loop->last), @endif @endforeach<br>
                        NPR {{ number_format($booking->total_amount) }} - 
                        @if($booking->payment_status === 'paid')
                            <span class="status-paid">PAID</span>
                        @else
                            <span class="status-pending">CASH PENDING</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        @if($booking->payment_status === 'pending')
        <div style="margin-top: 10px; padding: 5px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 3px;">
            <strong style="font-size: 10px;">⚠️ REMINDER:</strong>
            <span style="font-size: 10px;">Confirm cash payment in system after collection</span>
        </div>
        @endif
    </div>
</body>
</html>
