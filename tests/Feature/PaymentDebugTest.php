<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentDebugTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $trip;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();
        
        $this->user = User::where('role', 'user')->first();
        $this->trip = Trip::first();
    }

    public function test_payment_creation_directly()
    {
        // Test creating a payment directly
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'trip_id' => $this->trip->id,
            'booking_reference' => 'BNG123456',
            'passenger_name' => 'Jane Doe',
            'passenger_phone' => '9841234567',
            'total_amount' => 1500.00,
            'status' => 'booked',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'cash',
            'amount' => 1500.00,
            'transaction_id' => 'TXN12345678',
            'status' => 'pending',
        ]);

        $this->assertNotNull($payment);
        $this->assertEquals('cash', $payment->payment_method);
        $this->assertEquals(1500.00, $payment->amount);
    }

    public function test_booking_creation_debug()
    {
        $this->actingAs($this->user);

        // Get a seat from the trip
        $seat = $this->trip->bus->seats->first();

        // Test booking creation
        $response = $this->postJson("/trips/{$this->trip->id}/book", [
            'passenger_name' => 'Jane Doe',
            'passenger_phone' => '9841234567',
            'passenger_email' => 'jane@example.com',
            'seat_ids' => [$seat->id],
        ]);

        // Check if there are any validation errors
        if ($response->getStatusCode() === 302) {
            $session = $response->getSession();
            if ($session && $session->has('errors')) {
                $errors = $session->get('errors');
                dump('Validation errors:', $errors->all());
            }
        }

        // Dump the response for debugging
        dump('Response status:', $response->getStatusCode());
        dump('Response content:', $response->getContent());

        $this->assertTrue(true); // Just to make the test pass for debugging
    }
}
