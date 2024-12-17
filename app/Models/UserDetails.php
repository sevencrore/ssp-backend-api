<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    protected $table = 'user_details'; // You can change this to a singular name if needed

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'phone_1',
        'phone_2',
        'email',
        'user_id',
        'aadhar_number',
        'referral_code',
        'comission_id',
        'is_first_order_completed',
        'referred_by',
    ];
}
