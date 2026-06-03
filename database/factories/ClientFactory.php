<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('CL###??')),
            'name' => fake()->company(),
            'legal_name' => fake()->company().' Ltd',
            'tier' => fake()->randomElement(['bronze', 'silver', 'gold', 'platinum']),
            'industry' => fake()->randomElement(['logistics', 'technology', 'manufacturing', 'retail', 'services']),
            'tax_number' => fake()->numerify('NPWP-##########'),
            'billing_address' => fake()->address(),
            'status' => fake()->randomElement(['active', 'inactive', 'prospect']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
