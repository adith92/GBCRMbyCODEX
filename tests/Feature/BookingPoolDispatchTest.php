<?php

namespace Tests\Feature;

use App\Livewire\Bookings\Create as BookingCreate;
use App\Livewire\Bookings\Show as BookingShow;
use App\Livewire\Pool\AssignBooking;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Driver;
use App\Models\DriverAssignment;
use App\Models\Pool;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BookingPoolDispatchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_with_bookings_create_can_create_booking(): void
    {
        $user = User::query()->where('email', 'sales@blueerp.test')->firstOrFail();
        $client = Client::query()->firstOrFail();
        $before = Booking::query()->count();

        Livewire::actingAs($user)
            ->test(BookingCreate::class)
            ->set('client_id', $client->id)
            ->set('start_datetime', now()->addDay()->format('Y-m-d\TH:i'))
            ->set('end_datetime', now()->addDays(2)->format('Y-m-d\TH:i'))
            ->call('save');

        $this->assertSame($before + 1, Booking::query()->count());
    }

    public function test_booking_number_auto_generated(): void
    {
        $user = User::query()->where('email', 'sales@blueerp.test')->firstOrFail();
        $client = Client::query()->firstOrFail();

        Livewire::actingAs($user)
            ->test(BookingCreate::class)
            ->set('client_id', $client->id)
            ->set('start_datetime', now()->addDay()->format('Y-m-d\TH:i'))
            ->set('end_datetime', now()->addDays(2)->format('Y-m-d\TH:i'))
            ->call('save');

        $booking = Booking::query()->latest('id')->firstOrFail();

        $this->assertMatchesRegularExpression('/^BK-\d{6}-\d{4}$/', $booking->booking_number);
    }

    public function test_guest_cannot_access_bookings(): void
    {
        $this->get('/bookings')->assertRedirect('/login');
    }

    public function test_user_without_bookings_view_cannot_access_bookings(): void
    {
        $user = User::query()->where('email', 'operation@blueerp.test')->firstOrFail();

        $this->actingAs($user)->get('/bookings')->assertForbidden();
    }

    public function test_pool_user_can_view_pool_queue(): void
    {
        $user = User::query()->where('email', 'headpool@blueerp.test')->firstOrFail();

        $this->actingAs($user)->get('/pool/queue')->assertOk();
    }

    public function test_assign_driver_and_vehicle_changes_booking_to_assigned(): void
    {
        [$user, $booking, $vehicle, $driver] = $this->assignFixture();

        Livewire::actingAs($user)
            ->test(AssignBooking::class, ['booking' => $booking])
            ->set('vehicle_id', $vehicle->id)
            ->set('driver_id', $driver->id)
            ->call('save');

        $booking->refresh();
        $this->assertSame('assigned', $booking->status);
    }

    public function test_assignment_creates_driver_assignments_row(): void
    {
        [$user, $booking, $vehicle, $driver] = $this->assignFixture();

        Livewire::actingAs($user)
            ->test(AssignBooking::class, ['booking' => $booking])
            ->set('vehicle_id', $vehicle->id)
            ->set('driver_id', $driver->id)
            ->call('save');

        $this->assertDatabaseHas('driver_assignments', [
            'booking_id' => $booking->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'assignment_type' => 'primary',
        ]);
    }

    public function test_assigned_vehicle_status_becomes_po(): void
    {
        [$user, $booking, $vehicle, $driver] = $this->assignFixture();

        Livewire::actingAs($user)
            ->test(AssignBooking::class, ['booking' => $booking])
            ->set('vehicle_id', $vehicle->id)
            ->set('driver_id', $driver->id)
            ->call('save');

        $vehicle->refresh();
        $this->assertSame('po', $vehicle->status);
    }

    public function test_cannot_assign_unavailable_vehicle(): void
    {
        [$user, $booking, $vehicle, $driver] = $this->assignFixture();
        $vehicle->update(['status' => 'maintenance']);

        Livewire::actingAs($user)
            ->test(AssignBooking::class, ['booking' => $booking])
            ->set('vehicle_id', $vehicle->id)
            ->set('driver_id', $driver->id)
            ->call('save')
            ->assertHasErrors(['vehicle_id']);
    }

    public function test_cannot_double_assign_driver_on_overlapping_booking(): void
    {
        [$user, $booking, $vehicle, $driver] = $this->assignFixture();
        $otherVehicle = Vehicle::factory()->create([
            'pool_id' => $booking->pool_id,
            'status' => 'available',
        ]);

        Booking::query()->create([
            'booking_number' => 'BK-209901-9999',
            'client_id' => $booking->client_id,
            'pool_id' => $booking->pool_id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $driver->id,
            'start_datetime' => $booking->start_datetime,
            'end_datetime' => $booking->end_datetime,
            'status' => 'assigned',
        ]);

        Livewire::actingAs($user)
            ->test(AssignBooking::class, ['booking' => $booking])
            ->set('vehicle_id', $vehicle->id)
            ->set('driver_id', $driver->id)
            ->call('save')
            ->assertHasErrors(['driver_id']);
    }

    public function test_confirm_assigned_booking_changes_status_to_confirmed(): void
    {
        [$user, $booking, $vehicle, $driver] = $this->assignFixture();

        Livewire::actingAs($user)
            ->test(AssignBooking::class, ['booking' => $booking])
            ->set('vehicle_id', $vehicle->id)
            ->set('driver_id', $driver->id)
            ->call('save');

        $approver = User::query()->where('email', 'salesmanager@blueerp.test')->firstOrFail();

        Livewire::actingAs($approver)
            ->test(BookingShow::class, ['booking' => $booking->fresh()])
            ->call('confirm');

        $this->assertSame('confirmed', $booking->fresh()->status);
    }

    public function test_cannot_confirm_booking_without_driver_or_vehicle(): void
    {
        $approver = User::query()->where('email', 'salesmanager@blueerp.test')->firstOrFail();
        $booking = Booking::query()->firstOrFail();
        $booking->update(['status' => 'assigned', 'vehicle_id' => null, 'driver_id' => null]);

        Livewire::actingAs($approver)
            ->test(BookingShow::class, ['booking' => $booking])
            ->call('confirm')
            ->assertSet('errorMessage', 'Booking requires assigned vehicle and driver before confirmation.');

        $this->assertSame('assigned', $booking->fresh()->status);
    }

    public function test_cancel_assigned_booking_releases_assignment_and_returns_vehicle_available(): void
    {
        [$user, $booking, $vehicle, $driver] = $this->assignFixture();

        Livewire::actingAs($user)
            ->test(AssignBooking::class, ['booking' => $booking])
            ->set('vehicle_id', $vehicle->id)
            ->set('driver_id', $driver->id)
            ->call('save');

        $canceller = User::query()->where('email', 'salesmanager@blueerp.test')->firstOrFail();

        Livewire::actingAs($canceller)
            ->test(BookingShow::class, ['booking' => $booking->fresh()])
            ->call('cancel');

        $booking->refresh();
        $vehicle->refresh();

        $this->assertSame('cancelled', $booking->status);
        $this->assertSame('available', $vehicle->status);
        $this->assertNotNull(DriverAssignment::query()->where('booking_id', $booking->id)->latest()->first()?->released_at);
    }

    private function assignFixture(): array
    {
        $user = User::query()->where('email', 'headpool@blueerp.test')->firstOrFail();
        $pool = Pool::factory()->create(['status' => 'active']);
        $client = Client::factory()->create(['status' => 'active']);

        $booking = Booking::factory()->create([
            'booking_number' => 'BK-209901-0001',
            'client_id' => $client->id,
            'requested_by' => $user->id,
            'pool_id' => $pool->id,
            'vehicle_id' => null,
            'driver_id' => null,
            'start_datetime' => now()->addHours(4),
            'end_datetime' => now()->addHours(8),
            'status' => 'pending',
        ]);

        $vehicle = Vehicle::factory()->create([
            'pool_id' => $pool->id,
            'status' => 'available',
        ]);

        $driver = Driver::factory()->create([
            'pool_id' => $pool->id,
            'status' => 'active',
        ]);

        return [$user, $booking, $vehicle, $driver];
    }
}
