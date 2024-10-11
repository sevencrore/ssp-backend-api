<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', // Foreign key referencing the Product model
        'title',
        'description',
        'image_url',
        'price',
        'discount',
    ];
}
