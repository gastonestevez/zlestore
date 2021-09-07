<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Warehouse;
use App\Models\Stock;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use Carbon\Carbon;
use DB;

class ProductsController extends Controller
{

    public function loadcsv(Request $request)
    {
        $warehouses = Warehouse::all();
        if (count($warehouses) == 0) {
            return redirect('/')->with('noWarehouses', 'No hay depósitos para distribuir los nuevos productos.');
        }
        return view('/products/csv');
    }

    public function storecsv(Request $request)
    {

        $csv = Excel::import(new ProductsImport, $request->file('file'));
        $products =Product::all();
        $warehouses = Warehouse::all();
        $lastProduct = Product::orderBy('id','desc')->first();
        $wc = $this->getWcConfig();
        $lastSyncDateCreated = $wc->get('products/' . $lastProduct->woo_id)->date_created;
        $lastProduct->update(['woo_created' => $lastSyncDateCreated]);
        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                $product->getWarehouses()->attach($product->id, [
                    'warehouse_id' => $warehouse->id,
                    'quantity' => 0,
                ]);
            }
        }

        return back()->with('success', 'Los productos fueron agregados a la base.');
    }

    public function list(Request $request)
    {
        $sku = $request->get('sku');
        $name = $request->get('name');
        $price = $request->get('price');
        $id = $request->get('id');


        $products = getProducts();
        $vac = compact('products', 'request');

        return view('/products/products', $vac);
    }

    public function show(String $id)
    {
        $product = getProduct($id);
        $warehouses = Warehouse::all();

        $vac = compact('product', 'warehouses');

        return view('/products/product', $vac);
    }

    public function updatingUnits(Request $request, int $id)
    {
        // variables de ayuda
        $warehouseId = $request->warehouse_id;
        $product = getProduct($id);
        $quantity = $request->quantity;

        DB::table('stocks')
            ->where('warehouse_id', '=', $warehouseId)
            ->where('product_id', '=', $product->id)
            ->update(['quantity' => $quantity]);

        return back()->with('success', 'Stock actualizado correctamente');
    }

    public function updatingBoxes(Request $request, int $id)
    {

        // variables de ayuda
        $warehouseId = $request->warehouse_id;
        $product = getProduct($id);

        // Unidades actuales
        $stock = Warehouse::getProductStock($warehouseId, $id);
        // cajas actuales
        if ($product->units_in_box > 0) {
            $boxes = $stock / $product->units_in_box;

            // averiguo el resto de unidades en caso de que tenga cajas abiertas
            $sobra = $stock % $product->units_in_box;
            // nueva cantidad en stock
            $quantity = $sobra + ($product->units_in_box * $request->boxes);

            DB::table('stocks')
            ->where('warehouse_id', '=', $warehouseId)
            ->where('product_id', '=', $id)
            ->update(['quantity' => $quantity]);
          
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
