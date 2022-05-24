<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Warehouse;
use App\Models\Movement;
use Carbon\Carbon;
use App\Models\Stocks;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\Concept;
use Auth;
use PDF;
use DB;

class OrderController extends Controller
{
     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function wcOrders()
    {
        $wc = $this->getWcConfig();
        $wcOrders = $wc->get('orders' . '?&order=asc&orderby=date&status=pending,processing');
        foreach ($wcOrders as $order) {
            $order->customerName = $this->getCustomerFullname($wc, $order);
            $order->date_created = (new Carbon($order->date_created))->format('Y-m-d H:i:s');          
        }
        return view('orders/wcOrders', [
            'orders' => $wcOrders,
        ]);
    }

    private function getCustomerFullname(Client $wc, $order)
    {
        $customer = $wc->get('customers/'.$order->customer_id);
        return $customer->first_name . ' ' . $customer->last_name;
    }


    public function prepare($id)
    {
        $wc = $this->getWcConfig();
        $wcOrder = $wc->get('orders' . '/' . $id);
        $warehouses = Warehouse::all();
        $wcOrder->date_created = (new Carbon($wcOrder->date_created))->format('Y-m-d H:i:s');

        if(count($warehouses) == 0) {
            return redirect()->route('editWarehouses')->with('error', 'No hay depósitos para distribuir la orden.');
        }


        

        // foreach ($wcOrder->line_items as $item) {
        //     // $localProduct = Product::where('woo_id','=',$item->variation_id ?: $item->product_id)->first();
        //     // if(!$localProduct) {
        //     //     return redirect()->route('stockList')->with('error', 'Los productos no están sincronizados.');
        //     // } else {
        //         if(empty($item->variation_id)){
                    // dd($item);
        //         }
        //     // $item->localId = $localProduct->id;
        //     // $item->units_in_box = $localProduct->units_in_box;
        //     // }

       // }

        return view('orders/prepareOrder', [
            'order' => $wcOrder,
            'warehouses' => $warehouses,
        ]);
    }

