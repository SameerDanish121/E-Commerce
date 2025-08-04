<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
{

    protected $table = 'orders';
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'order_datetime',
        'status',
        'total_payment',
        'delivered_on_time',
        'delivery_address',
        'expected_delivery_date',
        'actual_delivery_date',
        'payment_type',
        'is_paid'
    ];
    public function customer()
    {
        return $this->belongsTo(customers::class, 'customer_id');
    }
    public function orderItems()
    {
        return $this->hasMany(order_items::class, 'order_id');
    }
    
}

