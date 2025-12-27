<?php

use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Support\Str;
use App\Models\AccountsReceivable;
use Illuminate\Support\Facades\DB;


class PosCheckoutService
{
    public function checkout(array $cart, array $payment)
    {
        return DB::transaction(function () use ($cart, $payment) {

            $sale = Sale::create([
                'uuid' => Str::uuid(),
                'invoice_no' => $this->generateInvoice(),
                'user_id' => auth()->id,
                'subtotal' => $cart['subtotal'],
                'discount' => $cart['discount'],
                'tax' => $cart['tax'],
                'total' => $cart['total'],
                'payment_method' => $payment['method'],
                'cash_received' => $payment['cash'] ?? null,
                'change_amount' => $payment['change'] ?? null,
            ]);

            $inventory = new InventoryService();

            foreach ($cart['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'batch_id' => null,
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);

                $inventory->deductFIFO(
                    $product,
                    $item['qty'],
                    'SALE',
                    $sale->id
                );
            }

            if ($payment['method'] === 'credit') {
                AccountsReceivable::create([
                    'uuid' => Str::uuid(),
                    'customer_id' => $cart['customer_id'],
                    'sale_id' => $sale->id,
                    'amount' => $sale->total,
                    'balance' => $sale->total,
                    'status' => 'open'
                ]);
            }

            return $sale;
        });
    }

    private function generateInvoice()
    {
        return 'POS-' . now()->format('Ymd') . '-' . rand(1000, 9999);
    }
}
