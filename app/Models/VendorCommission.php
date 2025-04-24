<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCommission extends Model
{
    use HasFactory;
    protected $table = 'vendor_commissions';

    protected $fillable = [
        'vendor_id',
        'order_id',
        'amount',
        'status', //1 for unpaid 2 for paid 3 refunded and 4 for pending
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the order associated with the commission.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
