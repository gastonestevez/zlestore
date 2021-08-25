<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'woo_id' => $row[0],
            'sku' => $row[1],
            'name' => $row[2],
            'price' => $row[4],
            'units_in_box' => $row[5] ?? 0,
        ]);
    }
}
