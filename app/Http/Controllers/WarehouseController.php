<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    public function create(Request $req) {
        Warehouse::create($req->all());
        return redirect('/warehouse/list');
    }

    public function list() {
        return view('warehouse', [
            'warehouses' => Warehouse::all(),
            ]);
    }

    public function index() {
        $warehouses = Warehouse::all();
        foreach ($warehouses as $warehouse) {
            $warehouse->getProducts;
        }
        return response([
            'warehouses' => $warehouses,
        ], 200);
    }
}
