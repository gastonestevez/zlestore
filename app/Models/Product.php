<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    public function getWarehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'stocks')->withPivot('quantity')->as('stock');
    }
}
