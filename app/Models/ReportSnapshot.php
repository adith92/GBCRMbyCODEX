<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'snapshot_date',
        'type',
        'payload',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'payload' => 'array',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
