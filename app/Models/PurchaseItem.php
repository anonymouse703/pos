<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'cost_price',
        'unit_cost',
        'discount',
        'tax_rate',
        'tax_amount',
        'total',
        'batch_number',
        'expiry_date',
    ];

    public function purchase() : BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
