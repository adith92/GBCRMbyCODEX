<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Driver;
use App\Models\DriverAssignment;
use App\Models\Pool;
use App\Models\Vehicle;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreDatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrate_fresh_seed_runs_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('pools', 3);
        $this->assertDatabaseCount('clients', 10);
        $this->assertDatabaseCount('vehicles', 15);
        $this->assertDatabaseCount('drivers', 10);
    }

    public function test_client_contacts_relationship_works(): void
    {
        $this->seed(DatabaseSeeder::class);

        $client = Client::query()->firstOrFail();

        $this->assertGreaterThan(0, $client->contacts()->count());
    }

    public function test_vehicle_belongs_to_pool(): void
    {
        $this->seed(DatabaseSeeder::class);

        $vehicle = Vehicle::query()->firstOrFail();

        $this->assertNotNull($vehicle->pool);
        $this->assertInstanceOf(Pool::class, $vehicle->pool);
    }

    public function test_booking_can_link_client_vehicle_and_driver(): void
    {
        $this->seed(DatabaseSeeder::class);

        $booking = Booking::query()
            ->with(['client', 'vehicle', 'driver'])
            ->whereNotNull('vehicle_id')
            ->whereNotNull('driver_id')
            ->firstOrFail();

        $this->assertNotNull($booking->client);
        $this->assertNotNull($booking->vehicle);
        $this->assertNotNull($booking->driver);
    }

    public function test_driver_assignment_can_be_created(): void
    {
        $this->seed(DatabaseSeeder::class);

        $booking = Booking::query()->firstOrFail();
        $driver = Driver::query()->firstOrFail();
        $vehicle = Vehicle::query()->firstOrFail();

        $assignment = DriverAssignment::factory()->create([
            'booking_id' => $booking->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'assigned_by' => null,
        ]);

        $this->assertDatabaseHas('driver_assignments', ['id' => $assignment->id]);
    }
}
