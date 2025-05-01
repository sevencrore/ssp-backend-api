<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
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
    // Vendor.php
    public function vendorCommissions()
    {
        return $this->hasMany(VendorCommission::class, 'vendor_id');
    }
}
