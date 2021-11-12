<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\Order;
use App\Models\Warehouse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       Paginator::useBootstrap();

       // https://www.youtube.com/watch?v=7QWZxjgvEQc
      // Especificamos las vistas donde queremos compartir estas variables
      View::composer(['partials.navbar'], function($view){

        // Traemos todo lo que queremos imprimir en la navbar
        $orderInProgress = Order::where('status', '=', 'in progress')->where('user_id', '=', auth()->user()->id)->get()->last();
        // if ternario
        $id = isset($orderInProgress)?$orderInProgress->id:'';
        
        $warehouses = Warehouse::all();

        // Especificamos los nombres y valores de las variables a compartir
        // $view->with('orderInProgress', $orderInProgress)->with('id', $id);
        $view->with(['orderInProgress' => $orderInProgress, 'id' => $id, 'warehouses' => $warehouses]);
      });
    }
}
