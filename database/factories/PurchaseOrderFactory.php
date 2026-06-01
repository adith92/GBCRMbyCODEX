<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Client;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 1000000, 30000000);
        $tax = round($subtotal * 0.11, 2);

        return [
            'po_number' => strtoupper(fake()->unique()->bothify('PO-########')),
            'booking_id' => Booking::factory(),
            'client_id' => Client::factory(),
            'status' => fake()->randomElement(['draft', 'pending', 'approved', 'invoiced', 'cancelled']),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'approved_by' => User::factory(),
            'approved_at' => fake()->optional()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
