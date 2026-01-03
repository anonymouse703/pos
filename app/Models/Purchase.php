<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'invoice_number',
        'purchase_date',
        'due_date',
        'subtotal',
        'tax',
        'discount',
        'shipping',
        'total',
        'amount_paid',
        'balance',
        'payment_status',
        'payment_method',
        'notes',
        'reference',
    ];

    public function items() : HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier() : BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
