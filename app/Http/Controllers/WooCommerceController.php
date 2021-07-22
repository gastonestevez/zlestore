<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;

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
