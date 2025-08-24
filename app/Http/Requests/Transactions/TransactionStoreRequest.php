<?php

namespace App\Http\Requests\Transactions;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method mixed input(string $key = null, mixed $default = null)
 * @method array all(array $keys = null)
 * @method bool has(string|array $key)
 * @method mixed get(string $key, mixed $default = null)
 */

class TransactionStoreRequest extends FormRequest
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
            'customer_id' => 'required|integer|exists:customers,id',
            'invoice_date' => 'required|date',
            'total' => 'required|numeric|min:0',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_at_time' => 'required|numeric|min:0',
            'items.*.disc1' => 'nullable|numeric|min:0|max:100',
            'items.*.disc2' => 'nullable|numeric|min:0|max:100',
            'items.*.disc3' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer is required.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'invoice_date.required' => 'Invoice date is required.',
            'total.required' => 'Total amount is required.',
            'total.min' => 'Total amount must be greater than 0.',
            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item is required.',
            'items.*.product_id.required' => 'Product selection is required for each item.',
            'items.*.product_id.exists' => 'Selected product does not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.price_at_time.required' => 'Price is required for each item.',
            'items.*.price_at_time.min' => 'Price cannot be negative.',
            'items.*.disc1.max' => 'Discount 1 cannot exceed 100%.',
            'items.*.disc2.max' => 'Discount 2 cannot exceed 100%.',
            'items.*.disc3.max' => 'Discount 3 cannot exceed 100%.',
        ];
    }
}
