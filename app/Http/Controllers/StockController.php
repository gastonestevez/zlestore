<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Warehouse;
use App\Models\Stocks;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use Carbon\Carbon;
use DB;

class StockController extends Controller
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

    public function allStock(Request $request)
    {
        $orderInProgress = Order::where('status', '=', 'in progress')->where('user_id', '=', auth()->user()->id)->get()->last();
            if($orderInProgress)
            {
                $orderItems = $orderInProgress->orderItems();
            } else {
                $orderItems = null;
            }
        
        $sku = $request->get('sku');
        $name = $request->get('name');
        $price = $request->get('price');
        $id = $request->get('id');
        
        $searchParams = array(
            "p.id" => $id,
            "p.post_title" => $name,
            "pml.max_price" => $price,
            "pml.sku" => $sku
        );
        $products = getProducts($searchParams, true);
        $vac = compact('products', 'request', 'orderInProgress', 'orderItems');

        return view('stock.products', $vac);
    }

    public function show(String $id)
    {
        $product = getProduct($id);
        $warehouses = Warehouse::all();

        $vac = compact('product', 'warehouses');

        return view('stock.product', $vac);
    }

    public function warehouseStock(Request $request, string $warehouseSlug)
    {

        $id = $request->get('id');
        $sku = $request->get('sku');
        $name = $request->get('name');
        $price = $request->get('price');

        $warehouse = Warehouse::where('slug', '=', $warehouseSlug)->first();
        
        if ($warehouse && $warehouse->count() > 0) {

            $products = DB::table('wpct_posts AS p')
                            ->join('wpct_wc_product_meta_lookup AS pml', 'p.id', '=', 'pml.product_id')
                            ->join('stocks AS s', 'pml.product_id', '=', 's.product_id')
                            ->select('s.product_id AS id','p.post_title AS name', 'pml.sku', 'pml.max_price AS price', 's.quantity')
                            ->where('warehouse_id', "=", $warehouse->id)
                            ->where('quantity', '>', 0);

            if(!empty($sku)){
                $products = $products->where('pml.sku', 'LIKE', '%' . $sku . '%');
            }
            if(!empty($name)){
                $name = str_replace(' ', '%', $name);
                $products = $products->where('p.post_title', 'LIKE', '%' . $name . '%');
            }
            if(!empty($id)){
                $products = $products->where('p.id', 'LIKE', '%' . $id . '%');
            }

            if(!empty($price)){
                $products = $products->where('pml.max_price', '=', $price);
            }
            
            $products = $products->paginate(100);
            $vac = compact('warehouse', 'products', 'request');

            return view('stock.warehouseStock', $vac);           
        } else {
            return back();
        }
    } 


    public function updatingUnits(Request $request, int $id)
    {
        // variables de ayuda
        $warehouseId = $request->warehouse_id;
        $product = getProduct($id);
        $quantity = $request->quantity;

        // Use update orCreate en lugar de updateOrInsert por tema de timestamps
        Stocks::updateOrCreate(
                ['warehouse_id' => $warehouseId, 'product_id' => $product->id],
                ['quantity' => $quantity]
            );

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

            Stocks::updateOrCreate(
                ['warehouse_id' => $warehouseId, 'product_id' => $product->id],
                ['quantity' => $quantity]
            );
          
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
