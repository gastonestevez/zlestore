<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Product;

class WarehouseController extends Controller
{

  // public function index() {
  //     $warehouses = Warehouse::all();
  //     foreach ($warehouses as $warehouse) {
  //         $warehouse->getProducts;
  //     }
  //     return response([
  //         'warehouses' => $warehouses,
  //     ], 200);
  // }


  public function list() {
      return view('warehouses.warehouses', [
          'warehouses' => Warehouse::all(),
          ]);
  }

  public function show(int $id)
  {
    $warehouse = Warehouse::find($id);
    $vac = compact('warehouse');

    return view('warehouses.warehouse', $vac);
  }

  public function products(Request $request, int $id)
  {

    $sku = $request->get('sku');
      $name = $request->get('name');
      $price = $request->get('price');
      $woo_id = $request->get('woo_id');

    $warehouse = Warehouse::find($id);
    $products = $warehouse->getProducts()->orderBy('name')->sku($sku)->name($name)->price($price)->woo_id($woo_id)->paginate(25);
    $vac = compact('warehouse', 'products', 'request');

    return view('warehouses.warehouseProducts', $vac);
  }

  public function new() {
    $warehouses = Warehouse::all();
    $vac = compact('warehouses');
    return view('warehouses.createWarehouse', $vac);
  }


  public function store(Request $req) {
      Warehouse::create($req->all());
      $products = Product::all();

      $lastWarehouseId = Warehouse::all()->last()->id;
      
      foreach ($products as $product) {
        // aca indicamos que producto va updatear su stock, la cantidad nueva de stock y en que deposito se esta realizando
        $product->getWarehouses()->attach($lastWarehouseId, ['quantity' => 0]); // https://laravel.com/docs/8.x/eloquent-relationships Updating A Record On A Pivot Table
      }


    return redirect('/warehouse/list')
        ->with('success', 'Depósito creado exitosamente');
  }

  public function update(Request $request, int $id)
  {
    $warehouse = Warehouse::find($id);
    $warehouse->name = $request->name;
    $warehouse->address = $request->address;

    $warehouse->save();
   
    return redirect('/warehouse/list')
      ->with('success', 'Depósito editado exitosamente');
  }

  public function destroy(int $id)
  {
    $warehouse = Warehouse::find($id);
    $warehouse->delete();

    return redirect('/warehouse/list')
          ->with('success', 'Depósito eliminado exitosamente');
  }

}
