<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function concept()
    {
        return $this->belongsTo(Concept::class, 'concept_id');
    }

    public function orderItems()
    {
        return $this->hasMany(Order_item::class, 'order_id');
    }
}