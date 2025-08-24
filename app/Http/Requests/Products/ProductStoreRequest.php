<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @method mixed input(string $key = null, mixed $default = null)
 * @method array all(array $keys = null)
 * @method bool has(string|array $key)
 * @method mixed get(string $key, mixed $default = null)
 */
class ProductStoreRequest extends FormRequest
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
            'product_code' => 'required|string|max:255|unique:products,product_code',
            'name' => 'required|alpha_num|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ];
    }
}
