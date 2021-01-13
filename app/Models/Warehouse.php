<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\stocks;

class Warehouse extends Model
{
    use HasFactory;
    protected $table = 'Warehouse';
    protected $fillable = ['name', 'address', 'visibility'];

    public function getProducts()
    {
        return $this->belongsToMany(Product::class, 'stocks')->withPivot('quantity')->as('stock');
    }

}
