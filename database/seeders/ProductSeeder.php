<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'product_code' => 'ELC001',
                'name' => 'Laptop ASUS VivoBook 14',
                'price' => 7500000,
                'stock' => 25,
            ],
            [
                'product_code' => 'ELC002',
                'name' => 'Smartphone Samsung Galaxy A54',
                'price' => 4200000,
                'stock' => 40,
            ],
            [
                'product_code' => 'ELC003',
                'name' => 'Headphone Sony WH-1000XM4',
                'price' => 3800000,
                'stock' => 15,
            ],
            [
                'product_code' => 'OFF001',
                'name' => 'Kursi Kantor Ergonomis',
                'price' => 1200000,
                'stock' => 30,
            ],
            [
                'product_code' => 'OFF002',
                'name' => 'Meja Kerja Minimalis',
                'price' => 850000,
                'stock' => 20,
            ],
            [
                'product_code' => 'ACC001',
                'name' => 'Mouse Wireless Logitech MX Master 3',
                'price' => 1350000,
                'stock' => 50,
            ],
            [
                'product_code' => 'ACC002',
                'name' => 'Keyboard Mechanical Cherry MX',
                'price' => 1800000,
                'stock' => 35,
            ],
            [
                'product_code' => 'ELC004',
                'name' => 'Monitor LED 24 inch LG UltraWide',
                'price' => 2800000,
                'stock' => 18,
            ],
            [
                'product_code' => 'ACC003',
                'name' => 'Webcam Logitech C920 HD',
                'price' => 1100000,
                'stock' => 45,
            ],
            [
                'product_code' => 'ELC005',
                'name' => 'Printer Canon PIXMA G2020',
                'price' => 2200000,
                'stock' => 12,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
