<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Stocks;

class Warehouse extends Model
{
    use HasFactory;
    protected $table = 'warehouse';
    protected $fillable = ['name', 'address', 'visibility'];

    public function getProducts()
    {
        return $this->belongsToMany(Product::class, 'stocks')->withPivot('quantity')->as('stock');
    }

    public function getProductStock(Int $warehouseId, Int $productId)
    {
      return stocks::where('product_id', '=', $productId)->where('warehouse_id', '=', $warehouseId)->first()->quantity;
    }
    
}
