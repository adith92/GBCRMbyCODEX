<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Driver;
use App\Models\DriverAssignment;
use App\Models\MeetingLog;
use App\Models\Pool;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class CoreDataSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $pools = Pool::factory()->count(3)->create();
        $clients = Client::factory()->count(10)->create();

        $clients->each(function (Client $client): void {
            ClientContact::factory()->count(2)->create([
                'client_id' => $client->id,
            ]);

            MeetingLog::factory()->count(2)->create([
                'client_id' => $client->id,
                'user_id' => User::query()->inRandomOrder()->value('id'),
            ]);
        });

        $vehicles = collect();
        for ($i = 0; $i < 15; $i++) {
            $vehicles->push(Vehicle::factory()->create([
                'pool_id' => $pools->random()->id,
            ]));
        }

        $drivers = collect();
        for ($i = 0; $i < 10; $i++) {
            $drivers->push(Driver::factory()->create([
                'pool_id' => $pools->random()->id,
            ]));
        }

        for ($i = 0; $i < 8; $i++) {
            $booking = Booking::factory()->create([
                'client_id' => $clients->random()->id,
                'pool_id' => $pools->random()->id,
                'vehicle_id' => $vehicles->random()->id,
                'driver_id' => $drivers->random()->id,
                'requested_by' => $users->random()->id,
            ]);

            DriverAssignment::factory()->create([
                'booking_id' => $booking->id,
                'vehicle_id' => $booking->vehicle_id,
                'driver_id' => $booking->driver_id,
                'assigned_by' => $users->random()->id,
            ]);
        }
    }
}
