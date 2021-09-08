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
        return $this->belongsToMany(Product::class, 'stocks', 'warehouse_id', 'product_id')->withPivot('quantity')->as('stock');
    }

    public static function getProductStock(Int $warehouseId, Int $productId)
    {
      $stock = Stocks::where('product_id', '=', $productId)->where('warehouse_id', '=', $warehouseId)->pluck('quantity')->first();
      return $stock;
    }
    
}
