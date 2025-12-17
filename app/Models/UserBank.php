<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBank extends Model
{
    use HasFactory;

    // Specify the correct table name
    protected $table = 'user_bank';

    // Fillable fields
    protected $fillable = [
        'bank_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'a_c_holder_name',
        'phone_number',
        'pancard',
        'aadharcard',
        'user_id',
    ];
}
