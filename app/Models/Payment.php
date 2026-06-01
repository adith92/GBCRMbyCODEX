<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'e_voucher_id',
        'payment_number',
        'paid_at',
        'amount',
        'method',
        'reference_number',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function eVoucher(): BelongsTo
    {
        return $this->belongsTo(EVoucher::class, 'e_voucher_id');
    }
}
