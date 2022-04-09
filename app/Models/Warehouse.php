<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Stocks;
use DB;

class Warehouse extends Model
{
    use HasFactory;
    protected $table = 'warehouse';
    protected $fillable = ['name', 'address', 'visibility'];

    public static function getProductsByWarehouse($warehouseId)
    {
      $products = DB::table('wpct_posts AS p')
                            ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
                            ->join('stocks AS s', 'pml.product_id', '=', 's.product_id')
                            ->join('wpct_postmeta AS pm', 'p.id', '=', 'pm.post_id')
                            ->select('s.product_id AS id','p.post_title AS name', 'pml.sku', 'pml.max_price AS price', 's.quantity', 'pm.meta_value AS units_in_box')
                            ->where('warehouse_id', "=", $warehouseId)
                            ->where('quantity', '>', 0)
                            ->where('pm.meta_key', '=', 'unidades_por_caja');    
                                           
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
