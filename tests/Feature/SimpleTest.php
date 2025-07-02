<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Operator;
use App\Models\City;
use App\Models\Route;
use App\Models\Bus;
use App\Models\Trip;
use App\Models\Booking;

class SimpleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $operator;
    protected $admin;
    protected $bus;
    protected $trip;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the database
        $this->seed();
        
        // Create test users
        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        
        // Get existing data from seeders
        $this->operator = Operator::first();
        $this->bus = Bus::first();
        $this->trip = Trip::first();
    }

    /**
     * Test basic bus functionality
     */
    public function test_basic_bus_functionality(): void
    {
        $this->assertNotNull($this->bus);
        $this->assertEquals(32, $this->bus->total_seats);
        $this->assertCount(32, $this->bus->seats);
    }

    /**
     * Test basic trip functionality
     */
    public function test_basic_trip_functionality(): void
    {
        $this->assertNotNull($this->trip);
        $this->assertEquals(32, $this->trip->available_seats_count);
    }

    /**
     * Test basic booking creation
     */
    public function test_basic_booking_creation(): void
    {
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'trip_id' => $this->trip->id,
            'booking_code' => 'TEST123456',
            'status' => 'booked',
            'total_amount' => 1500.00,
        ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'trip_id' => $this->trip->id,
            'booking_code' => 'TEST123456',
            'status' => 'booked',
        ]);
    }
}
