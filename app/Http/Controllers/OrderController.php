<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;
use App\Models\Warehouse;
use App\Models\Product;


class OrderController extends Controller
{
     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function orders()
    {
        //return view('orders/orders');
        $wc = $this->getWcConfig();
        $wcOrders = $wc->get('orders' . '?&order=desc&orderby=date&status=pending');
        foreach ($wcOrders as $order) {
            $order->customerName = $this->getCustomerFullname($wc, $order);
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
        if(count($warehouses) == 0) {
            return redirect()->route('newWarehouse')->with('error', 'No hay depósitos para distribuir la orden.');
        }

        foreach ($wcOrder->line_items as $item) {
            $item->localId = Product::where('woo_id','=',$item->product_id)->first()->id;
        }

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
            $productDB = Product::find($idProduct);
            foreach ($stocks as $idWarehouse => $stock) {
                $warehouseDB = Warehouse::find($idWarehouse);
                $newStock = $warehouseDB->getProductStock($idWarehouse, $idProduct) - $stock;
                $productDB
                    ->getWarehouses()
                    ->updateExistingPivot(
                        $idWarehouse, 
                        ['quantity' => $newStock]
                    );
            }
        }
        
        $wc = $this->getWcConfig();
        $wcOrders = $wc->get('orders' . '?&order=desc&orderby=date&status=pending');

        // Liberar cuando esté en producción.
        if($transition)
        {
            $data = [ 'status' => $transition ];
            //$wc->put('orders/' . $id, $data);
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
}
