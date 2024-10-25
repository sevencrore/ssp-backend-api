<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        
        'title',
        'description',
        'image_url',
        'price',
        'priority',
        'category_id'
    ];
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }
}
