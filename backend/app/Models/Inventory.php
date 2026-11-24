<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;

    protected $table = 'inventory';

    protected $primaryKey = 'id';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'quantity_on_hand',
        'reserved_quantity',
        'reorder_level',
        'warehouse_id',
        'location',
        'last_restocked_at',
    ];

    protected $casts = [
        'quantity_on_hand' => 'integer',
        'reserved_quantity' => 'integer',
        'reorder_level' => 'integer',
        'last_restocked_at' => 'datetime',
    ];

    public function getAvailableQuantityAttribute()
    {
        return $this->quantity_on_hand - $this->reserved_quantity;
    }
}