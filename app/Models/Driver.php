<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'pool_id',
        'employee_code',
        'name',
        'phone',
        'email',
        'license_type',
        'license_number',
        'license_expired_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'license_expired_at' => 'date',
        ];
    }

    public function pool(): BelongsTo
    {
        return $this->belongsTo(Pool::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DriverAssignment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(DriverAttendance::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
