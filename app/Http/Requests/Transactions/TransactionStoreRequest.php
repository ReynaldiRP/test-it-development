<?php

namespace App\Http\Requests\Transactions;

use Illuminate\Foundation\Http\FormRequest;

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
            'customer_id' => 'required|exists:customers,id',
            'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number',
            'invoice_date' => 'required|date',
            'total' => 'required|numeric|min:0',

            // Detail transaction
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.disc1' => 'nullable|numeric|min:0|max:100',
            'items.*.disc2' => 'nullable|numeric|min:0|max:100',
            'items.*.disc3' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
