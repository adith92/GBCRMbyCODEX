<?php

namespace Database\Factories;

use App\Models\MaintenanceLog;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceLog>
 */
class MaintenanceLogFactory extends Factory
{
    protected $model = MaintenanceLog::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'reported_by' => User::factory(),
            'title' => fake()->sentence(4),
            'status' => fake()->randomElement(['scheduled', 'in_progress', 'completed', 'cancelled']),
            'start_at' => fake()->optional()->dateTimeBetween('-7 days', 'now'),
            'end_at' => fake()->optional()->dateTimeBetween('now', '+7 days'),
            'cost' => fake()->randomFloat(2, 0, 10000000),
            'notes' => fake()->optional()->paragraph(),
        ];
    }
}
