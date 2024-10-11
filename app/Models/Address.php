<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'address'; // Ensure it refers to the singular table name

    protected $fillable = [
        'first_name',
        'last_name',
        'city_id',
        'address',
        'pin_code',
        'phone_number',
        'user_id',
        'latitude',  // Add latitude
        'longitude', // Add longitude
    ];
}
