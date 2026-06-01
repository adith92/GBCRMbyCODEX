<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 1000000, 30000000);
        $tax = round($subtotal * 0.11, 2);
        $total = $subtotal + $tax;

        return [
            'invoice_number' => strtoupper(fake()->unique()->bothify('INV-########')),
            'purchase_order_id' => PurchaseOrder::factory(),
            'client_id' => Client::factory(),
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled']),
            'issued_at' => fake()->optional()->date(),
            'due_at' => fake()->optional()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'paid_amount' => fake()->randomFloat(2, 0, $total),
        ];
    }
}
