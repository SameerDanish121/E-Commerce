<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class order_items extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price_at_purchase',
    ];

    public $timestamps = false; 


    public function orders()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

        public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
