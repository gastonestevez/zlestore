<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Warehouse;
use App\Models\Stocks;
use App\Models\Order;
use App\Models\Movement;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use Carbon\Carbon;
use DB;

class StockController extends Controller
{

    public function allStock(Request $request)
    {
        
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

        if(!$id && !$name && !$price && !$sku){
            $products = [];
        } else {
            $products = getProducts($searchParams, true);
        }
        
        $storages = Warehouse::all();
        $vac = compact('products', 'request', 'storages');

        return view('stock.products', $vac);
    }

    public function show(String $id)
    {
        $product = getProduct($id);
        $warehouses = Warehouse::orderBy('type', 'desc')->get();
        $movements = Movement::where('product_id', $id)->latest()->take(50)->get();

        $vac = compact('product', 'warehouses', 'movements');

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


            $products = Warehouse::getProductsByWarehouse($warehouse->id);
            

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
        $noRedirect = $request->noRedirect;
        $batch = $request->batch;

        if($batch) {
            $stockList = $request->stockList;
            foreach ($stockList as $item) {
                Stocks::updateOrCreate(
                    ['warehouse_id' => $item['warehouseId'], 'product_id' => $item['productId']],
                    ['quantity' => $item['stock']]
                );
            }
        } else {
            Stocks::updateOrCreate(
                    ['warehouse_id' => $warehouseId, 'product_id' => $product->id],
                    ['quantity' => $quantity]
            );
        }

        // Use update orCreate en lugar de updateOrInsert por tema de timestamps
        if($noRedirect){
            return response()->json(['success' => true, 'message' => 'Stock actualizado correctamente']);
        } 
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
