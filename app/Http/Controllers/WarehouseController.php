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
}