    public function storeWcOrder($id, Request $r)
    {
        $rProducts = $r->product;
        $transition = $r->transition;

        foreach ($rProducts as $idProduct => $stocks) {
            $productDB = getProduct($idProduct);
            foreach ($stocks as $idWarehouse => $stock) {
                $warehouseDB = Warehouse::find($idWarehouse);
                $newStock = $warehouseDB->getProductStock($idWarehouse, $idProduct) - $stock;
                // $productDB
                //     ->getWarehouses()
                //     ->updateExistingPivot(
                //         $idWarehouse, 
                //         ['quantity' => $newStock]
                //     );
                Stocks::updateOrCreate(
                    ['warehouse_id' => $idWarehouse, 'product_id' => $productDB->id],
                    ['quantity' => $newStock]
                );
            }
        }
        
        $wc = $this->getWcConfig();
        // $wcOrders = $wc->get('orders' . '?&order=desc&orderby=date&status=pending');

        // Liberar cuando esté en producción.
        if($transition)
        {
            $data = [ 'status' => $transition ];
            $wc->put('orders/' . $id, $data);
        }

        return redirect()->route('home')->with('success', 'Orden #' . $id . ' actualizada correctamente.');

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

    // Aqui se comienza a crear una orden
    public function createOrder(Request $request)
    {
        $orderInProgress = Order::where('status', '=', 'in progress')->where('user_id', '=', auth()->user()->id)->get()->last();
            if($orderInProgress)
            {
                $orderItems = $orderInProgress->orderItems();
            } else {
                $orderItems = null;
            }

        $shops = Warehouse::getShops();
        $storages = Warehouse::getStorages();
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
        
        $vac = compact('products', 'request', 'orderInProgress', 'orderItems', 'shops', 'storages');
        return view('orders.createOrder', $vac);
    }

    // Muestra la order en formato in progress antes de generar los descuentos y el pdf
    public static function orderPreview(int $id) {
        $order = Order::where('id', '=', $id)->where('status', '=', 'in progress')->first();
        $concepts = Concept::all();
        $vac = compact('order', 'id', 'concepts');
        if ($order && $order->count() > 0) {
            return view('/orders/orderPreview', $vac);          
        } else {
            return back();
        }

    }

    // Agrega un producto a una orden
    public function addProductToOrder(Request $request)  
    {

        if($request->storageItems) {
            $localStorage = json_decode($request->storageItems);
            

            // Busco si ya hay una orden en progreso
            // $orderInProgress = Order::where('status', '=', 'in progress')->where('user_id', '=', auth()->user()->id)->get()->last();
            Order::updateOrCreate(
                ['status' => 'in progress', 'user_id' => auth()->user()->id],
                ['concept_id' => null]
            );

            // Llamo a la ultima orden 
            $lastOrder = Order::get()->last();
            $lastOrderId = $lastOrder->id;

            // Si el order item a agregar ya existe en la correspondiente orden sumo las cantidades (ALTERNATIVA 2)
            // $repeatedItem = Order_item::where('order_id', '=', $lastOrderId)->where('product_id', '=', $request->productId)->get()->first();
            // if ($repeatedItem) {
            //     $repeatedItem->quantity += $request->quantity;
            //     if ($repeatedItem->quantity > getAllStock($request->productId)) {
            //         return back()->with('error', 'Stock limitado');
            //     }
            //     $repeatedItem->save();
            //     return back()->with('success', 'Cantidad actualizada');
            // }

            // Instancio un nuevo order item y lo asigno a la orden
            // https://laravel.com/docs/8.x/eloquent#inserting-and-updating-models (ALTERNATIVA 3 LA MEJOR!!)
            foreach ($localStorage as $item) {
                $product = getProduct($item->id);

                Order_item::updateOrCreate(
                    ['product_id' => $product->id, 
                    'order_id' => $lastOrderId,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'order_id' => $lastOrderId,
                    'subprice' => $product->price,
                    'warehouse_id' => $item->warehouseId == 0 ? null : $item->warehouseId,
                    'price' => $product->price],
                    ['quantity' => $item->quantity]
                );

            }

            // Calculo el valor total de la orden
            $total = 0;
            foreach ($lastOrder->orderItems() as $item) {
                $total += ($item->quantity * $item->price);
            }

            $lastOrder->total = $total;
            $lastOrder->subtotal = $total;
            $lastOrder->save();

            // return back()->with('success', 'Producto agregado a la orden');
            return self::orderPreview($lastOrderId);
        }

        // Si no hay orden en progreso creo una nueva orden
        // if (!$orderInProgress) {
        //     $newOrder = New Order();
        //     $newOrder->user_id = auth()->user()->id;
        //     $newOrder->concept_id = 1;
        //     $newOrder->save();
        // }



        // Si el order item a agregar ya existe en la correspondiente orden sumo las cantidades (ALTERNATIVA 1)
        // $items = $lastOrder->orderItems();
        // foreach ($items as $item) {
        //    if ($item->product_id == $request->productId) {
        //        $order_item = Order_item::find($item->id);
        //        $order_item->quantity += $request->quantity;
        //        if ($order_item->quantity > getAllStock($request->productId)) {
        //         return back()->with('error', 'Stock limitado');
        //        }
        //        $order_item->save();
        //        return back()->with('success', 'Cantidad actualizada');
        //    }
        // }

    
    }

    // Remueve un producto de la orden
    function removeProduct(int $id) {
        $product = Order_item::find($id);
        $order = Order::find($product->order_id);
        $product->delete();

        // Si la orden se queda sin productos la elimino
        if (count($order->orderItems()) == 0) {
            $order->delete();
            return redirect()->route('createOrder')->with('success', 'Orden eliminada');
        }

        // Calculo el valor total de la orden
        $total = 0;
        foreach ($order->orderItems() as $item) {
            $total += ($item->quantity * $item->price);
        }

        $order->total = $total;
        $order->save();

        return back()->with('success', 'Producto removido');
    }



    public function createAndSavePdf(int $id, Request $request, Order $order) { 
        $pdf = PDF::loadView('orders.orderInvoice', ['order' => $order, 'request' => $request]);
        // $path = public_path('storage');
        $path = storage_path('app/public');
        $fileName = $order->id . '_' . Carbon::now()->format('dmY') . ".pdf";
        $pdf->save($path . '/' . $fileName);
        return $fileName;
    }

    // Genera un pdf con la factura de la orden y pasa el estado a pending
    function orderToPending(int $id, Request $request) {
        // dd($id, $request->all());
        $products = $request->products;
        $discounts = $request->discount;

        $order = Order::find($id);
        for ($i=0; $i < count($products); $i++) { 
            $orderItem = Order_item::where('product_id', '=', $products[$i])->where('order_id', '=', $order->id)->first();
            $orderItem->price = $orderItem->price - ($orderItem->price * $discounts[$i] / 100);
            $orderItem->discounts = $orderItem->discounts . ' ' . $discounts[$i] . '%';
            $orderItem->save();
        }
        
        // si envia un descuento por request
        // $isTotalDiscount = 0;
        // if ($request->category_discount && $request->discount) {
        //     // si el descuento es para toda la orden
        //     foreach ($request->category_discount as $index => $categoryDiscount) {
        //         # code...
        //         if($request->discount[$index]){
        //             if ($categoryDiscount == "all") {
        //                 $isTotalDiscount = $request->discount[$index];
        //             } else {
        //             // recorro todos los productos de la orden. 
        //                 foreach ($order->orderItemsIds() as $itemId) {
        //                     // llamo a sus categorías con getProductTaxonomies($productId) y dentro del array encuentra la categoria enviada por request aplico un descuento a su order_item->price o creo otra tabla discount_price?
        //                     if(in_array($categoryDiscount, getProductTaxonomies($itemId))){
        //                         $orderItem = Order_item::where('product_id', '=', $itemId)->where('order_id', '=', $order->id)->first();
        //                         $orderItem->price = $orderItem->price - ($orderItem->price * $request->discount[$index] / 100);
        //                         $orderItem->discounts = $orderItem->discounts . ' ' . $request->discount[$index] . '%,';
        //                         $orderItem->save();
        //                     }
        //                 }
        //             }
        //         }
        //     }
            // vuelvo a calcular el total de la orden con los nuevos precios
        $total = 0;

        foreach ($order->orderItems() as $item) {
            $total += ($item->quantity * $item->price);
        }
        $order->total = $total;
            // if($isTotalDiscount > 0) {
            //     $order->total = $order->total - ($isTotalDiscount * $order->total / 100);
            // }
            
        // }

        $filename = $this->createAndSavePdf($id, $request, $order);

        $order->status = 'pending';
        $order->info = $request->info;
        $order->concept_id = $request->concept_id;
        $order->document_link = 'storage' . '/' . $filename;

        $order->save();

        return redirect()->route('historySales');
    }

    function orderToCompleted(int $id, Request $request) {
        // encuentro la orden
        $order = Order::find($id);
        // encuentro sus items
        $orderItems = $order->orderItems();
        // busco el warehouse de donde se descontará el stock (hacer que lo elija el admin con un select)
        $warehouse = Warehouse::find($request->shopId);
        // encuentro el id del warehouse
        $warehouseId = $warehouse->id;

        // itero todos los items de la orden y les resto el stock en el warehouse elegido
        foreach ($orderItems as $item) {
            $productId = $item->product_id;
            // $stockToSubstract = $item->quantity; remove stock validation
            $stock = Warehouse::getProductStock($warehouseId, $productId);
            // if ($stock >= $stockToSubstract) { remove stock validation
                // $newStock = $stock - $stockToSubstract; remove stock validation
                $newStock = $stock - $item->quantity;

                Stocks::updateOrCreate(
                    ['warehouse_id' => $warehouseId, 'product_id' => $productId],
                    ['quantity' => $newStock]
                );
            // } else { 
                // return back()->with('error', 'No hay stock disponible de todos los productos de la orden en el local elegido');
            // }
        }

        // paso la orden a estado completed

        $order->status = 'completed';
        $order->save();

        return redirect()->route('historySales')->with('success', 'Orden completada');
    }

    function orderToCancelled(int $id) {
        $order = Order::find($id);
        $order->status = 'cancelled';
        $order->save();

        return redirect()->route('historySales')->with('success', 'Orden cancelada');
    }

    // ruta de prueba para render de pdf
    // function orderToPendingGet(Request $request) {
    //     $id = 1;
    //     $order = Order::find($id);   
    //     return view('/orders/orderInvoice', ['order' => $order, 'request' => $request]);
    //     // $this->createAndSavePdf($id, $request, $order);

    //     // $order->status = 'pending';
    //     // $order->save();

    //     // return redirect()->route('historySales');
    // }
    // Muesta la tabla de historial de ventas
    function historySales(Request $request) {
        $searchId = $request->get('id');
        $info = $request->get('info');
        $createdAt = $request->get('createdAt');

        $orders = Order::orderBy('created_at', 'DESC');
        if(!empty($searchId)){
            $orders = Order::where('id', '=', $searchId);
        }
        if(!empty($info)){
            $orders = Order::where('info', 'like', '%' . $info .'%');
        }

        if(!empty($createdAt)){
            $from = $createdAt . ' 00:00:00';
            $to = $createdAt . ' 23:59:59';
            $orders = $orders->whereBetween('created_at', array($from, $to));
        }
        $orders = $orders->paginate(100);
        
        $shops = Warehouse::getShops();
        $vac = compact('orders', 'shops', 'request');
        return view('history.sales', $vac);
    }

    // Muesta la tabla de historial de movimientos
    function historyMovements(Request $request) {

        // dd($request->all());

        $createdAt = $request->get('createdAt');
        $origin = $request->get('origin');
        $destiny = $request->get('destiny');

        $movements = Movement::orderBy('created_at', 'DESC');

        if(!empty($origin)){
            $movements = Movement::where('origin_warehouse_id', $request->origin);
        }

        if(!empty($destiny)){
            $movements = Movement::where('destiny_warehouse_id', $request->destiny);
        }

        if(!empty($createdAt)){
            $from = $createdAt . ' 00:00:00';
            $to = $createdAt . ' 23:59:59';
            $movements = $movements->whereBetween('created_at', array($from, $to));
        }

        $movements = $movements->paginate(100);
        $warehouses = Warehouse::all();
        $vac = compact('movements', 'warehouses', 'request');
        return view('history.movements', $vac);
    }
}
