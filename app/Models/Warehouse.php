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

    public static function getProducts($warehouse_id)
    {
      $products = Stocks::select('product_id')->where('warehouse_id', '=', $warehouse_id)->get();
      return $products;
    }

    public static function getProductStock(Int $warehouseId, Int $productId)
    {
      $stock = Stocks::where('product_id', '=', $productId)->where('warehouse_id', '=', $warehouseId)->pluck('quantity')->first();
      isset($stock)?$stock:$stock=0;
      return $stock;
    }

    public static function getShops()
    {
      $shops = Warehouse::where('type', '=', 'shop')->get();
      return $shops;
    }

    public static function getStorages()
    {
      $storages = Warehouse::where('type', '=', 'storage')->get();
      return $storages;
    }
    
}
