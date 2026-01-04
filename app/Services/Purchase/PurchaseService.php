<?php

namespace App\Services\Purchase;

use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function store(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {

            $purchase = Purchase::create([
                'supplier_id'    => $data['supplier_id'],
                'invoice_no' => $data['invoice_no'],
                'purchase_date'  => $data['purchase_date'],
                'due_date'       => $data['due_date'],
                'subtotal'       => $data['subtotal'],
                'tax'            => $data['tax'],
                'discount'       => $data['discount'],
                'shipping'       => $data['shipping'],
                'total'          => $data['total'],
                'amount_paid'    => $data['amount_paid'],
                'balance'        => $data['balance'],
                'payment_method' => $data['payment_method'],
                'status'         => $data['status'],
                'notes'          => $data['notes'] ?? null,
                'reference'      => $data['reference'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $purchase->items()->create([
                    'product_id'   => $item['product_id'],
                    'quantity'     => $item['quantity'],
                    'cost_price'   => $item['cost_price'],
                    'unit_cost'    => $item['unit_cost'],
                    'discount'     => $item['discount'],
                    'tax_rate'     => $item['tax_rate'],
                    'tax_amount'   => $item['tax_amount'],
                    'total'        => $item['total'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'expiry_date'  => $item['expiry_date'] ?? null,
                ]);
            }

            return $purchase;
        });
    }
}
