<?php

namespace App\Http\Requests\Transactions;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method mixed route(string $param = null, mixed $default = null)
 * @method mixed input(string $key = null, mixed $default = null)
 * @method array all(array $keys = null)
 * @method bool has(string|array $key)
 * @method mixed get(string $key, mixed $default = null)
 */
class TransactionUpdateRequest extends FormRequest
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
            'invoice_number' => 'required|string|max:255|unique:transactions,invoice_number,' . $this->route('transaction')->id,
            'invoice_date' => 'required|date',
            'total' => 'required|numeric|min:0',
        ];
    }
}
