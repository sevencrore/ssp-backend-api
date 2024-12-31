<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComissionHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comission_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'comission_type',
        'referal_id',
        'amount',
        'description',
    ];

    /**
     * Define the relationship with the User model (user_id).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define the relationship with the User model (referal_id).
     */
    public function referral()
    {
        return $this->belongsTo(User::class, 'referal_id');
    }
}
