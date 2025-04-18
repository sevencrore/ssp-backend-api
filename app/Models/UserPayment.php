<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'amount',
        'email',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'status',
    ];
}
