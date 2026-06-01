<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'pool_id' => null,
            'plate_number' => strtoupper(fake()->unique()->bothify('B #### ???')),
            'product_line' => fake()->randomElement(['goldenbird', 'bigbird', 'cititrans', 'regular']),
            'brand' => fake()->randomElement(['Toyota', 'Isuzu', 'Mitsubishi', 'Hyundai']),
            'model' => fake()->word(),
            'year' => fake()->numberBetween(2014, 2026),
            'seat_capacity' => fake()->numberBetween(4, 50),
            'status' => fake()->randomElement(['available', 'po', 'maintenance', 'hold']),
            'odometer' => fake()->numberBetween(5000, 300000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
