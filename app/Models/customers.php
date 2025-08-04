<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class customers extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone_no',
        'dob',
        'gender',
        'profile_pic',
    ];

    public $timestamps = false;

   
    public function users()
    {
        return $this->belongsTo(users::class, 'user_id');
    }

 
    public function orders()
    {
        return $this->hasMany(Orders::class, 'customer_id');
    }
}
