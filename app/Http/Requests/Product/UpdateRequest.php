<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                'unique:products,barcode,' . $productId,
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'unit' => [
                'required',
                'in:' . implode(',', array_column(\App\Enums\Product\Unit::cases(), 'value')),
            ],

            'cost_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'selling_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'reorder_level' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'boolean',
            ],
        ];
    }
}
