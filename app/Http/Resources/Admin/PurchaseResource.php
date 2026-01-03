<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
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
            'invoice_number' => $this->invoice_number,
            'purchase_date' => $this->purchase_date,
            'due_date' => $this->due_date,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'discount' => $this->discount,
            'shipping' => $this->shipping,
            'total' => $this->total,
            'amount_paid' => $this->amount_paid,
            'balance' => $this->balance,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
            'reference' => $this->reference,

            // RELATIONSHIPS
            'supplier' => new CategoryResource($this->whenLoaded('supplier')),
        ];
    }
}
