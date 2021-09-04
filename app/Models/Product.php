<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;
use DB;

class Product extends Model
{
    use HasFactory;

    public $table = 'Wpct_wc_product_meta_lookup';
    protected $primaryKey = 'product_id';

    public static function getProduct($id) {
      return DB::table('wpct_posts AS p')
                ->join('wpct_postmeta AS pm', 'p.id', '=', 'pm.post_id')
                ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
                ->select('p.id', 'pml.sku', 'p.post_title', 'pml.max_price', 'pm.meta_value AS unidades por caja')       
                ->where('pm.meta_key',  '=', 'unidades_por_caja')            
                ->where('p.id', '=', $id)
                ->first();
    }

    public static function getProducts() {
      $productos = DB::table('wpct_posts AS p')
                  ->join('wpct_term_relationships AS r', 'p.id', '=', 'r.object_id')
                  ->join('wpct_term_taxonomy AS tt', 'r.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                  ->join('wpct_terms AS t', 't.term_id', '=', 'tt.term_id')
                  ->select('p.id')
                  ->where('tt.taxonomy', '=', 'product_type')
                  ->where('t.name', '=', 'variable')                 
                  ->get();

        $array = $productos->toArray();
        foreach ($array as $value) {
          $productosPadre[] = $value->id;
        }
                       

      return DB::table('wpct_posts AS p')
                ->join('wpct_postmeta AS pm', 'p.id', '=', 'pm.post_id')
                ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
                ->select('p.id', 'pml.sku', 'p.post_title', 'pml.max_price', 'pm.meta_value AS unidades por caja')       
                ->where('pm.meta_key',  '=', 'unidades_por_caja')                          
                ->whereNotIn('p.id', $productosPadre)
                ->orderBy('post_title', 'ASC')
                ->get();               
    }

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

    public function scopeWoo_id($query, $woo_id)
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
