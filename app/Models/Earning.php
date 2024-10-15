<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    use HasFactory;

    protected $table = 'earnings'; // Specify the correct table name

    protected $fillable = [
        'referral_incentive',
        'sale_value_estimated',
        'sale_actual_value',
        'wallet_amount',
        'self_purchase_total',
        'first_referral_purchase_total',
        'second_referral_purchase_total',
        'user_id',
    ];

    // Removed user relation since user_id is no longer part of the schema
}
