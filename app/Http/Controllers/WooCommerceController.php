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

    try {

      dd($woocommerce->get('orders')[0]);

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
