<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid', 
        'product_id', 
        'supplier_id',
        'batch_number', 
        'cost_price',
        'quantity_received', 
        'quantity_remaining',
        'received_at'
    ];

    protected $casts = [
        'received_at' => 'datetime'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
