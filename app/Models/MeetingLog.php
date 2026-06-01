<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'meeting_date',
        'title',
        'outcome',
        'notes',
        'next_follow_up_at',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
            'next_follow_up_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
