<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Automattic\WooCommerce\Client;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('index');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function orders()
    {
        return view('orders/orders');
        // $wc = $this->getWcConfig();
        // $wcOrders = $wc->get('orders');
        // foreach ($wcOrders as $order) {
        //     $order->customerName = $this->getCustomerFullname($wc, $order);
        // }
        // return view('orders', [
        //     'orders' => $wcOrders,
        // ]);
    }

    private function getCustomerFullname(Client $wc, $order)
    {
        $customer = $wc->get('customers/'.$order->customer_id);
        return $customer->first_name . ' ' . $customer->last_name;
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
