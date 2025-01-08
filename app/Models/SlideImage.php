<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlideImage extends Model
{
    use HasFactory;
    protected $table = 'slideimages'; // Explicitly define the table name
    protected $fillable = [
        'title',
        'image_path',
    ];
}
