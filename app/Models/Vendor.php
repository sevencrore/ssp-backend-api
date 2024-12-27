<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'business_name',
        'phone_1',
        'phone_2',
        'aadhar_number',
        'address',
        'pincode',
        'latitude',
        'longitude',
    ];
}
