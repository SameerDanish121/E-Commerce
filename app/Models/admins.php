<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class admins extends Model
{
    use HasFactory;

    protected $table = 'admins';

    protected $fillable = [
        'user_id',
        'name',
        'designation',
        'contact_no',
        'address',
        'profile_pic',
    ];

    public $timestamps = false; 
    public function user()
    {
        return $this->belongsTo(users::class);
    }
}
