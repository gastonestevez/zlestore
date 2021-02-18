<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\stocks;

class ProductsController extends Controller
{
    public function newProducts()
    {
        $wc = $this->getWcConfig();
        $index = 1;
        $products = [];
        $wcProducts = $wc->get('products?page=' . $index);

        while(count($wcProducts) > 0) {
            $products = array_merge($products, $wcProducts);
            $index += 1;
            $url = 'products?page=' . $index;
            $wcProducts = $wc->get($url);
        }

        $newProducts = false;
        $productsToAdd = [];
        $warehousesCount = Warehouse::count() != 0;
        $warehouses = Warehouse::where('visibility','1')->get();
        foreach ($products as $item) {
            $search = Product::where('woo_id', $item->id)->first();
            if(!$search && $item->id && $item->status != 'draft' && $item->price){
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

    public function store(Request $request)
    {
        $data = $request->except('_token');
        $wc = $this->getWcConfig();
        $wcProduct = $wc->get('products/'.$data['itemId']);
        $sProduct = new Product;
        $sProduct->price = $wcProduct->price;
        $sProduct->sku = $wcProduct->sku;
        $sProduct->woo_id = $wcProduct->id;
        $sProduct->visibility = 1;
        $sProduct->name = $wcProduct->name;
        $sProduct->save();

        foreach ($data['warehouse'] as $wh) {
            if($wh['stock'] > 0){
                $result = $sProduct->getWarehouses()->attach($sProduct->id, [
                    'warehouse_id' => $wh['id'],
                    'quantity' => $wh['stock'],
                ]);
            }
        }
        
        return back();
    }

    public function prepareOrder(int $id)
    {
        $wc = $this->getWcConfig();
        $order = $wc->get('orders/'.$id);
        $warehouses = Warehouse::where('visibility','1')->get();
        return view('prepareOrder', [
            'order' => $order,
            'warehouses' => $warehouses,
        ]);
    }

    private function getWcConfig()
    {
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
