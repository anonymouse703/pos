<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'unit' => $this->unit,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'reorder_level' => $this->reorder_level,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),

            // RELATIONSHIPS
            'category' => new CategoryResource($this->whenLoaded('category')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'product_photo' => new FileResource($this->whenLoaded('productPhoto')),
        ];
    }
}
