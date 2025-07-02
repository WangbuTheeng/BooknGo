<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\City;
use App\Models\Route;
use App\Models\Operator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test database seeding and basic model relationships
     */
    public function test_database_seeding_and_relationships(): void
    {
        // Run seeders
        $this->seed();

        // Test cities were seeded
        $this->assertDatabaseCount('cities', 20);
        $kathmandu = City::where('name', 'Kathmandu')->first();
        $this->assertNotNull($kathmandu);
        $this->assertEquals('Bagmati', $kathmandu->province);

        // Test users were seeded
        $this->assertDatabaseCount('users', 5); // 1 admin + 2 operators + 2 regular users
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());

        // Test operators were seeded
        $this->assertDatabaseCount('operators', 2);
        $operator = Operator::where('company_name', 'Greenline Tours')->first();
        $this->assertNotNull($operator);
        $this->assertTrue($operator->verified);

        // Test routes were seeded
        $this->assertDatabaseCount('routes', 12);
        $route = Route::where('from_city_id', $kathmandu->id)->first();
        $this->assertNotNull($route);

        // Test relationships
        $this->assertNotNull($operator->user);
        $this->assertEquals('operator', $operator->user->role);

        $this->assertNotNull($route->fromCity);
        $this->assertNotNull($route->toCity);
        $this->assertEquals('Kathmandu', $route->fromCity->name);
    }

    /**
     * Test authentication pages are accessible
     */
    public function test_authentication_pages_accessible(): void
    {
        // Test login page
        $response = $this->get('/login');
        $response->assertStatus(200);

        // Test register page
        $response = $this->get('/register');
        $response->assertStatus(200);

        // Test dashboard requires authentication
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test user can login with seeded credentials
     */
    public function test_user_can_login_with_seeded_credentials(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'admin@bookngo.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
