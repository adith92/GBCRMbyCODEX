<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EVoucher extends Model
{
    use HasFactory;

    protected $table = 'e_vouchers';

    protected $fillable = [
        'code',
        'client_id',
        'status',
        'amount',
        'used_amount',
        'expired_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'used_amount' => 'decimal:2',
            'expired_at' => 'date',
            'used_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
