<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigSetting extends Model
{
    use HasFactory;

    protected $table = 'config_setting';

    protected $fillable = [
        'referal_incentive',
        'max_level',
        'vendor_comission',
        'admin_comission_percentage',
        'tds_charges_percentage',
        'default_vendor_id',
        'minimum_basepay_amount',
        'slideImage_displayCount',
    ];
}
