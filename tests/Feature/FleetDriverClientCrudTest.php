<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Driver;
use App\Models\Pool;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FleetDriverClientCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_without_permission_cannot_access_vehicles(): void
    {
        $user = User::query()->where('email', 'sales@blueerp.test')->firstOrFail();

        $response = $this->actingAs($user)->get('/fleet/vehicles');

        $response->assertForbidden();
    }

    public function test_user_with_vehicles_view_can_access_vehicles(): void
    {
        $user = User::query()->where('email', 'operation@blueerp.test')->firstOrFail();

        $response = $this->actingAs($user)->get('/fleet/vehicles');

        $response->assertOk();
    }

    public function test_create_vehicle_validation_works(): void
    {
        $user = User::query()->where('email', 'operation@blueerp.test')->firstOrFail();

        $response = $this->actingAs($user)->post('/fleet/vehicles', [
            'plate_number' => '',
            'product_line' => 'unknown',
            'status' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['plate_number', 'product_line', 'status']);
    }

    public function test_create_client_with_contact_works(): void
    {
        $user = User::query()->where('email', 'salesmanager@blueerp.test')->firstOrFail();

        $clientResponse = $this->actingAs($user)->post('/crm/clients', [
            'code' => 'CL-TST-001',
            'name' => 'Test Client',
            'tier' => 'gold',
            'status' => 'active',
        ]);

        $clientResponse->assertRedirect();

        $client = Client::query()->where('code', 'CL-TST-001')->firstOrFail();

        $contactResponse = $this->actingAs($user)->post("/crm/clients/{$client->id}/contacts", [
            'name' => 'PIC Name',
            'email' => 'pic@test.com',
            'is_primary' => true,
        ]);

        $contactResponse->assertRedirect();

        $this->assertDatabaseHas('client_contacts', [
            'client_id' => $client->id,
            'name' => 'PIC Name',
        ]);
    }

    public function test_create_driver_works(): void
    {
        $user = User::query()->where('email', 'superadmin@blueerp.test')->firstOrFail();
        $pool = Pool::query()->firstOrFail();

        $response = $this->actingAs($user)->post('/drivers', [
            'pool_id' => $pool->id,
            'employee_code' => 'DRV-TST-001',
            'name' => 'Driver Baru',
            'status' => 'active',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('drivers', [
            'employee_code' => 'DRV-TST-001',
            'name' => 'Driver Baru',
        ]);
    }
}
