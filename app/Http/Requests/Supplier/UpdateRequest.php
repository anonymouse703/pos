<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Validation\Rule;
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
        return [
            'name' => 'string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'string|max:20',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('suppliers', 'email')->ignore($this->route('supplier')),
            ],
            'address' => 'nullable|string',
            'terms_days' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric',
        ];
    }
}
