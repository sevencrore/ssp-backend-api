<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerVendor extends Model
{
    use HasFactory;

    protected $table = 'customer_vendor'; 
    protected $fillable = ['customer_id', 'vendor_id']; 

    
}
