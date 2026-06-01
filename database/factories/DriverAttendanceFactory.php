<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\DriverAttendance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DriverAttendance>
 */
class DriverAttendanceFactory extends Factory
{
    protected $model = DriverAttendance::class;

    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory(),
            'attendance_date' => fake()->date(),
            'status' => fake()->randomElement(['present', 'absent', 'sick', 'leave']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
