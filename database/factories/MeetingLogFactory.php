<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\MeetingLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingLog>
 */
class MeetingLogFactory extends Factory
{
    protected $model = MeetingLog::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'user_id' => User::factory(),
            'meeting_date' => fake()->date(),
            'title' => fake()->sentence(4),
            'outcome' => fake()->randomElement(['prospecting', 'follow_up', 'negotiation', 'closed_won', 'closed_lost']),
            'notes' => fake()->optional()->paragraph(),
            'next_follow_up_at' => fake()->optional()->dateTimeBetween('+1 day', '+30 days'),
        ];
    }
}
