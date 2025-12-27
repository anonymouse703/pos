<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid', 
        'sku',
        'barcode', 
        'name',
        'category_id',
        'supplier_id',
        'cost_price',
        'selling_price',
        'reorder_level', 
        'is_active'
    ];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function batches() {
        return $this->hasMany(ProductBatch::class);
    }
}
