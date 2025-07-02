<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\City;
use App\Models\Route;
use App\Models\Operator;
use App\Models\Bus;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class CoreFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $operator;
    protected $user;
    protected $bus;
    protected $route;
    protected $trip;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the database
        $this->seed();
        
        // Get test users
        $this->admin = User::where('role', 'admin')->first();
        $this->operator = User::where('role', 'operator')->first();
        $this->user = User::where('role', 'user')->first();
        
        // Get test data
        $this->bus = Bus::first();
        $this->route = Route::first();
        
        // Create a test trip
        $this->trip = Trip::create([
            'bus_id' => $this->bus->id,
            'route_id' => $this->route->id,
            'departure_datetime' => Carbon::tomorrow()->setTime(10, 0),
            'arrival_time' => Carbon::tomorrow()->setTime(16, 0),
            'price' => 1500.00,
            'status' => 'active',
        ]);
    }

    /**
     * Test bus management functionality
     */
    public function test_bus_management_workflow(): void
    {
        // Test operator can create a bus
        $this->actingAs($this->operator);

        $response = $this->post('/buses', [
            'registration_number' => 'BA-1-PA-9999',
            'name' => 'Test Bus',
            'type' => 'AC',
            'total_seats' => 40,
            'features' => ['AC', 'WiFi'],
        ]);

        $response->assertRedirect('/buses');
        $this->assertDatabaseHas('buses', [
            'registration_number' => 'BA-1-PA-9999',
            'operator_id' => $this->operator->operator->id,
        ]);

        // Test seats were auto-generated
        $bus = Bus::where('registration_number', 'BA-1-PA-9999')->first();
        $this->assertEquals(40, $bus->seats()->count());

        // Test bus exists in database
        $this->assertNotNull($bus);
        $this->assertEquals('Test Bus', $bus->name);
        $this->assertEquals('AC', $bus->type);
    }

    /**
     * Test trip management functionality
     */
    public function test_trip_management_workflow(): void
    {
        $this->actingAs($this->operator);

        // Test operator can create a trip
        $response = $this->post('/trips', [
            'bus_id' => $this->bus->id,
            'route_id' => $this->route->id,
            'departure_datetime' => Carbon::tomorrow()->setTime(8, 0)->format('Y-m-d H:i:s'),
            'arrival_time' => Carbon::tomorrow()->setTime(14, 0)->format('Y-m-d H:i:s'),
            'price' => 2000.00,
        ]);

        $response->assertRedirect('/trips');
        $this->assertDatabaseHas('trips', [
            'bus_id' => $this->bus->id,
            'route_id' => $this->route->id,
            'price' => 2000.00,
        ]);

        // Test trip was created successfully
        $trip = Trip::where('bus_id', $this->bus->id)->where('price', 2000.00)->first();
        $this->assertNotNull($trip);
        $this->assertEquals('active', $trip->status);
    }

    /**
     * Test booking workflow
     */
    public function test_booking_workflow(): void
    {
        $this->actingAs($this->user);

        // Debug: Check if trip and bus exist
        $this->assertNotNull($this->trip);
        $this->assertNotNull($this->bus);
        $this->assertTrue($this->bus->seats()->count() > 0);

        // Get available seats
        $availableSeats = $this->trip->bus->seats()->take(2)->pluck('id')->toArray();
        $this->assertCount(2, $availableSeats);

        // Test user can create a booking
        $response = $this->postJson("/trips/{$this->trip->id}/book", [
            'passenger_name' => 'John Doe',
            'passenger_phone' => '9841234567',
            'passenger_email' => 'john@example.com',
            'seat_ids' => $availableSeats,
        ]);

        // Debug the response if it fails
        if ($response->status() !== 200) {
            dump('Response Status: ' . $response->status());
            dump('Response Content: ' . $response->getContent());
        }

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'trip_id' => $this->trip->id,
            'passenger_name' => 'John Doe',
            'status' => 'booked',
        ]);

        // Test booking seats were created
        $booking = Booking::where('user_id', $this->user->id)->first();
        $this->assertEquals(2, $booking->bookingSeats()->count());
        $this->assertEquals(3000.00, $booking->total_amount); // 2 seats * 1500

        // Test seat availability is updated (refresh the trip to get updated count)
        $this->trip->refresh();
        $this->assertEquals($this->bus->total_seats - 2, $this->trip->available_seats_count);
    }

    /**
     * Test payment processing
     */
    public function test_payment_processing(): void
    {
        // Create a booking first
        $booking = Booking::create([
            'user_id' => $this->user->id,
            'trip_id' => $this->trip->id,
            'booking_reference' => 'BNG123456',
            'passenger_name' => 'Jane Doe',
            'passenger_phone' => '9841234567',
            'total_amount' => 1500.00,
            'status' => 'booked',
        ]);

        $this->actingAs($this->user);

        // Test cash payment
        $response = $this->post("/bookings/{$booking->id}/payment", [
            'payment_method' => 'Cash',
            'amount' => 1500.00,
        ]);

        $response->assertRedirect("/bookings/{$booking->id}");

        // Test payment was created
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'method' => 'Cash',
            'amount' => 1500.00,
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Test festival pricing
     */
    public function test_festival_pricing(): void
    {
        // Enable festival mode
        SystemSetting::setValue('festival_mode_enabled', true);
        SystemSetting::setValue('festival_fare_multiplier', 2.0);

        $this->actingAs($this->operator);

        // Create trip with festival pricing
        $response = $this->post('/trips', [
            'bus_id' => $this->bus->id,
            'route_id' => $this->route->id,
            'departure_datetime' => Carbon::tomorrow()->setTime(8, 0)->format('Y-m-d H:i:s'),
            'price' => 1000.00,
            'is_festival_fare' => true,
        ]);

        $response->assertRedirect('/trips');

        // Check that price was multiplied
        $this->assertDatabaseHas('trips', [
            'bus_id' => $this->bus->id,
            'price' => 2000.00, // 1000 * 2.0 multiplier
            'is_festival_fare' => true,
        ]);
    }

    /**
     * Test authorization policies
     */
    public function test_authorization_policies(): void
    {
        // Test regular user cannot create buses
        $this->actingAs($this->user);
        $response = $this->post('/buses', [
            'registration_number' => 'BA-1-PA-8888',
            'name' => 'Unauthorized Bus',
            'type' => 'AC',
            'total_seats' => 40,
        ]);
        $response->assertStatus(403);

        // Test operator can create buses
        $this->actingAs($this->operator);
        $response = $this->post('/buses', [
            'registration_number' => 'BA-1-PA-7777',
            'name' => 'Authorized Bus',
            'type' => 'AC',
            'total_seats' => 40,
        ]);
        $response->assertRedirect('/buses');
    }

    /**
     * Test seat availability checking
     */
    public function test_seat_availability(): void
    {
        // Authenticate as user to access the endpoint
        $this->actingAs($this->user);

        // Get seat availability for trip
        $response = $this->get("/trips/{$this->trip->id}/seat-availability");
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertEquals($this->trip->id, $data['trip_id']);
        $this->assertEquals($this->bus->total_seats, $data['total_seats']);
        $this->assertEquals($this->bus->total_seats, $data['available_count']);
        $this->assertCount($this->bus->total_seats, $data['seats']);
    }
}
