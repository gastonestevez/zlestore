<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Stock;

class ProductsController extends Controller
{

    public function syncWoocommerce(Request $request)
    {

        try {
            
            $warehouses = Warehouse::all();
            if (count($warehouses) == 0) {
                return back()->with('noWarehouses', 'No hay depósitos para distribuir los nuevos productos.');
            }

            $pr = Product::where('woo_id','not like','%FAKE%')->orderBy('woo_id', 'DESC')->get()->first();
            $wc = $this->getWcConfig();
            $index = 1;
            $products = [];
            $queryString = 'products' . '/?after=' . ($pr ? $pr->woo_created : '2020-01-01T12:00:00' . '&order=asc&orderby=id');
            $wcProducts = $wc->get($queryString . '&page='. $index);
            
            while(count($wcProducts) > 0) {
                foreach ($wcProducts as $wcProduct) {
                    $sProduct = new Product;
                    $sProduct->price = $wcProduct->price ?: 0;
                    $sProduct->sku = $wcProduct->sku;
                    $sProduct->woo_id = $wcProduct->id;
                    $sProduct->visibility = 1;
                    $sProduct->name = $wcProduct->name;
                    $sProduct->woo_created = $wcProduct->date_created;
                    
                    if($wcProduct->type == 'simple') {
                        $sProduct->save();
    
                        foreach ($warehouses as $warehouse) {
                            $sProduct->getWarehouses()->attach($sProduct->id, [
                                'warehouse_id' => $warehouse->id,
                                'quantity' => 0,
                            ]);
                        }
                    }

                    if($wcProduct->type == 'variable') {
                        foreach ($wcProduct->variations as $variationId) {
                            $wcVariation = $wc->get('products/'.$variationId);
                            $vProduct = new Product;
                            $vProduct->price = $wcVariation->price ?: 0;
                            $vProduct->sku = $wcVariation->sku;
                            $vProduct->woo_id = $wcVariation->id;
                            $vProduct->visibility = 1;
                            $vProduct->name = $wcVariation->name ?: 'NO_NAME';
                            $vProduct->woo_created = $wcVariation->date_created ?: '';
                            $vProduct->save();
                            foreach ($warehouses as $warehouse) {
                                $vProduct->getWarehouses()->attach($vProduct->id, [
                                    'warehouse_id' => $warehouse->id,
                                    'quantity' => 0,
                                ]);
                            }
                        }
                    }
                }
                $index += 1;
                $url = $queryString . '&page=' . $index;
                $wcProducts = $wc->get($url);
            }

            return back()->with('success', 'Sus depósitos han sido sincronizados a Woocommerce.');

        } catch (\Throwable $th) {
            dd($th);
            return back()->with('error', 'Aun faltan productos por sincronizar.');
        }
        
    }

    public function list(Request $request)
    {
      $sku = $request->get('sku');
      $name = $request->get('name');
      $price = $request->get('price');
      $woo_id = $request->get('woo_id');


      $products = Product::sku($sku)->name($name)->price($price)->woo_id($woo_id)->paginate(25);
      $vac = compact('products', 'request');

      return view('/products/products', $vac);
    }

    public function show(String $woo_id)
    {
      $product = Product::where('woo_id', '=', $woo_id)->first();
      $warehouses = Warehouse::all();

      $vac = compact('product', 'warehouses');

      return view('/products/product', $vac);
    }


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

        return view('products/newProducts', [
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
        return view('orders/prepareOrder', [
            'order' => $order,
            'warehouses' => $warehouses,
            'timeout' => 900
        ]);
    }

    public function updatingUnits(Request $request, int $id)
    {
       // variables de ayuda
        $warehouseId = $request->warehouse_id;
        $product = Product::find($id);
        $quantity = $request->quantity;

        // aca indicamos que producto va updatear su stock, la cantidad nueva de stock y en que deposito se esta realizando
        $product->getWarehouses()->updateExistingPivot($warehouseId, ['quantity' => $quantity]); // https://laravel.com/docs/8.x/eloquent-relationships Updating A Record On A Pivot Table

        return back()->with('success', 'Stock actualizado correctamente');
    }


    public function updatingBoxes(Request $request, int $id)
    {
       
       // variables de ayuda
        $warehouseId = $request->warehouse_id;
        $product = Product::find($id);

        // Unidades actuales
        $stock = Warehouse::getProductStock($warehouseId, $id);    
        // cajas actuales
        if ($product->units_in_box > 0) {        
        $boxes = $stock / $product->units_in_box;

        // averiguo el resto de unidades en caso de que tenga cajas abiertas
        $sobra = $stock % $product->units_in_box;
        // nueva cantidad en stock
        $quantity = $sobra + ($product->units_in_box * $request->boxes);
        
        
        // aca indicamos que producto va updatear su stock, la cantidad nueva de stock y en que deposito se esta realizando
        $product->getWarehouses()->updateExistingPivot($warehouseId, ['quantity' => $quantity]); // https://laravel.com/docs/8.x/eloquent-relationships Updating A Record On A Pivot Table
        
        return back()->with('success', 'Stock actualizado correctamente');
        
        } else {
            $boxes = 0;
        return back()->with('success', 'Stock actualizado correctamente');
        }

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
              'timeout' => 900
            ]
          );
    }
}
