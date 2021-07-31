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

  public function transferingUnits(Request $request, int $id) 
  {
    
    $product = Product::where('id', '=', $id)->first();
    $quantity = $request->quantity;
    $warehouseOrigin = $request->warehouseOrigin;
    $warehouseDestiny = $request->warehouseDestiny;
    $stockInDestiny = Warehouse::getProductStock($warehouseDestiny, $id);

    $stockInOrigin = Warehouse::getProductStock($warehouseOrigin, $id);
    if ($request->quantity > $stockInOrigin) {
      return redirect()->back()
          ->with('error', 'No hay tanta cantidad de stock');
    } else {
      $product->getWarehouses()->updateExistingPivot($warehouseOrigin, ['quantity' => ($stockInOrigin-$quantity)]);
      $product->getWarehouses()->updateExistingPivot($warehouseDestiny, ['quantity' => ($stockInDestiny +$quantity)]);
      return redirect()->back()
          ->with('success', 'Stock actualizado exitosamente');
    }
  }

  public function transferingBoxes(Request $request, int $id) 
  {
    
    $product = Product::where('id', '=', $id)->first();
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
      $product->getWarehouses()->updateExistingPivot($warehouseOrigin, ['quantity' => ($stockInOrigin - $quantity)]);
      $product->getWarehouses()->updateExistingPivot($warehouseDestiny, ['quantity' => ($stockInDestiny + $quantity)]);
      return redirect()->back()
          ->with('success', 'Stock actualizado exitosamente');
    }
  }

}
