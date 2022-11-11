<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Postmeta;
use App\Models\Product;
use DB;

class WooCommerceController extends Controller
{
  public function wc()
  {

    $woocommerce = new Client(
      'https://zlestore.com',
      env('WC_KEY_CK'),
      env('WC_KEY_CS'),
      [
        'wp_api' => true,
        'version' => 'wc/v3',
      ]
    );

    dd(Product::getProducts());
    // dd(Product::getProduct('4959'));

    //4956 Variacion
    //4955 PADRE
    //4852 SIMPLE

    // Hay que resolver ciertas cosas. Primero hay que guardar unicamente 
    // productos de type simple o type variation. No hay que guardar type variable 
    // Segundo: hay que traer el id, nombre, precio, sku y unidad por caja.

    // Dos maneras de traer un producto (devuelven info distina)
    $producto = Product::find(4956);
    // dd($producto);
    $producto = $woocommerce->get('products/4956');
    // dd($producto);

    // Traer unidades por caja a traves de peticion al cliente de Woo
    // dd($productoSimple->meta_data[0]->value);

    // Traer unidades por caja desde base de datos
    // Recorro la tabla de los postmeta y busco que la columna post_id sea = al id del producto
    $product_meta = Postmeta::where('post_id', '=', '4959')->get();
    // recorro todas las filas de ese producto y busco que la meta_key sea unidades_por_caja y obtengo su value
    foreach($product_meta as $meta) {
      if($meta->meta_key === "unidades_por_caja") {
        $unidades_por_caja = $meta->meta_value;
      } 
    }
    // dd($unidades_por_caja);






     $productosLocos = DB::table('ewg62_posts AS p')
              ->join('ewg62_postmeta AS pm', 'p.id', '=', 'pm.post_id')
              ->join('ewg62_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
              ->select('p.id', 'pml.sku', 'p.post_title', 'pml.max_price', 'pm.meta_value AS unidades por caja')       
              ->where('pm.meta_key',  '=', 'unidades_por_caja')            
              ->where('p.id', '=', '4957')
              ->get();
              dd($productosLocos);



    try {

    } catch (\Exception $e)
    {
      return redirect('/');
    }



  }   

}
