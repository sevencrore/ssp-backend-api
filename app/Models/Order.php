<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'order_status',
        'tracking_number',
        'total_amount',
        'grand_total',
        'discount',
        'supplied_by',
        'vendor_comission_percentage',
        'vendor_comission_total',
        'delivery_otp',
    
    ];

    // Define relationships if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
