<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Sale extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid', 
        'invoice_no', 
        'user_id',
        'customer_id', 
        'subtotal',
        'discount', 
        'tax', 
        'total',
        'payment_method', 
        'cash_received',
        'change_amount', 
        'is_void'
    ];

    public function items() {
        return $this->hasMany(SaleItem::class);
    }
}
