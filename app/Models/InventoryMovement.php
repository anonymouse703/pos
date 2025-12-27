<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'uuid', 
        'product_id', 
        'batch_id',
        'type', 
        'reference_type',
        'reference_id', 
        'quantity',
        'balance_after'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
