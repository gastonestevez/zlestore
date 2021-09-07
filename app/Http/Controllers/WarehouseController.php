<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use DB;

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
          'products' => count(getProducts())
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

    $warehouse = Warehouse::find($id);
    $products = getProducts();

    foreach ($products as $product) {
      $stock = $warehouse->getProductStock($warehouse->id, $product->id);
      if ($stock > 0 && $product->units_in_box > 0) {
        $boxes = intval($stock / $product->units_in_box);
      } else {
        $boxes = 0;
      }
    }
    
    $vac = compact('warehouse', 'products', 'request', 'boxes');

    return view('warehouses.warehouseProducts', $vac);
  }

  public function new() {
    $warehouses = Warehouse::all();
    $vac = compact('warehouses');
    return view('warehouses.createWarehouse', $vac);
  }


  public function store(Request $req) {
      Warehouse::create($req->all());
      $products = getProducts();

      $lastWarehouseId = Warehouse::all()->last()->id;
      
      foreach ($products as $product) {
        DB::table('stocks')
        ->insert([
          'product_id' => $product->id,
          'warehouse_id' => $lastWarehouseId,
          'quantity' => 0
        ]);

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

  public function transferingUnits(Request $request, int $id) 
  {
    
    $product = getProduct($id);
    $quantity = $request->quantity;
    $warehouseOrigin = $request->warehouseOrigin;
    $warehouseDestiny = $request->warehouseDestiny;
    $stockInDestiny = Warehouse::getProductStock($warehouseDestiny, $id);

    $stockInOrigin = Warehouse::getProductStock($warehouseOrigin, $id);
    if ($request->quantity > $stockInOrigin) {
      return redirect()->back()
          ->with('error', 'No hay tanta cantidad de stock');
    } else {

      db::table('stocks')
          ->where('warehouse_id', '=', $warehouseOrigin)
          ->where('product_id', '=', $product->id)
          ->update(['quantity' => $stockInOrigin-$quantity]);

      db::table('stocks')
          ->where('warehouse_id', '=', $warehouseDestiny)
          ->where('product_id', '=', $product->id)
          ->update(['quantity' => $stockInDestiny+$quantity]);    

      return redirect()->back()
          ->with('success', 'Stock actualizado exitosamente');
    }
  }

  public function transferingBoxes(Request $request, int $id) 
  {
    
    $product = getProduct($id);
    $quantity = $request->quantity * $product->units_in_box;
    $warehouseOrigin = $request->warehouseOrigin;
    $warehouseDestiny = $request->warehouseDestiny;
    $stockInDestiny = Warehouse::getProductStock($warehouseDestiny, $id);
    $stockInOrigin = Warehouse::getProductStock($warehouseOrigin, $id);

    $boxesInOrigin = intval(Warehouse::getProductStock($warehouseOrigin, $id) / $product->units_in_box);
    if ($request->quantity > $boxesInOrigin) {
      return redirect()->back()
          ->with('error', 'No hay tanta cantidad de stock');
    } else {
      db::table('stocks')
          ->where('warehouse_id', '=', $warehouseOrigin)
          ->where('product_id', '=', $product->id)
          ->update(['quantity' => $stockInOrigin-$quantity]);

      db::table('stocks')
          ->where('warehouse_id', '=', $warehouseDestiny)
          ->where('product_id', '=', $product->id)
          ->update(['quantity' => $stockInDestiny+$quantity]);

      return redirect()->back()
          ->with('success', 'Stock actualizado exitosamente');
    }
  }

}
