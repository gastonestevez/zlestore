<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Stocks;
use App\Models\Movement;
use Carbon\Carbon;
use DB;
use Auth;
use Illuminate\Support\Str;

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


  public function list()
  {
    return view('warehouses.warehouses', [
      'warehouses' => Warehouse::all()
    ]);
  }

  public function edit()
  {
    $warehouses = Warehouse::all();
    $vac = compact('warehouses');
    return view('warehouses.createWarehouse', $vac);
  }

  public function store(Request $request)
  {
    $warehouse = new Warehouse();
    $warehouse->name = $request->name;
    $warehouse->slug = Str::slug($request->name, '-');
    $warehouse->address = $request->address;
    $warehouse->type = $request->type;
    $warehouse->visibility = true;
    $warehouse->save();


    return redirect('/warehouses')
      ->with('success', 'Depósito creado exitosamente');
  }

  public function update(Request $request, int $id)
  {
    $warehouse = Warehouse::find($id);
    $warehouse->name = $request->name;
    $warehouse->address = $request->address;

    $warehouse->save();

    return redirect('/warehouses')
      ->with('success', 'Depósito editado exitosamente');
  }

  public function destroy(int $id)
  {
    $warehouse = Warehouse::find($id);
    $warehouse->delete();

    DB::table('stocks')
      ->where('warehouse_id', '=', $id)->delete();

    return redirect('/warehouses')
      ->with('success', 'Depósito eliminado exitosamente');
  }

  public function transferingUnits(Request $request, int $id)
  {
    $batch = $request->batch;
    if($batch) {
      $transferList = $request->transferList;
      if(!$transferList){
        return response()->json(['success' => false, 'message' => 'Hay campos incompletos.'], 500);
      }
      $firstLocal = Warehouse::where('type','shop')->get()->first();
      $firstStorage = Warehouse::where('type','storage')->get()->first();

      foreach ($transferList as $transfer) {
        if(!array_key_exists('productId', $transfer) ||  
          !array_key_exists('stock', $transfer)) {
          return response()->json(['success' => false, 'message' => 'Hay campos incompletos.'], 500);
        }
      }

      foreach ($transferList as $transfer) {
        if(!array_key_exists('warehouseFrom', $transfer)) {
          $transfer['warehouseFrom'] = $firstStorage->id;
        }
        if(!array_key_exists('warehouseTo', $transfer)) {
          $transfer['warehouseTo'] = $firstLocal->id;
        }
        
        $product = getProduct($transfer['productId']);
        $quantity = $transfer['stock'];
        $warehouseOrigin = $transfer['warehouseFrom'];
        $warehouseDestiny = $transfer['warehouseTo'];
        $stockInDestiny = Warehouse::getProductStock($warehouseDestiny, $transfer['productId']);
        $stockInOrigin = Warehouse::getProductStock($warehouseOrigin, $transfer['productId']);

        if ($quantity > $stockInOrigin) {
          return response()->json(['success' => false, 'message' => 'No hay tanta cantidad de stock.'], 500);
        } else {

          db::table('stocks')
            ->where('warehouse_id', '=', $warehouseOrigin)
            ->where('product_id', '=', $product->id)
            ->update(['quantity' => $stockInOrigin - $quantity]);

          Stocks::updateOrCreate(
            ['warehouse_id' => $warehouseDestiny, 'product_id' => $product->id],
            ['quantity' => $stockInDestiny + $quantity]
          );

          $movement = new Movement();
          $movement->product_id = $product->id;
          $movement->user_id = Auth()->user()->id;
          $movement->quantity = $quantity;
          $movement->origin_warehouse_id = $warehouseOrigin;
          $movement->destiny_warehouse_id = $warehouseDestiny;
          $movement->remaining_stock = $stockInOrigin - $quantity;
          $movement->status = 'completed';
          $movement->save();
        }
        // return redirect('/stock')->with('success', 'Transferencia exitosa');
      }
      return response()->json(['success' => true, 'message' => 'Transferencia exitosa.']);
      // return redirect()->back()
      // ->with('success', "Transferencias exitosas.");
    }

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
        ->update(['quantity' => $stockInOrigin - $quantity]);

      Stocks::updateOrCreate(
        ['warehouse_id' => $warehouseDestiny, 'product_id' => $product->id],
        ['quantity' => $stockInDestiny + $quantity]
      );

      $movement = new Movement();
      $movement->product_id = $product->id;
      $movement->user_id = Auth()->user()->id;
      $movement->quantity = $request->quantity;
      $movement->origin_warehouse_id = $warehouseOrigin;
      $movement->destiny_warehouse_id = $warehouseDestiny;
      $movement->remaining_stock = $stockInOrigin - $request->quantity;
      $movement->status = 'completed';
      $movement->save();

      return redirect()->back()
        ->with('success', "Transferencia de  {$movement->quantity} unidades de {$movement->warehouseOrigin->name} a {$movement->warehouseDestiny->name}");
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
        ->update(['quantity' => $stockInOrigin - $quantity]);

      db::table('stocks')
        ->where('warehouse_id', '=', $warehouseDestiny)
        ->where('product_id', '=', $product->id)
        ->update(['quantity' => $stockInDestiny + $quantity]);

      return redirect()->back()
        ->with('success', 'Stock actualizado exitosamente');
    }
  }

  public function exportCsv($id)
  {
    $warehouse = Warehouse::find($id);
    $products = Warehouse::getProductsByWarehouse($warehouse->id)->get();

    $filename = $warehouse->name .' - '.Carbon::now()->format('Y-m-d') . '.csv';
    $headers = array(
      "Content-type"        => "text/csv",
      "Content-Disposition" => "attachment; filename=$filename",
      "Pragma"              => "no-cache",
      "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
      "Expires"             => "0"
    );
    $columns = array(
      "ID",
      "SKU",
      "Nombre",
      "Precio",
      "Uni/caja",
      "Stock",
    );
    $callback = function () use ($products, $columns) {
      $file = fopen('php://output', 'w');
      fputcsv($file, $columns);

      foreach ($products as $product) {
        $row['ID']  = $product->id;
        $row['SKU']    = $product->sku;
        $row['Nombre']    = $product->name;
        $row['Price']    = $product->price;
        $row['Uni/caja']    = $product->units_in_box;
        $row['Stock']  = $product->quantity;

        fputcsv($file, array($row['ID'], $row['SKU'], $row['Nombre'], $row['Price'], $row['Uni/caja'], $row['Stock']));
      }

      fclose($file);
    };
    return response()->stream($callback, 200, $headers);
  }
}
