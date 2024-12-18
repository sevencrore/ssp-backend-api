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
    ];
}
