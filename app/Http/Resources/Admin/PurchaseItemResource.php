<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'cost_price' => $this->cost_price,
            'unit_cost' => $this->unit_cost,
            'discount' => $this->discount,
            'tax_rate' => $this->tax_rate,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'batch_number' => $this->batch_number,
            'expiry_date' => $this->expiry_date,

            // RELATIONSHIPS
            'purchase' => new PurchaseResource($this->whenLoaded('purchase')),
            'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}
