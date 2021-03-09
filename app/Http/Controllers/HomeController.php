<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Product;
use App\Models\Warehouse;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wc = $this->getWcConfig();
        $orders = count($wc->get('orders'. '?&status=pending'));
        $warehouses = Warehouse::all()->count();
        $products = Product::all()->count();
        return view('index', [
            'orders' => $orders,
            'warehouses' => $warehouses,
            'products' => $products,
        ]);
    }


    private function getWcConfig(){
        return new Client(
            'https://zlestore.com',
            env('WC_KEY_CK'),
            env('WC_KEY_CS'),
            [
              'wp_api' => true,
              'version' => 'wc/v3',
            ]
          );
    }
}
