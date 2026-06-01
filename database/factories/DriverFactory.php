<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'pool_id' => null,
            'employee_code' => strtoupper(fake()->unique()->bothify('DRV###??')),
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'license_type' => fake()->randomElement(['A', 'B1', 'B2']),
            'license_number' => strtoupper(fake()->bothify('SIM-########')),
            'license_expired_at' => fake()->dateTimeBetween('+6 months', '+3 years')->format('Y-m-d'),
            'status' => fake()->randomElement(['active', 'inactive', 'sick', 'on_leave']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
