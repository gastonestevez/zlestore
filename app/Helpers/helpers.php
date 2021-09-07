<?php
//https://dev.to/kingsconsult/how-to-create-laravel-8-helpers-function-global-function-d8n
/*
interface Product:
id: string,
name: string,
sku: string,
price: number,
unidades_por_caja: number

*/
function getProduct($id) {
  if(empty(getVariationProduct($id))){
    return getSimpleProduct($id);
  } else {
    return getVariationProduct();
  }
}

  function getVariationProduct($id) {
    $producto = DB::table('wpct_posts AS p')
              ->join('wpct_postmeta AS pm', 'p.id', '=', 'pm.post_id')
              ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
              ->select('p.id', 'pml.sku', 'p.post_title AS name', 'pml.max_price AS price', 'pm.meta_value AS units_in_box')       
              ->where('pm.meta_key',  '=', 'unidades_por_caja')            
              ->where('p.id', '=', $id)
              ->first();

    return $producto;
  }

  function getSimpleProduct($id) {
    $producto = DB::table('wpct_posts AS p')
          // ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')

              ->join('wpct_term_relationships AS r', 'p.id', '=', 'r.object_id')
              ->join('wpct_term_taxonomy AS tt', 'r.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
              ->join('wpct_terms AS t', 't.term_id', '=', 'tt.term_id')
              ->select('*')
              ->where('tt.taxonomy', '=', 'product_type')
              ->where('t.name', '=', 'simple')      
              ->where('p.id', '=', $id)           
              ->get();

    return $producto;
  }

  function getVariationProducts() {
    $productos = DB::table('wpct_posts AS p')
              ->join('wpct_postmeta AS pm', 'p.id', '=', 'pm.post_id')
              ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
              ->select('p.id', 'pml.sku', 'p.post_title AS name', 'pml.max_price AS price', 'pm.meta_value AS units_in_box')       
              ->where('pm.meta_key',  '=', 'unidades_por_caja')              
              ->orderBy('post_title', 'ASC')
              ->get();
        
    return $productos;
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

  function getSimpleProducts() {
    $productosSimples = DB::table('wpct_posts AS p')
                ->join('wpct_term_relationships AS r', 'p.id', '=', 'r.object_id')
                ->join('wpct_term_taxonomy AS tt', 'r.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                ->join('wpct_terms AS t', 't.term_id', '=', 'tt.term_id')
                ->select('p.id')
                ->where('tt.taxonomy', '=', 'product_type')
                ->where('t.name', '=', 'simple')                 
                ->get();

    return $productosSimples;
  }

  function getProducts() {
    // return  [...getVariationProducts(), ...getSimpleProducts()];
    return getVariationProducts();
  }