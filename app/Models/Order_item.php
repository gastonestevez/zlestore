<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_item extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'product_name', 'product_sku', 'price', 'quantity', 'order_id', 'subprice'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
