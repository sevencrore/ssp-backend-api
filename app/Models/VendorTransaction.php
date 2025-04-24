<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorTransaction extends Model
{
    use HasFactory;

    protected $table = 'vendor_transactions';

    protected $fillable = [
        'vendor_id',
        'paid_by_user_id',
        'vendor_commission_id_array',
        'total_amount',
        'admin_commission_amount',
        'tds_charges_amount',
        'amount_paid',
        'transaction_id',
        'status', // 1 for unpaid 2 for paid 3 refunded and 4 for pending
        'attachment',
    ];

    protected $casts = [
        'vendor_commission_id_array' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }
}
