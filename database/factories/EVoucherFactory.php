<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\EVoucher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EVoucher>
 */
class EVoucherFactory extends Factory
{
    protected $model = EVoucher::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 50000, 2000000);
        $expiredAt = fake()->optional()->dateTimeBetween('+1 month', '+1 year');

        return [
            'code' => strtoupper(fake()->unique()->bothify('EVC-########')),
            'client_id' => Client::factory(),
            'status' => fake()->randomElement(['active', 'used', 'expired', 'cancelled']),
            'amount' => $amount,
            'used_amount' => fake()->randomFloat(2, 0, $amount),
            'expired_at' => $expiredAt?->format('Y-m-d'),
            'used_at' => fake()->optional()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
