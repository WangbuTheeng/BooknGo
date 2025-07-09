<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\SeatLayoutController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\OperatorBookingController;
use App\Http\Controllers\PublicController;
use App\Models\City;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $cities = City::orderBy('name')->get();
    return view('welcome', compact('cities'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public trip search
Route::get('/search', [TripController::class, 'search'])->name('trips.search');
Route::get('/trips/{trip}/seats', [TripController::class, 'selectSeats'])->name('trips.select-seats');
Route::post('/trips/{trip}/book', [BookingController::class, 'store'])->name('trips.book.store')->middleware('booking.limiter');

// Public operators and buses routes (using browse prefix to avoid conflicts)
Route::get('/browse/operators', [PublicController::class, 'operators'])->name('public.operators.index');
Route::get('/browse/operators/{operator}', [PublicController::class, 'showOperator'])->name('public.operators.show');
Route::get('/browse/operators/{operator}/buses', [PublicController::class, 'operatorBuses'])->name('public.operators.buses');
Route::get('/browse/buses/{bus}/trips', [PublicController::class, 'busTrips'])->name('public.buses.trips');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Bus management routes
    Route::resource('buses', BusController::class);

    Route::middleware('admin')->group(function () {
        // Seat layout configuration routes
        Route::get('buses/{bus}/layout/configure', [SeatLayoutController::class, 'configure'])->name('buses.layout.configure');
        Route::post('buses/{bus}/layout/store', [SeatLayoutController::class, 'store'])->name('buses.layout.store');
        Route::get('buses/{bus}/layout/preview', [SeatLayoutController::class, 'preview'])->name('buses.layout.preview');
        Route::post('buses/{bus}/layout/preview-data', [SeatLayoutController::class, 'getPreview'])->name('buses.layout.preview-data');
        Route::post('buses/{bus}/layout/reset', [SeatLayoutController::class, 'reset'])->name('buses.layout.reset');

        // Individual seat management routes
        Route::get('buses/{bus}/seats', [SeatController::class, 'index'])->name('buses.seats.index');
        Route::get('buses/{bus}/seats/create', [SeatController::class, 'create'])->name('buses.seats.create');
        Route::post('buses/{bus}/seats', [SeatController::class, 'store'])->name('buses.seats.store');
        Route::get('buses/{bus}/seats/{seat}', [SeatController::class, 'show'])->name('buses.seats.show');
        Route::get('buses/{bus}/seats/{seat}/edit', [SeatController::class, 'edit'])->name('buses.seats.edit');
        Route::put('buses/{bus}/seats/{seat}', [SeatController::class, 'update'])->name('buses.seats.update');
        Route::delete('buses/{bus}/seats/{seat}', [SeatController::class, 'destroy'])->name('buses.seats.destroy');
        Route::get('buses/{bus}/seat-availability', [SeatController::class, 'availability'])->name('buses.seats.availability');
    });

    // Trip management routes
    Route::middleware('admin')->group(function () {
        Route::resource('trips', TripController::class);
        Route::get('trips/{trip}/seat-availability', [TripController::class, 'seatAvailability'])->name('trips.seat-availability');
        Route::post('trips/{trip}/cancel', [TripController::class, 'cancel'])->name('trips.cancel');
    });

    // Booking routes
    Route::resource('bookings', BookingController::class)->except(['edit', 'update']);
    Route::get('trips/{trip}/book', [BookingController::class, 'create'])->name('trips.book');
    Route::post('trips/{trip}/book', [BookingController::class, 'store'])->name('trips.book.store')->middleware('booking.limiter');
    Route::get('bookings/{booking}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
    Route::get('bookings/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('bookings.confirmation');
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('bookings/{booking}/passenger', [BookingController::class, 'updatePassenger'])->name('bookings.update-passenger');
    Route::get('bookings/history/tickets', [BookingController::class, 'ticketHistory'])->name('bookings.ticket-history');

    // Ticket routes
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{booking}/print', [TicketController::class, 'print'])->name('tickets.print');
    Route::get('/tickets/{booking}/download', [TicketController::class, 'download'])->name('tickets.download');

    // Payment routes
    Route::resource('payments', PaymentController::class)->only(['index', 'show']);
    Route::post('bookings/{booking}/payment', [PaymentController::class, 'process'])->name('payments.process');
    Route::get('payments/{payment}/callback/{gateway}', [PaymentController::class, 'callback'])->name('payments.callback');
    Route::post('payments/{payment}/confirm-cash', [PaymentController::class, 'confirmCash'])->name('payments.confirm-cash');

    // Operator routes
    Route::resource('operators', OperatorController::class);
    
    // Operator Booking Routes (Counter Sales)
    Route::middleware('operator')->prefix('operator')->name('operator.')->group(function () {
        Route::get('/booking', [OperatorBookingController::class, 'index'])->name('booking.index');
        Route::get('/booking/customers', [OperatorBookingController::class, 'customerBookings'])->name('booking.customer-bookings');
        Route::get('/booking/trip/{trip}', [OperatorBookingController::class, 'create'])->name('booking.create');
        Route::post('/booking/trip/{trip}', [OperatorBookingController::class, 'store'])->name('booking.store');
        Route::get('/booking/{booking}', [OperatorBookingController::class, 'show'])->name('booking.show');
        Route::get('/booking/{booking}/print', [OperatorBookingController::class, 'print'])->name('booking.print');
        Route::get('/booking/{booking}/download', [OperatorBookingController::class, 'downloadTicket'])->name('booking.download-ticket');
        Route::post('/booking/{booking}/confirm-payment', [OperatorBookingController::class, 'confirmPayment'])->name('booking.confirm-payment');
        Route::get('/trip/{trip}/seat-availability', [OperatorBookingController::class, 'getSeatAvailability'])->name('trip.seat-availability');
    });

    // Operator Bus Management
    Route::middleware('operator')->prefix('operator')->name('operator.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Operator\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('buses', \App\Http\Controllers\Operator\BusController::class);
        Route::get('buses/{bus}/layout/configure', [\App\Http\Controllers\Operator\SeatLayoutController::class, 'configure'])->name('buses.layout.configure');
        Route::post('buses/{bus}/layout/store', [\App\Http\Controllers\Operator\SeatLayoutController::class, 'store'])->name('buses.layout.store');
        Route::get('buses/{bus}/layout/preview', [\App\Http\Controllers\Operator\SeatLayoutController::class, 'preview'])->name('buses.layout.preview');
        Route::post('buses/{bus}/layout/preview-data', [\App\Http\Controllers\Operator\SeatLayoutController::class, 'getPreview'])->name('buses.layout.preview-data');
        Route::post('buses/{bus}/layout/reset', [\App\Http\Controllers\Operator\SeatLayoutController::class, 'reset'])->name('buses.layout.reset');
        Route::resource('routes', \App\Http\Controllers\Operator\RouteController::class);
        Route::resource('trips', \App\Http\Controllers\Operator\TripController::class);

        Route::get('trips/{trip}/seats', [\App\Http\Controllers\Operator\SeatController::class, 'index'])->name('trips.seats.index');
        Route::get('trips/{trip}/seats/{seat}/edit', [\App\Http\Controllers\Operator\SeatController::class, 'edit'])->name('trips.seats.edit');
        Route::put('trips/{trip}/seats/{seat}', [\App\Http\Controllers\Operator\SeatController::class, 'update'])->name('trips.seats.update');
        Route::post('trips/{trip}/seats/block', [\App\Http\Controllers\Operator\SeatController::class, 'block'])->name('trips.seats.block');
        Route::post('trips/{trip}/seats/unblock', [\App\Http\Controllers\Operator\SeatController::class, 'unblock'])->name('trips.seats.unblock');

        Route::get('bookings', [\App\Http\Controllers\Operator\BookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [\App\Http\Controllers\Operator\BookingController::class, 'show'])->name('bookings.show');
        Route::get('trips/{trip}/manifest', [\App\Http\Controllers\Operator\BookingController::class, 'generateManifest'])->name('trips.manifest');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');

    // Operator Management
    Route::get('/operators', [AdminController::class, 'operators'])->name('operators.index');
    Route::get('/operators/create', [AdminController::class, 'createOperator'])->name('operators.create');
    Route::post('/operators', [AdminController::class, 'storeOperator'])->name('operators.store');
    Route::get('/operators/{operator}', [AdminController::class, 'showOperator'])->name('operators.show');
    Route::get('/operators/{operator}/edit', [AdminController::class, 'editOperator'])->name('operators.edit');
    Route::put('/operators/{operator}', [AdminController::class, 'updateOperator'])->name('operators.update');

    // Booking Management
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings.index');

    // Payment Management
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments.index');
});

require __DIR__.'/auth.php';
