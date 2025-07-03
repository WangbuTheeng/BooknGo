<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OperatorController;
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
Route::post('/trips/{trip}/book', [BookingController::class, 'store'])->name('trips.book.store');

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
    Route::get('buses/{bus}/seats', [SeatController::class, 'index'])->name('buses.seats.index');
    Route::get('buses/{bus}/seats/create', [SeatController::class, 'create'])->name('buses.seats.create');
    Route::post('buses/{bus}/seats', [SeatController::class, 'store'])->name('buses.seats.store');
    Route::get('buses/{bus}/seats/{seat}', [SeatController::class, 'show'])->name('buses.seats.show');
    Route::get('buses/{bus}/seats/{seat}/edit', [SeatController::class, 'edit'])->name('buses.seats.edit');
    Route::put('buses/{bus}/seats/{seat}', [SeatController::class, 'update'])->name('buses.seats.update');
    Route::delete('buses/{bus}/seats/{seat}', [SeatController::class, 'destroy'])->name('buses.seats.destroy');
    Route::get('buses/{bus}/seat-availability', [SeatController::class, 'availability'])->name('buses.seats.availability');

    // Trip management routes
    Route::resource('trips', TripController::class);
    Route::get('trips/{trip}/seat-availability', [TripController::class, 'seatAvailability'])->name('trips.seat-availability');
    Route::post('trips/{trip}/cancel', [TripController::class, 'cancel'])->name('trips.cancel');

    // Booking routes
    Route::resource('bookings', BookingController::class)->except(['edit', 'update']);
    Route::get('trips/{trip}/book', [BookingController::class, 'create'])->name('trips.book');
    Route::post('trips/{trip}/book', [BookingController::class, 'store'])->name('trips.book.store');
    Route::get('bookings/{booking}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
    Route::get('bookings/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('bookings.confirmation');
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('bookings/{booking}/passenger', [BookingController::class, 'updatePassenger'])->name('bookings.update-passenger');

    // Payment routes
    Route::resource('payments', PaymentController::class)->only(['index', 'show']);
    Route::post('bookings/{booking}/payment', [PaymentController::class, 'process'])->name('payments.process');
    Route::get('payments/{payment}/callback', [PaymentController::class, 'callback'])->name('payments.callback');
    Route::post('payments/{payment}/confirm-cash', [PaymentController::class, 'confirmCash'])->name('payments.confirm-cash');

    // eSewa payment callbacks
    Route::get('payments/esewa/success', [PaymentController::class, 'esewaSuccess'])->name('payments.esewa.success');
    Route::get('payments/esewa/failure', [PaymentController::class, 'esewaFailure'])->name('payments.esewa.failure');

    // Stripe payment callbacks
    Route::get('payments/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('payments.stripe.success');
    Route::get('payments/stripe/failure', [PaymentController::class, 'stripeFailure'])->name('payments.stripe.failure');

    // Operator routes
    Route::resource('operators', OperatorController::class);
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
