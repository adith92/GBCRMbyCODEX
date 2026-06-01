<?php

namespace Database\Factories;

use App\Models\Pool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pool>
 */
class PoolFactory extends Factory
{
    protected $model = Pool::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('POOL-##??')),
            'name' => 'Pool '.fake()->city(),
            'location' => fake()->city(),
            'address' => fake()->address(),
            'status' => fake()->randomElement(['active', 'inactive']),
        ];
    }
}
