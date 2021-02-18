<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    public function store(Request $req) {
        Warehouse::create($req->all());

        return redirect('/warehouse/list')
          ->with('status', 'Depósito eliminado exitosamente');;
    }

    public function list() {
        return view('warehouses', [
            'warehouses' => Warehouse::all(),
            ]);
    }

    public function show(int $id)
  {
    $warehouse = Warehouse::find($id);
    $vac = compact('warehouse');

    return view('/warehouse', $vac);
  }

    public function update(Request $request, int $id)
    {
      $warehouse = Warehouse::find($id);
      $request->name = $warehouse->name;
      $request->address = $warehouse->address;
      $request->visibility = $warehouse->visibility;

      $warehouse->save();

      return redirect()->back()
        ->with('success', 'Depósito editado exitosamente');
    }

    public function destroy(int $id)
    {
      $warehouse = Warehouse::find($id);
      $warehouse->delete();

      return redirect()->back()
            ->with('status', 'Depósito eliminado exitosamente');
    }
}
