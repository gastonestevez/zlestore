<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'price',
        'woo_id'
    ];

    public function getWarehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'stocks')->withPivot('quantity')->as('stock');
    }

    // public function getStockByWarehouse(int $warehousId, int $productId)
    // {
    //     return stocks::where('product_id', '=', $productId)->where('warehouse_id', '=', $warehouseId);
    // }

    public function scopeSku($query, $sku)
    {
      if($sku)
        return $query->where('sku', 'LIKE', "%$sku%");
    }

    public function scopeWooId($query, $woo_id)
    {
      if($woo_id)
        return $query->where('woo_id', 'LIKE', "%$woo_id%");
    }

    public function scopePrice($query, $price)
    {
      if($price)
        return $query->where('price', 'LIKE', "%$price%");
    }

    public function scopeName($query, $name)
    {
      if($name)
        return $query->where('name', 'LIKE', "%$name%");
    }
}
