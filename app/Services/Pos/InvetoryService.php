<?php

use App\Models\Product;
use Illuminate\Support\Str;
use App\Models\ProductBatch;
use App\Models\InventoryMovement;

class InventoryService
{
    public function deductFIFO(Product $product, int $quantity, string $refType, int $refId)
    {
        $batches = ProductBatch::where('product_id', $product->id)
            ->where('quantity_remaining', '>', 0)
            ->orderBy('received_at')
            ->lockForUpdate()
            ->get();

        $remaining = $quantity;

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;

            $deduct = min($batch->quantity_remaining, $remaining);

            $batch->decrement('quantity_remaining', $deduct);

            InventoryMovement::create([
                'uuid' => Str::uuid(),
                'product_id' => $product->id,
                'batch_id' => $batch->id,
                'type' => 'OUT',
                'reference_type' => $refType,
                'reference_id' => $refId,
                'quantity' => $deduct,
                'balance_after' => $batch->quantity_remaining - $deduct,
            ]);

            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            throw new Exception('Insufficient stock');
        }
    }
}
