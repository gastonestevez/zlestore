<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Wpct_postmeta;
use App\Models\Product;

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

    // Hay que resolver ciertas cosas. Primero hay que guardar unicamente 
    // productos de type simple o type variation. No hay que guardar type variable 
    // Segundo: hay que traer el id, nombre, precio, sku y unidad por caja.

    // Dos maneras de traer un producto (devuelven info distina)
    $producto = Product::find('4957');
    // dd($producto);
    $producto = $woocommerce->get('products/4957');
    dd($producto);
    

    // Traer unidades por caja a traves de peticion al cliente de Woo
    dd($productoSimple->meta_data[0]->value);

    // Traer unidades por caja desde base de datos
    // Recorro la tabla de los postmeta y busco que la columna post_id sea = al id del producto
    $product_meta = Postmeta::where('post_id', '=', '4959')->get();
    // recorro todas las filas de ese producto y busco que la meta_key sea unidades_por_caja y obtengo su value
    foreach($product_meta as $meta) {
      if($meta->meta_key === "unidades_por_caja") {
        $unidades_por_caja = $meta->meta_value;
      } 
    }
    dd($unidades_por_caja);

  

    $productoSimple = $woocommerce->get('products/2314'); // sin variaciones $product->type = simple
    $productoVariable = $woocommerce->get('products/2320'); // con variaciones $product->type = variable
    $productoVariacion = $woocommerce->get('products/2321'); // variación del de arriba
    // $orden = $woocommerce->get('orders/4157');

    // Si el producto es simple pregunto por este valor
    // Con esto obtengo el numero de unidades por caja del producto variación
    // dd($productoVariacion->meta_data[0]->value);

    // Si el producto es variation pregunto por este valor
    // Con esto obtengo el numero de unidades por caja del producto simple
    // dd($productoSimple->cantidad_por_caja);

    dd('producto simple', $productoSimple, 'producto variable', $productoVariable, 'producto variacion', $productoVariacion);

    // pregunto si el producto tiene variaciones
    $variaciones = count($producto->variations);
    
    // si tiene variaciones recorro cada producto y lo guardo
    if($variaciones > 0)
    {
     foreach ($producto->variations as $product) {
       $vproduct = $woocommerce->get('products/'.$product);
       dd($vproduct);
      $vProduct = new Product;
      $vProduct->price = $product->price ?: 0;
      $vProduct->sku = $product->sku;
      $vProduct->woo_id = $product->id;
      $vProduct->visibility = 1;
      $vProduct->name = $product->name;
      $vProduct->woo_created = $product->date_created;
      $vProduct->save();
      }
    }






    try {

      // dd($woocommerce->get('orders')[0]);
      

      // Seleccionamos, recorremos cada producto y vemos cuanta cantidad piden del producto
      $cantidad = $woocommerce->get('orders')[0]->line_items[0]->quantity;

      // Si el producto no tiene el campo unidades por caja vacio entonces multiplico el total de cajas por las unidades que lleva dentro
      if (!empty($woocommerce->get('orders')[0]->line_items[0]->unidades_por_caja)) {
        $unidades_totales = $cantidad * ($woocommerce->get('orders')[0]->line_items[0]->unidades_por_caja);
        dd($unidades_totales);
      } else {
        dd($cantidad);
      }

    } catch (\Exception $e)
    {
      return redirect('/');
    }



  }

}
