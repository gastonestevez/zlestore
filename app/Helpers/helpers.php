<?php
use App\Models\Warehouse;
use App\Models\Stocks;

//https://dev.to/kingsconsult/how-to-create-laravel-8-helpers-function-global-function-d8n
/*
interface Product:
id: string,
name: string,
sku: string,
price: number,
unidades_por_caja: number

*/

  function getProducts($searchParams = [], $hasPagination = false) {
    $productos = DB::table('wpct_posts AS p')
              ->join('wpct_postmeta AS pm', 'p.id', '=', 'pm.post_id')
              ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
              ->select('p.id', 'pml.sku', 'p.post_title AS name', 'pml.max_price AS price', 'pm.meta_value AS units_in_box')       
              ->where('pm.meta_key',  '=', 'unidades_por_caja')
              // ->orderBy('id', 'DESC');
              ->orderBy('post_title', 'ASC');

    foreach ($searchParams as $key => $value) {
      if(!empty($value)){
        $value = str_replace(' ', '%', $value);
        $productos->where($key, 'LIKE', '%' . $value . '%');
      }
    }
    return $hasPagination ? $productos->paginate(100) : $productos->get();
  }

  function getProduct($id) {
    $producto = DB::table('wpct_posts AS p')
              ->join('wpct_postmeta AS pm', 'p.id', '=', 'pm.post_id')
              ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
              ->select('p.id', 'pml.sku', 'p.post_title AS name', 'pml.max_price AS price', 'pm.meta_value AS units_in_box')       
              ->where('pm.meta_key',  '=', 'unidades_por_caja')              
              ->orderBy('post_title', 'ASC')  
              ->where('p.id', '=', $id)           
              ->first();

    return $producto;
  }

  function getParentProductsId() {
    $productosPadre = DB::table('wpct_posts AS p')
                ->join('wpct_term_relationships AS r', 'p.id', '=', 'r.object_id')
                ->join('wpct_term_taxonomy AS tt', 'r.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                ->join('wpct_terms AS t', 't.term_id', '=', 'tt.term_id')
                ->select('p.id')
                ->where('tt.taxonomy', '=', 'product_type')
                ->where('t.name', '=', 'variable')                 
                ->pluck('id');

    return $productosPadre;
  }

  // Con esta query te trae las categorias y tags de un producto
  // Pero tiene q ser producto padre o simple.  Si es variación hay q encontrar la forma de buscar el id del padre
  function getProductTaxonomies($productId) {

    // Detecto si el producto que me pasan es padre, simple o variacion
    $productParentId = DB::table('wpct_posts AS p')
                ->where('p.id', '=', $productId)
                ->select('p.post_parent')
                ->pluck('post_parent')
                ->first();
    
    // si me da distinto a 0 quiere decir que es una variación. Utilizo el id del padre
    if ($productParentId != 0) {
      $productId = $productParentId;
    }

    $taxonomies = DB::table('wpct_term_relationships AS tr')
                ->join('wpct_term_taxonomy AS tt', 'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
                ->join('wpct_terms AS t', 't.term_id', '=', 'tr.term_taxonomy_id')
                ->select('t.name')
                ->where('tr.object_id', '=', $productId) 
                ->where('t.name', '!=', 'Variable')             
                ->where('t.name', '!=', 'Simple')             
                ->where('t.name', '!=', 'Sin categorizar')             
                ->pluck('name')
                ->toArray();
              
    return $taxonomies;
  }

  function getAllStock($productId) {
    // Trae la totalidad de stock que hay de un producto en todos los depósitos.
    
    $stocks = Stocks::where('product_id', '=', $productId)->pluck('quantity');
    $total = 0;
    foreach ($stocks as $stock) {
      $total = $stock + $total;
    }
    return $total;
  }