<?php

namespace Tests\Feature;

use App\Livewire\Maintenance\Create as MaintenanceCreate;
use App\Livewire\Pool\AssignBooking;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Driver;
use App\Models\DriverAttendance;
use App\Models\Invoice;
use App\Models\MaintenanceLog;
use App\Models\Pool;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OperationsDashboardDrilldownTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_operation_user_can_create_maintenance(): void
    {
        $user = User::query()->where('email', 'operation@blueerp.test')->firstOrFail();
        $vehicle = Vehicle::query()
            ->where('status', 'available')
            ->whereDoesntHave('maintenanceLogs', fn ($query) => $query->whereIn('status', ['scheduled', 'in_progress']))
            ->whereDoesntHave('bookings', fn ($query) => $query->whereIn('status', ['assigned', 'confirmed']))
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(MaintenanceCreate::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('title', 'Emergency service')
            ->set('status', 'scheduled')
            ->set('cost', 200000)
            ->call('save');

        $this->assertDatabaseHas('maintenance_logs', [
            'vehicle_id' => $vehicle->id,
            'title' => 'Emergency service',
        ]);
    }

    public function test_maintenance_in_progress_changes_vehicle_status_to_maintenance(): void
    {
        $user = User::query()->where('email', 'operation@blueerp.test')->firstOrFail();
        $vehicle = Vehicle::query()
            ->where('status', 'available')
            ->whereDoesntHave('maintenanceLogs', fn ($query) => $query->whereIn('status', ['scheduled', 'in_progress']))
            ->whereDoesntHave('bookings', fn ($query) => $query->whereIn('status', ['assigned', 'confirmed']))
            ->firstOrFail();

        Livewire::actingAs($user)
            ->test(MaintenanceCreate::class)
            ->set('vehicle_id', $vehicle->id)
            ->set('title', 'Engine work')
            ->set('status', 'in_progress')
            ->set('cost', 500000)
            ->call('save');

        $this->assertSame('maintenance', $vehicle->fresh()->status);
    }

    public function test_completed_maintenance_returns_vehicle_to_available(): void
    {
        $user = User::query()->where('email', 'operation@blueerp.test')->firstOrFail();
        $vehicle = Vehicle::query()
            ->where('status', 'available')
            ->whereDoesntHave('maintenanceLogs', fn ($query) => $query->whereIn('status', ['scheduled', 'in_progress']))
            ->whereDoesntHave('bookings', fn ($query) => $query->whereIn('status', ['assigned', 'confirmed']))
            ->firstOrFail();

        $log = MaintenanceLog::query()->create([
            'vehicle_id' => $vehicle->id,
            'reported_by' => $user->id,
            'title' => 'Body repair',
            'status' => 'in_progress',
            'cost' => 100000,
        ]);

        $vehicle->update(['status' => 'maintenance']);

        app(\App\Services\MaintenanceService::class)->update($log, [
            'vehicle_id' => $vehicle->id,
            'title' => 'Body repair',
            'status' => 'completed',
            'start_at' => null,
            'end_at' => now()->format('Y-m-d H:i:s'),
            'cost' => 100000,
            'notes' => 'Done',
        ]);

        $this->assertSame('available', $vehicle->fresh()->status);
    }

    public function test_vehicle_in_maintenance_cannot_be_assigned_to_booking(): void
    {
        $user = User::query()->where('email', 'headpool@blueerp.test')->firstOrFail();
        $pool = Pool::factory()->create(['status' => 'active']);
        $client = Client::factory()->create(['status' => 'active']);
        $vehicle = Vehicle::factory()->create(['pool_id' => $pool->id, 'status' => 'maintenance']);
        $driver = Driver::factory()->create(['pool_id' => $pool->id, 'status' => 'active']);
        $booking = Booking::factory()->create([
            'client_id' => $client->id,
            'requested_by' => User::query()->where('email', 'sales@blueerp.test')->firstOrFail()->id,
            'pool_id' => $pool->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($user)
            ->test(AssignBooking::class, ['booking' => $booking])
            ->set('vehicle_id', $vehicle->id)
            ->set('driver_id', $driver->id)
            ->call('save')
            ->assertHasErrors(['vehicle_id']);
    }

    public function test_super_admin_can_access_hr_routes(): void
    {
        $user = User::query()->where('email', 'superadmin@blueerp.test')->firstOrFail();

        $this->actingAs($user)->get('/admin/hr/drivers')->assertOk();
        $this->actingAs($user)->get('/admin/hr/attendance')->assertOk();
        $this->actingAs($user)->get('/admin/hr/licenses')->assertOk();
    }

    public function test_finance_and_pool_staff_cannot_access_hr_routes(): void
    {
        $finance = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $poolStaff = User::query()->where('email', 'poolstaff@blueerp.test')->firstOrFail();

        $this->actingAs($finance)->get('/admin/hr/drivers')->assertForbidden();
        $this->actingAs($poolStaff)->get('/admin/hr/attendance')->assertForbidden();
    }

    public function test_hr_menu_not_visible_for_non_super_admin(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();

        $this->actingAs($user)->get('/dashboard')->assertDontSee('HR (Backend)');
    }

    public function test_gm_can_access_dashboard_and_dashboard_renders_kpi_labels(): void
    {
        $gm = User::query()->where('email', 'gm@blueerp.test')->firstOrFail();

        $this->actingAs($gm)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Total Active Clients')
            ->assertSee('Active Bookings')
            ->assertSee('Available Vehicles')
            ->assertSee('Overdue Invoices')
            ->assertSee('Workspace Search')
            ->assertSee('Recent Activity')
            ->assertSee(route('crm.clients.index', ['status' => 'active']), false)
            ->assertSee(route('pool.queue'), false)
            ->assertSee(route('search.index'), false)
            ->assertSee(route('activity.index'), false);
    }

    public function test_dashboard_counts_update_from_seeded_data(): void
    {
        $gm = User::query()->where('email', 'gm@blueerp.test')->firstOrFail();
        $activeBookings = Booking::query()->whereIn('status', ['pending', 'assigned', 'confirmed'])->count();

        $this->actingAs($gm)->get('/dashboard')->assertSee((string) $activeBookings);
    }

    public function test_booking_detail_contains_client_link(): void
    {
        $user = User::query()->where('email', 'sales@blueerp.test')->firstOrFail();
        $booking = Booking::query()->with('client')->firstOrFail();

        $this->actingAs($user)
            ->get(route('bookings.show', $booking))
            ->assertSee(route('crm.clients.show', $booking->client), false);
    }

    public function test_client_detail_contains_booking_and_invoice_section(): void
    {
        $user = User::query()->where('email', 'sales@blueerp.test')->firstOrFail();
        $client = Client::query()->whereHas('bookings')->whereHas('invoices')->firstOrFail();

        $this->actingAs($user)
            ->get(route('crm.clients.show', $client))
            ->assertSee('Booking History')
            ->assertSee('Invoices');
    }

    public function test_invoice_detail_links_to_client_and_po(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $invoice = Invoice::query()->with(['client', 'purchaseOrder'])->firstOrFail();

        $this->actingAs($user)
            ->get(route('finance.invoices.show', $invoice))
            ->assertSee(route('crm.clients.show', $invoice->client), false)
            ->assertSee(route('finance.purchase-orders.show', $invoice->purchaseOrder), false);
    }

    public function test_finance_and_pool_index_pages_render_breadcrumb_navigation(): void
    {
        $finance = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $pool = User::query()->where('email', 'headpool@blueerp.test')->firstOrFail();

        $this->actingAs($finance)
            ->get(route('finance.purchase-orders.index'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Finance')
            ->assertSee('Purchase Orders');

        $this->actingAs($pool)
            ->get(route('pool.queue'))
            ->assertOk()
            ->assertSee('Pool Queue');
    }
}
