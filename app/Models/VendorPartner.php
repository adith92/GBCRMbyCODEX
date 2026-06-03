<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'service_type',
        'contact_person',
        'phone',
        'email',
        'city',
        'status',
        'notes',
    ];
}
