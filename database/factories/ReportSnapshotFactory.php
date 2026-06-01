<?php

namespace Database\Factories;

use App\Models\ReportSnapshot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReportSnapshot>
 */
class ReportSnapshotFactory extends Factory
{
    protected $model = ReportSnapshot::class;

    public function definition(): array
    {
        return [
            'snapshot_date' => fake()->date(),
            'type' => fake()->randomElement(['daily-kpi', 'fleet', 'finance', 'crm']),
            'payload' => [
                'metric' => fake()->word(),
                'value' => fake()->numberBetween(1, 1000),
            ],
            'created_by' => User::factory(),
        ];
    }
}
