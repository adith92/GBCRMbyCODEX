<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-7 days', '+7 days');

        return [
            'booking_number' => strtoupper(fake()->unique()->bothify('BKG-########')),
            'client_id' => null,
            'requested_by' => null,
            'pool_id' => null,
            'vehicle_id' => null,
            'driver_id' => null,
            'start_datetime' => $start,
            'end_datetime' => (clone $start)->modify('+4 hours'),
            'pickup_location' => fake()->address(),
            'destination' => fake()->address(),
            'status' => fake()->randomElement(['pending', 'assigned', 'confirmed', 'completed', 'cancelled']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
