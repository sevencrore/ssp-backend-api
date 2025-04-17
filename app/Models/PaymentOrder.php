<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'payment_orders';

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'currency',
        'status',
        'notes',
    ];

    protected $casts = [
        'notes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
