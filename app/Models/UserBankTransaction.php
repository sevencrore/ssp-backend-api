<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankTransaction extends Model
{
    use HasFactory;

    protected $table = 'user_bank_transactions';

    protected $fillable = [
        'user_id',
        'commission_ids_array',
        'customer_name',
        'bank_name',
        'branch',
        'ifsc_code',
        'account_number',
        'amount',
        'admin_charges',
        'tsd_charges',
        'amount_tobe_paid',
        'status',
        'utr_number',
        'failure_reason',
        'processed_at',
    ];

    protected $casts = [
        'commission_ids_array' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
