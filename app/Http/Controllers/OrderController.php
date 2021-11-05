<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Warehouse;
use Carbon\Carbon;
use App\Models\Stocks;
use App\Models\Order;
use App\Models\Order_item;
use Auth;


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
            return redirect()->route('newWarehouse')->with('error', 'No hay depósitos para distribuir la orden.');
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

    public function addProductToOrder(Request $request)  // Agrega un producto a una order
    {
       
        // Busco si ya hay una orden en progreso
        // $orderInProgress = Order::where('status', '=', 'in progress')->where('user_id', '=', auth()->user()->id)->get()->last();

        $orderInProgress = Order::updateOrCreate(
            ['status' => 'in progress', 'user_id' => auth()->user()->id],
            ['concept_id' => 1]
        );

        // Si no hay orden en progreso creo una nueva orden
        // if (!$orderInProgress) {
        //     $newOrder = New Order();
        //     $newOrder->user_id = auth()->user()->id;
        //     $newOrder->concept_id = 1;
        //     $newOrder->save();
        // }

        // Llamo a la ultima orden 
        $lastOrder = Order::get()->last();
        $lastOrderId = $lastOrder->id;

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
        $order_item = Order_item::updateOrCreate(
            ['product_id' => $request->productId, 'order_id' => $lastOrderId,
            'product_name' => $request->name,
            'product_sku' => $request->sku,
            'order_id' => $lastOrderId,
            'price' => $request->price],
            ['quantity' => $request->quantity]
        );

        // Calculo el valor total de la orden
        $total = 0;
        foreach ($lastOrder->orderItems() as $item) {
            $total += ($item->quantity * $item->price);
        }

        $lastOrder->total = $total;
        $lastOrder->save();

        return back()->with('success', 'Producto agregado a la orden');

    }

    function removeProduct(int $id) {
        $product = Order_item::find($id);
        $order = Order::find($product->order_id);
        $product->delete();

        // Si la orden se queda sin productos la elimino
        if (count($order->orderItems()) == 0) {
            $order->delete();
            return back()->with('success', 'Orden eliminada');
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

    function confirmOrder(int $id) {

        $order = Order::find($id);
        $vac = compact('order');

        return view('/orders/confirmOrder', $vac);
    }
}
