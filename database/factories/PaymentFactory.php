<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'e_voucher_id' => null,
            'payment_number' => strtoupper(fake()->unique()->bothify('PAY-########')),
            'paid_at' => fake()->date(),
            'amount' => fake()->randomFloat(2, 100000, 10000000),
            'method' => fake()->randomElement(['bank_transfer', 'cash', 'evoucher', 'other']),
            'reference_number' => fake()->optional()->bothify('REF-########'),
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
