<?php

namespace App\Models;

use App\Models\Traits\GeneratesSku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use GeneratesSku;
    
    protected $fillable = [
        'sku',
        'barcode', 
        'name',
        'category_id',
        'supplier_id',
        'cost_price',
        'selling_price',
        'reorder_level', 
        'is_active',
        'product_image_id',
    ];

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function purchaseItems() : HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier() : BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function batches() : HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function productPhoto() : BelongsTo   
    {
        return $this->belongsTo(File::class, 'product_image_id');
    }
}
