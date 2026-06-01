<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\DriverAssignment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DriverAssignment>
 */
class DriverAssignmentFactory extends Factory
{
    protected $model = DriverAssignment::class;

    public function definition(): array
    {
        $assignedAt = fake()->dateTimeBetween('-3 days', 'now');

        return [
            'booking_id' => Booking::factory(),
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
            'assignment_type' => fake()->randomElement(['primary', 'substitute', 'temporary']),
            'assigned_by' => User::factory(),
            'reason' => fake()->optional()->sentence(),
            'assigned_at' => $assignedAt,
            'released_at' => fake()->optional()->dateTimeBetween($assignedAt, '+3 days'),
        ];
    }
}
