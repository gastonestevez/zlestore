<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

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

  public function products(int $id)
  {
    $warehouse = Warehouse::find($id);
    $products = $warehouse->getProducts()->orderBy('name')->paginate(25);
    $vac = compact('warehouse', 'products');

    return view('warehouses.warehouseProducts', $vac);
  }

  public function new() {
    $warehouses = Warehouse::all();
    $vac = compact('warehouses');
    return view('warehouses.createWarehouse', $vac);
  }


  public function store(Request $req) {
      Warehouse::create($req->all());

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
