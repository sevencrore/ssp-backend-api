<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReferral extends Model
{
    use HasFactory;

    protected $table = 'user_referral';

    protected $fillable = [
        'reg_user_id',
        'referral_id',
    ];

    // If you need relationships, you can define them here
    public function registeredUser()
    {
        return $this->belongsTo(User::class, 'reg_user_id');
    }

    public function referralUser()
    {
        return $this->belongsTo(User::class, 'referral_id');
    }
}
