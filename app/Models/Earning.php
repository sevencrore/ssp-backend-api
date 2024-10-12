<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    use HasFactory;

    protected $table = 'earnings'; // Specify the correct table name

    protected $fillable = [
        'user_id',
        'referral_id',
        'sale_id',
        'referral_incentive',
        'sale_value_estimated',
        'sale_actual_value',
        'wallet_amount',
        'self_purchase_total',
        'first_referral_purchase_total',
        'second_referral_purchase_total',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
