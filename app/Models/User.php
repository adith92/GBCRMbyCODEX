<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function meetingLogs(): HasMany
    {
        return $this->hasMany(MeetingLog::class);
    }

    public function requestedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'requested_by');
    }

    public function assignedDriverAssignments(): HasMany
    {
        return $this->hasMany(DriverAssignment::class, 'assigned_by');
    }

    public function approvedPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    public function createdPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function reportedMaintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class, 'reported_by');
    }

    public function reportSnapshots(): HasMany
    {
        return $this->hasMany(ReportSnapshot::class, 'created_by');
    }
}
