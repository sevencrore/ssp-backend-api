<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'category_id', 'title', 'description', 'image_url', 'price', 'discount', 'unit_id', 'unit_quantity'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
