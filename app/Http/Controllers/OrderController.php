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
    public function orders()
    {
        $wc = $this->getWcConfig();
        $wcOrders = $wc->get('orders' . '?&order=asc&orderby=date&status=pending,processing');
        foreach ($wcOrders as $order) {
            $order->customerName = $this->getCustomerFullname($wc, $order);
            $order->date_created = (new Carbon($order->date_created))->format('Y-m-d H:i:s');          
        }
        return view('orders/orders', [
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

    public function storeOrder($id, Request $r)
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

    public function addProductToOrder(Request $request)
    {
        // Agrega un producto a una order

        // Busco si ya hay una orden en progreso
        $orderInProgress = Order::where('status', '=', 'in progress')->where('user_id', '=', auth()->user()->id)->get()->last();

        // Si no hay orden en progreso creo una nueva orden
        if (!$orderInProgress) {
            $newOrder = New Order();
            $newOrder->user_id = auth()->user()->id;
            $newOrder->concept_id = 1;
            $newOrder->save();
        }

        // Llamo a la ultima orden 
        $lastOrder = Order::get()->last();
        $lastOrderId = $lastOrder->id;

        // Instancio un nuevo order item y lo asigno a la orden
        $order_item = New Order_item();
        $order_item->product_id = $request->productId;
        $order_item->price = $request->price;
        $order_item->quantity = $request->quantity;
        $order_item->order_id = $lastOrderId;
        $order_item->save();

        // Sumo el valor del nuevo item al total de la orden
        $lastOrder->total = ($request->price * $request->quantity) + $lastOrder->total;
        $lastOrder->save();

        return back()->with('success', 'Producto agregado a la orden');
    }
}
