<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Trip;
use App\Models\Seat;
use App\Models\BookingSeat;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class OperatorBookingController extends Controller
{
    /**
     * Display operator booking dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            abort(403, 'Access denied. Only operators can access this page.');
        }

        $operator = $user->operator;
        
        // Get today's trips for this operator
        $todayTrips = Trip::whereHas('bus', function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            })
            ->whereDate('departure_datetime', today())
            ->with(['route.fromCity', 'route.toCity', 'bus'])
            ->orderBy('departure_datetime')
            ->get();

        // Get recent bookings for this operator
        $recentBookings = Booking::whereHas('trip.bus', function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            })
            ->with(['trip.route.fromCity', 'trip.route.toCity', 'user', 'payments'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('operator.booking.index', compact('todayTrips', 'recentBookings'));
    }

    /**
     * Show counter booking form for a specific trip
     */
    public function create(Trip $trip)
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            abort(403, 'Access denied.');
        }

        // Check if the trip belongs to this operator
        if ($trip->bus->operator_id !== $user->operator->id) {
            abort(403, 'You can only book tickets for your own trips.');
        }

        // Check if trip is available for booking
        if (!$trip->isBookable()) {
            return redirect()->back()->withErrors(['error' => 'This trip is not available for booking.']);
        }

        $trip->load(['bus.seats', 'route.fromCity', 'route.toCity']);
        
        // Get available seats with booking status - ordered by seat number as integer
        $seats = $trip->bus->seats()->with(['bookingSeats' => function($query) use ($trip) {
            $query->whereHas('booking', function($q) use ($trip) {
                $q->where('trip_id', $trip->id)
                  ->where('status', '!=', 'cancelled');
            });
        }])->orderByRaw('CAST(seat_number AS UNSIGNED)')->get();

        return view('operator.booking.create', compact('trip', 'seats'));
    }

    /**
     * Store a counter booking
     */
    public function store(Request $request, Trip $trip)
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            abort(403, 'Access denied.');
        }

        // Check if the trip belongs to this operator
        if ($trip->bus->operator_id !== $user->operator->id) {
            abort(403, 'You can only book tickets for your own trips.');
        }

        $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:20',
            'passenger_email' => 'nullable|email|max:255',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
            'payment_method' => 'required|in:cash,card',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if trip is still available
        if ($trip->status !== 'active' || $trip->departure_datetime <= now()) {
            return back()->withErrors(['error' => 'This trip is no longer available for booking.']);
        }
        
        // Debug: Log the trip status
        \Log::info('Trip status check passed', [
            'trip_id' => $trip->id,
            'trip_status' => $trip->status,
            'departure_datetime' => $trip->departure_datetime,
            'current_time' => now()
        ]);

        // Verify seat availability and ownership
        $seats = Seat::whereIn('id', $request->seat_ids)
                    ->where('bus_id', $trip->bus_id)
                    ->get();

        if ($seats->count() !== count($request->seat_ids)) {
            return back()->withErrors(['error' => 'Invalid seat selection.']);
        }

        // Check if any selected seats are already booked
        foreach ($seats as $seat) {
            if (!$seat->isAvailableForTrip($trip->id)) {
                return back()->withErrors(['error' => "Seat {$seat->seat_number} is no longer available."]);
            }
        }

        DB::beginTransaction();

        try {
            // Calculate total amount
            $totalAmount = $trip->price * $seats->count();

            // Create booking - for operator bookings, we set longer expiry
            $booking = Booking::create([
                'user_id' => $user->id, // Operator's user ID
                'trip_id' => $trip->id,
                'booking_code' => $this->generateBookingCode(),
                'booking_reference' => $this->generateBookingReference(),
                'passenger_name' => $request->passenger_name,
                'passenger_phone' => $request->passenger_phone,
                'passenger_email' => $request->passenger_email,
                'total_amount' => $totalAmount,
                'status' => 'booked',
                'payment_status' => $request->payment_method === 'cash' ? 'pending' : 'completed',
                'expires_at' => now()->addHours(24), // Longer expiry for operator bookings
                'booking_type' => 'counter', // Mark as counter booking
                'notes' => $request->notes,
            ]);

            // Create booking seats
            foreach ($seats as $seat) {
                BookingSeat::create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seat->id,
                    'seat_number' => $seat->seat_number,
                ]);
            }

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'method' => ucfirst($request->payment_method),
                'amount' => $totalAmount,
                'transaction_id' => $this->generateTransactionId(),
                'payment_status' => $request->payment_method === 'cash' ? 'pending' : 'completed',
                'processed_by_user_id' => $user->id, // Track who processed the payment
            ]);
            
            \Log::info('Payment record created', [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'method' => $payment->method,
                'payment_status' => $payment->payment_status,
                'amount' => $payment->amount
            ]);

            DB::commit();

            // Redirect to print ticket page
            return redirect()->route('operator.booking.print', $booking)
                           ->with('success', 'Booking created successfully! Print the ticket for the customer.');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the actual error for debugging
            \Log::error('Operator booking creation failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trip_id' => $trip->id,
                'passenger_name' => $request->passenger_name,
                'seat_ids' => $request->seat_ids,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Failed to create booking. Please try again. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Show booking details for operator
     */
    public function show(Booking $booking)
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            abort(403, 'Access denied.');
        }

        // Check if booking belongs to this operator's trips
        if ($booking->trip->bus->operator_id !== $user->operator->id) {
            abort(403, 'You can only view bookings for your own trips.');
        }

        $booking->load([
            'trip.route.fromCity',
            'trip.route.toCity',
            'trip.bus',
            'bookingSeats.seat',
            'payments'
        ]);

        return view('operator.booking.show', compact('booking'));
    }

    /**
     * Print ticket page
     */
    public function print(Booking $booking)
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            abort(403, 'Access denied.');
        }

        // Check if booking belongs to this operator's trips
        if ($booking->trip->bus->operator_id !== $user->operator->id) {
            abort(403, 'You can only print tickets for your own bookings.');
        }

        // Refresh booking to get latest payment status
        $booking->refresh();
        
        $booking->load([
            'trip.route.fromCity',
            'trip.route.toCity',
            'trip.bus.operator.user',
            'bookingSeats.seat',
            'payments'
        ]);
        

        return view('operator.booking.print', compact('booking'));
    }

    /**
     * Generate PDF ticket
     */
    public function downloadTicket(Booking $booking)
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            abort(403, 'Access denied.');
        }

        // Check if booking belongs to this operator's trips
        if ($booking->trip->bus->operator_id !== $user->operator->id) {
            abort(403, 'You can only download tickets for your own bookings.');
        }

        // Refresh booking to get latest payment status
        $booking->refresh();
        
        $booking->load([
            'trip.route.fromCity',
            'trip.route.toCity',
            'trip.bus.operator.user',
            'bookingSeats.seat',
            'payments'
        ]);

        $pdf = Pdf::loadView('operator.booking.ticket-pdf', compact('booking'));
        
        return $pdf->download('ticket-' . $booking->booking_reference . '.pdf');
    }

    /**
     * Confirm cash payment
     */
    public function confirmPayment(Booking $booking)
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            abort(403, 'Access denied.');
        }

        // Check if booking belongs to this operator's trips
        if ($booking->trip->bus->operator_id !== $user->operator->id) {
            abort(403, 'You can only confirm payments for your own bookings.');
        }

        // Log initial state
        \Log::info('Payment confirmation attempt', [
            'booking_id' => $booking->id,
            'booking_reference' => $booking->booking_reference,
            'current_payment_status' => $booking->payment_status,
            'confirmed_by' => $user->id
        ]);

        if ($booking->payment_status !== 'pending') {
            \Log::warning('Payment confirmation failed - not pending', [
                'booking_id' => $booking->id,
                'current_status' => $booking->payment_status
            ]);
            return back()->withErrors(['error' => 'Payment is not pending confirmation. Current status: ' . $booking->payment_status]);
        }

        DB::beginTransaction();

        try {
            // Load payments relationship to get payment method
            $booking->load('payments');
            
            // Check if payment records exist, create if missing
            if ($booking->payments->isEmpty()) {
                \Log::warning('Booking has no payment records, creating default payment record', [
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->booking_reference
                ]);
                
                // Create a default payment record
                $booking->payments()->create([
                    'amount' => $booking->total_amount,
                    'method' => 'Cash',
                    'payment_status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Reload the payments relationship
                $booking->load('payments');
            }
            
            $paymentMethod = $booking->payments->first() ? $booking->payments->first()->method : 'Cash';
            
            \Log::info('Before payment update', [
                'booking_payment_status' => $booking->payment_status,
                'payment_method' => $paymentMethod,
                'payments_count' => $booking->payments->count()
            ]);
            
            // Update payment status
            $updatedPayments = $booking->payments()->update([
                'payment_status' => 'completed',
                'confirmed_at' => now(),
                'confirmed_by_user_id' => $user->id,
            ]);

            // Update booking payment status
            $booking->update(['payment_status' => 'completed']);
            
            // Verify the update
            $booking->refresh();
            $booking->load('payments');
            
            \Log::info('After payment update', [
                'booking_payment_status' => $booking->payment_status,
                'updated_payments_count' => $updatedPayments,
                'first_payment_status' => $booking->payments->first() ? $booking->payments->first()->payment_status : null
            ]);

            DB::commit();

            // Final verification log
            \Log::info('Payment confirmed successfully', [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'final_booking_status' => $booking->payment_status,
                'payment_method' => $paymentMethod,
                'confirmed_by' => $user->id
            ]);
            
            // Redirect to print page instead of going back
            return redirect()->route('operator.booking.print', $booking)
                           ->with('success', ucfirst(strtolower($paymentMethod)) . ' payment confirmed successfully. You can now print the ticket.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Payment confirmation failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Failed to confirm payment. Please try again. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get trip seat availability (AJAX)
     */
    public function getSeatAvailability(Trip $trip)
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            abort(403, 'Access denied.');
        }

        // Check if the trip belongs to this operator
        if ($trip->bus->operator_id !== $user->operator->id) {
            abort(403, 'You can only view seat availability for your own trips.');
        }

        $seats = $trip->bus->seats()->with(['bookingSeats' => function($query) use ($trip) {
            $query->whereHas('booking', function($q) use ($trip) {
                $q->where('trip_id', $trip->id)
                  ->where('status', '!=', 'cancelled');
            });
        }])->orderByRaw('CAST(seat_number AS UNSIGNED)')->get();

        $seatData = $seats->map(function($seat) {
            return [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'is_available' => $seat->bookingSeats->isEmpty(),
                'booking_reference' => $seat->bookingSeats->first()?->booking?->booking_reference,
            ];
        });

        return response()->json([
            'trip_id' => $trip->id,
            'total_seats' => $seats->count(),
            'available_count' => $seatData->where('is_available', true)->count(),
            'seats' => $seatData
        ]);
    }

    /**
     * Generate a unique booking code
     */
    private function generateBookingCode(): string
    {
        do {
            $code = strtoupper(Str::random(10));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }

    /**
     * Generate a unique booking reference
     */
    private function generateBookingReference(): string
    {
        do {
            $reference = 'OPR' . strtoupper(Str::random(6));
        } while (Booking::where('booking_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Show all customer bookings for operator's buses
     */
    public function customerBookings(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isOperator()) {
            abort(403, 'Access denied. Only operators can access this page.');
        }

        $operator = $user->operator;
        
        // Base query for bookings on operator's buses
        $query = Booking::whereHas('trip.bus', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->with([
            'user',
            'trip.route.fromCity',
            'trip.route.toCity',
            'trip.bus',
            'bookingSeats.seat',
            'payments'
        ]);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        if ($request->filled('booking_type')) {
            $query->where('booking_type', $request->booking_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('trip_date')) {
            $query->whereHas('trip', function($q) use ($request) {
                $q->whereDate('departure_datetime', $request->trip_date);
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('passenger_name', 'like', "%{$search}%")
                  ->orWhere('passenger_phone', 'like', "%{$search}%")
                  ->orWhere('passenger_email', 'like', "%{$search}%");
            });
        }
        
        // Get statistics
        $stats = [
            'total_bookings' => Booking::whereHas('trip.bus', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->count(),
            'today_bookings' => Booking::whereHas('trip.bus', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->whereDate('created_at', today())->count(),
            'pending_payments' => Booking::whereHas('trip.bus', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('payment_status', 'pending')->count(),
            'total_revenue' => Booking::whereHas('trip.bus', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('payment_status', 'completed')->sum('total_amount'),
        ];
        
        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('operator.booking.customer-bookings', compact('bookings', 'stats'));
    }

    /**
     * Generate a unique transaction ID
     */
    private function generateTransactionId(): string
    {
        do {
            $transactionId = 'TXN' . strtoupper(Str::random(8));
        } while (Payment::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
}
