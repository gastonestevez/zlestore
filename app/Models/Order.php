<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Auth;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['status', 'user_id', 'concept_id'];

    public function concept()
    {
        return $this->belongsTo(Concept::class, 'concept_id');
    }

    public function orderItems()
    {
        return $this->hasMany(Order_item::class, 'order_id')->orderBy('product_name')->get();
    }

    public function orderItemsIds()
    {
        return $this->hasMany(Order_item::class, 'order_id')->pluck('product_id');
    }

    public function orderInProgress()
    {
        $orderInProgress = Order::where('status', '=', 'in progress')->where('user_id', '=', auth()->user()->id)->get()->last();
    }

    public function orderAuthor()
    {
        return $this->belongsTo(User::class, 'user_id')->first();
    }
}
