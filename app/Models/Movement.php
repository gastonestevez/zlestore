<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Warehouse;

class Movement extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function warehouseOrigin()
    {
        return $this->belongsTo(Warehouse::class, 'origin_warehouse_id');
    }

    public function warehouseDestiny()
    {
        return $this->belongsTo(Warehouse::class, 'destiny_warehouse_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
