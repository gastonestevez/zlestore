<?php
//https://dev.to/kingsconsult/how-to-create-laravel-8-helpers-function-global-function-d8n

function getProduct($id) {
    $producto = DB::table('wpct_posts AS p')
              ->join('wpct_postmeta AS pm', 'p.id', '=', 'pm.post_id')
              ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
              ->select('p.id', 'pml.sku', 'p.post_title AS name', 'pml.max_price AS price', 'pm.meta_value AS units_in_box')       
              ->where('pm.meta_key',  '=', 'unidades_por_caja')            
              ->where('p.id', '=', $id)
              ->first();

    return $producto;
  }

  function getProducts() {
    $productos = DB::table('wpct_posts AS p')
              ->join('wpct_postmeta AS pm', 'p.id', '=', 'pm.post_id')
              ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
              ->select('p.id', 'pml.sku', 'p.post_title AS name', 'pml.max_price AS price', 'pm.meta_value AS units_in_box')       
              ->where('pm.meta_key',  '=', 'unidades_por_caja')
              ->orderBy('post_title', 'ASC')
              ->get();
        
    return $productos;
  }

  function parentProducts() {
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