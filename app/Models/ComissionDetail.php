<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComissionDetail extends Model
{
    use HasFactory;

    protected $table = 'comission_details'; // Explicit table name

    protected $fillable = [
        'comission_id',
        'level',
        'commission',
    ];

    /**
     * Define the relationship with the Comission model.
     */
    public function comission()
    {
        return $this->belongsTo(Comission::class, 'comission_id', 'id');
    }
}
