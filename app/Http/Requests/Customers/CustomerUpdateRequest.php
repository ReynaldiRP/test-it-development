<?php

namespace App\Http\Requests\Customers;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method mixed route(string $param = null, mixed $default = null)
 */

class CustomerUpdateRequest extends FormRequest
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
        $customer = $this->route('customer')->id; // @phpstan-ignore-line

        return [
            'location_id' => 'nullable|exists:indonesia_villages,id',
            'customer_code' => 'required|string|max:255|unique:customers,customer_code,' . $customer,
            'name' => 'required|alpha_num'
        ];
    }
}
