<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class users extends Authenticatable
{
    use HasFactory, HasRoles;
    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
    ];
    protected $hidden = [
        'password',
    ];
    public $timestamps = false;
    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }


    public function admin()
    {
        return $this->hasOne(admins::class);
    }
    public function customer()
    {
        return $this->hasOne(customers::class);
    }
}
