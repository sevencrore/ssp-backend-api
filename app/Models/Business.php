<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Business extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Define the name of the "deleted at" column
    protected $dates = ['deleted_at'];
  
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'business_name', 'address', 'city', 'postal_code', 'phone_number','website', 'description', 'keywords', 'is_approved'
    ];
}