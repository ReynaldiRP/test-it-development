<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailTransaction>
 */
class DetailTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => $this->faker->randomElement(Product::pluck('id')),
            'transaction_id' => $this->faker->randomElement(Transaction::pluck('id')),
            'invoice_number' => $this->faker->randomElement(Transaction::pluck('invoice_number')),
            'product_code' => $this->faker->randomElement(Product::pluck('product_code')),
            'product_name' => $this->faker->randomElement(Product::pluck('name')),
            'price_at_time' => $this->faker->randomElement(Product::pluck('price')),
            'disc1' => $this->faker->randomFloat(2, 0, 100),
            'disc2' => $this->faker->randomFloat(2, 0, 100),
            'disc3' => $this->faker->randomFloat(2, 0, 100),
            'net_price' => $this->faker->randomFloat(2, 0, 10000),
            'quantity' => $this->faker->numberBetween(1, 10),
            'amount' => $this->faker->randomFloat(2, 1000, 10000),
        ];
    }
}
