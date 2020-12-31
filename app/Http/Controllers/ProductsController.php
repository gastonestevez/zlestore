<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Product;
use App\Models\Warehouse;

class ProductsController extends Controller
{
    public function newProducts()
    {
        $wc = $this->getWcConfig();
        $wcProducts = $wc->get('products');
        $newProducts = false;
        $productsToAdd = [];
        $warehousesCount = Warehouse::count() != 0;
        $warehouses = Warehouse::where('visibility','1')->get();
        foreach ($wcProducts as $item) {
            $search = Product::where('sku', $item->sku)->first();
            if(!$search && $item->sku){
                $productsToAdd[] = $item;
            }
        }

        return view('newProducts', [
            'products' => $productsToAdd,
            'newProducts' => $newProducts,
            'warehousesCount' => $warehousesCount,
            'warehouses' => $warehouses,
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
