<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{

    protected $table = 'product'; 
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'category','price', 'stock','is_active','image'];

    public function orderItems()
    {
        return $this->hasMany(order_items::class, 'product_id');
    }
}
