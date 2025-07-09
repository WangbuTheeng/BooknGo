<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isUser()) {
            abort(403, 'Unauthorized action.');
        }

        $tickets = $user->bookings()->where('status', 'bought')->with(['trip.route.origin', 'trip.route.destination', 'trip.bus'])->latest()->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['trip.route.origin', 'trip.route.destination', 'trip.bus', 'seats']);
        return view('tickets.show', compact('booking'));
    }

    public function print(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['trip.route.origin', 'trip.route.destination', 'trip.bus', 'seats']);
        return view('tickets.print', compact('booking'));
    }

    public function download(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['trip.route.origin', 'trip.route.destination', 'trip.bus', 'seats']);
        $pdf = Pdf::loadView('tickets.show', compact('booking'));
        return $pdf->download('ticket-' . $booking->id . '.pdf');
    }
}
