<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
         return [
            // Purchase
            'supplier_id'     => ['required', 'exists:suppliers,id'],
            'invoice_no'  => ['required', 'string', 'max:50'],
            'purchase_date'   => ['required', 'date'],
            'due_date'        => ['nullable', 'date', 'after_or_equal:purchase_date'],

            'subtotal'        => ['required', 'numeric', 'min:0'],
            'tax'             => ['required', 'numeric', 'min:0'],
            'discount'        => ['required', 'numeric', 'min:0'],
            'shipping'        => ['required', 'numeric', 'min:0'],
            'total'           => ['required', 'numeric', 'min:0'],
            'amount_paid'     => ['required', 'numeric', 'min:0'],
            'balance'         => ['required', 'numeric'],

            'payment_method'  => ['required', 'string'],
            'status'          => ['required', 'in:unpaid,paid,partial'],

            'notes'           => ['nullable', 'string'],
            'reference'       => ['nullable', 'string'],

            // Items
            'items'                       => ['required', 'array', 'min:1'],
            'items.*.product_id'          => ['required', 'exists:products,id'],
            'items.*.quantity'            => ['required', 'numeric', 'min:1'],
            'items.*.cost_price'          => ['required', 'numeric', 'min:0'],
            'items.*.unit_cost'           => ['required', 'numeric', 'min:0'],
            'items.*.discount'            => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate'            => ['required', 'numeric', 'min:0'],
            'items.*.tax_amount'          => ['required', 'numeric', 'min:0'],
            'items.*.total'               => ['required', 'numeric', 'min:0'],
            'items.*.batch_number'        => ['nullable', 'string'],
            'items.*.expiry_date'         => ['nullable', 'date'],
        ];
    }
}
