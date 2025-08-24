<?php

namespace App\Http\Requests\Customers;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method mixed input(string $key = null, mixed $default = null)
 * @method array all(array $keys = null)
 * @method bool has(string|array $key)
 * @method mixed get(string $key, mixed $default = null)
 */
class CustomerStoreRequest extends FormRequest
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
            'code' => 'required|string|max:255|unique:customers,customer_code',
            'name' => 'required|string|max:255',
            'location.address' => 'required|string|max:500',
            'location.province' => 'required|string|max:255',
            'location.cities' => 'required|string|max:255',
            'location.district' => 'required|string|max:255',
            'location.subdistrict' => 'required|string|max:255',
            'location.postal_code' => 'required|string|max:10',
        ];
    }
}
