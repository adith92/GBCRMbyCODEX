<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\MaintenanceLog;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_sales_user_can_search_clients_and_bookings(): void
    {
        $user = User::query()->where('email', 'sales@blueerp.test')->firstOrFail();
        $client = Client::factory()->create(['name' => 'Searchable Client']);
        $booking = Booking::factory()->create([
            'client_id' => $client->id,
            'requested_by' => $user->id,
            'booking_number' => 'BK-SEARCH-1001',
        ]);

        $this->actingAs($user)
            ->get(route('search.index', ['q' => 'Searchable']))
            ->assertOk()
            ->assertSee('Searchable Client')
            ->assertSee($booking->booking_number);
    }

    public function test_search_scope_limits_results_to_requested_module(): void
    {
        $user = User::query()->where('email', 'sales@blueerp.test')->firstOrFail();
        $client = Client::factory()->create(['name' => 'Scoped Alpha Client']);
        $booking = Booking::factory()->create([
            'client_id' => $client->id,
            'requested_by' => $user->id,
            'booking_number' => 'Scoped-Alpha-Booking',
        ]);

        $response = $this->actingAs($user)
            ->get(route('search.index', ['q' => 'Scoped-Alpha', 'scope' => 'booking']))
            ->assertOk();

        $response->assertSee($booking->booking_number);
        $this->assertStringNotContainsString(route('crm.clients.show', $client), $response->getContent());
    }

    public function test_finance_user_search_does_not_show_vehicle_results_without_permission(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $vehicle = Vehicle::factory()->create(['plate_number' => 'B-SEARCH-01']);

        $response = $this->actingAs($user)
            ->get(route('search.index', ['q' => 'B-SEARCH-01']))
            ->assertOk();

        $response->assertSee('Global search');
        $this->assertStringNotContainsString(
            route('fleet.vehicles.show', $vehicle),
            $response->getContent()
        );
    }

    public function test_activity_page_shows_invoice_and_payment_related_items_for_finance(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $invoice = Invoice::query()->firstOrFail();

        $this->actingAs($user)
            ->get(route('activity.index'))
            ->assertOk()
            ->assertSee('Recent activity')
            ->assertSee($invoice->invoice_number);
    }

    public function test_operation_activity_page_shows_maintenance_items(): void
    {
        $user = User::query()->where('email', 'operation@blueerp.test')->firstOrFail();
        $log = MaintenanceLog::query()->firstOrFail();

        $this->actingAs($user)
            ->get(route('activity.index'))
            ->assertOk()
            ->assertSee($log->title);
    }

    public function test_activity_type_filter_limits_timeline_results(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $invoice = Invoice::query()->firstOrFail();
        $payment = Payment::query()->firstOrFail();

        $response = $this->actingAs($user)
            ->get(route('activity.index', ['type' => 'invoice']))
            ->assertOk();

        $response->assertSee($invoice->invoice_number);
        $this->assertStringNotContainsString($payment->payment_number, $response->getContent());
    }
}
