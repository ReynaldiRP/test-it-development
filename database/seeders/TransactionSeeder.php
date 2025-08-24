<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\DetailTransaction;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define realistic transactions with varying dates
        $transactions = [
            [
                'customer_code' => 'CUST001', // PT Maju Bersama
                'invoice_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'invoice_number' => 'INV/2508/0001',
                'items' => [
                    [
                        'product_code' => 'ELC001', // Laptop ASUS VivoBook 14
                        'quantity' => 3,
                        'disc1' => 5.0,
                        'disc2' => 2.0,
                        'disc3' => 0.0,
                    ],
                    [
                        'product_code' => 'ACC001', // Mouse Wireless Logitech
                        'quantity' => 5,
                        'disc1' => 10.0,
                        'disc2' => 0.0,
                        'disc3' => 0.0,
                    ],
                ]
            ],
            [
                'customer_code' => 'CUST002', // CV Sejahtera Abadi
                'invoice_date' => Carbon::now()->subDays(8)->format('Y-m-d'),
                'invoice_number' => 'INV/2508/0002',
                'items' => [
                    [
                        'product_code' => 'ELC002', // Smartphone Samsung Galaxy A54
                        'quantity' => 2,
                        'disc1' => 3.0,
                        'disc2' => 1.5,
                        'disc3' => 0.0,
                    ],
                    [
                        'product_code' => 'ELC003', // Headphone Sony WH-1000XM4
                        'quantity' => 1,
                        'disc1' => 0.0,
                        'disc2' => 0.0,
                        'disc3' => 0.0,
                    ],
                ]
            ],
            [
                'customer_code' => 'CUST003', // Toko Elektronik Berkah
                'invoice_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'invoice_number' => 'INV/2508/0003',
                'items' => [
                    [
                        'product_code' => 'ELC004', // Monitor LED 24 inch LG
                        'quantity' => 2,
                        'disc1' => 7.5,
                        'disc2' => 2.5,
                        'disc3' => 1.0,
                    ],
                    [
                        'product_code' => 'ACC002', // Keyboard Mechanical Cherry MX
                        'quantity' => 3,
                        'disc1' => 5.0,
                        'disc2' => 0.0,
                        'disc3' => 0.0,
                    ],
                    [
                        'product_code' => 'ACC003', // Webcam Logitech C920 HD
                        'quantity' => 2,
                        'disc1' => 8.0,
                        'disc2' => 3.0,
                        'disc3' => 0.0,
                    ],
                ]
            ],
            [
                'customer_code' => 'CUST004', // PT Teknologi Nusantara
                'invoice_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'invoice_number' => 'INV/2508/0004',
                'items' => [
                    [
                        'product_code' => 'OFF001', // Kursi Kantor Ergonomis
                        'quantity' => 10,
                        'disc1' => 15.0,
                        'disc2' => 5.0,
                        'disc3' => 2.0,
                    ],
                    [
                        'product_code' => 'OFF002', // Meja Kerja Minimalis
                        'quantity' => 8,
                        'disc1' => 12.0,
                        'disc2' => 3.0,
                        'disc3' => 0.0,
                    ],
                ]
            ],
            [
                'customer_code' => 'CUST005', // UD Sumber Rejeki
                'invoice_date' => Carbon::now()->subDays(4)->format('Y-m-d'),
                'invoice_number' => 'INV/2508/0005',
                'items' => [
                    [
                        'product_code' => 'ELC005', // Printer Canon PIXMA G2020
                        'quantity' => 1,
                        'disc1' => 2.5,
                        'disc2' => 0.0,
                        'disc3' => 0.0,
                    ],
                    [
                        'product_code' => 'ACC001', // Mouse Wireless Logitech
                        'quantity' => 3,
                        'disc1' => 8.0,
                        'disc2' => 2.0,
                        'disc3' => 0.0,
                    ],
                ]
            ],
            [
                'customer_code' => 'CUST006', // PT Digital Solution
                'invoice_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'invoice_number' => 'INV/2508/0006',
                'items' => [
                    [
                        'product_code' => 'ELC001', // Laptop ASUS VivoBook 14
                        'quantity' => 5,
                        'disc1' => 10.0,
                        'disc2' => 5.0,
                        'disc3' => 2.0,
                    ],
                    [
                        'product_code' => 'ELC004', // Monitor LED 24 inch LG
                        'quantity' => 5,
                        'disc1' => 8.0,
                        'disc2' => 3.0,
                        'disc3' => 0.0,
                    ],
                    [
                        'product_code' => 'ACC002', // Keyboard Mechanical Cherry MX
                        'quantity' => 5,
                        'disc1' => 6.0,
                        'disc2' => 2.0,
                        'disc3' => 0.0,
                    ],
                ]
            ],
            [
                'customer_code' => 'CUST007', // Toko Furniture Modern
                'invoice_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'invoice_number' => 'INV/2508/0007',
                'items' => [
                    [
                        'product_code' => 'OFF001', // Kursi Kantor Ergonomis
                        'quantity' => 6,
                        'disc1' => 12.0,
                        'disc2' => 4.0,
                        'disc3' => 1.0,
                    ],
                    [
                        'product_code' => 'OFF002', // Meja Kerja Minimalis
                        'quantity' => 4,
                        'disc1' => 10.0,
                        'disc2' => 2.0,
                        'disc3' => 0.0,
                    ],
                ]
            ],
            [
                'customer_code' => 'CUST008', // CV Karya Mandiri
                'invoice_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'invoice_number' => 'INV/2508/0008',
                'items' => [
                    [
                        'product_code' => 'ELC002', // Smartphone Samsung Galaxy A54
                        'quantity' => 1,
                        'disc1' => 0.0,
                        'disc2' => 0.0,
                        'disc3' => 0.0,
                    ],
                    [
                        'product_code' => 'ELC003', // Headphone Sony WH-1000XM4
                        'quantity' => 2,
                        'disc1' => 5.0,
                        'disc2' => 0.0,
                        'disc3' => 0.0,
                    ],
                    [
                        'product_code' => 'ACC003', // Webcam Logitech C920 HD
                        'quantity' => 1,
                        'disc1' => 3.0,
                        'disc2' => 0.0,
                        'disc3' => 0.0,
                    ],
                ]
            ],
        ];

        foreach ($transactions as $transactionData) {
            // Get customer by code
            $customer = Customer::where('customer_code', $transactionData['customer_code'])->first();

            if (!$customer) {
                continue; // Skip if customer not found
            }

            $totalAmount = 0;
            $transactionItems = [];

            // Calculate total and prepare items
            foreach ($transactionData['items'] as $itemData) {
                $product = Product::where('product_code', $itemData['product_code'])->first();

                if (!$product) {
                    continue; // Skip if product not found
                }

                // Calculate net price with sequential discounts
                $price = $product->price;
                $netPrice = $price;
                $netPrice -= ($netPrice * ($itemData['disc1'] / 100));
                $netPrice -= ($netPrice * ($itemData['disc2'] / 100));
                $netPrice -= ($netPrice * ($itemData['disc3'] / 100));
                $netPrice = round($netPrice, 2);

                $amount = $netPrice * $itemData['quantity'];
                $totalAmount += $amount;

                $transactionItems[] = [
                    'product' => $product,
                    'quantity' => $itemData['quantity'],
                    'price_at_time' => $price,
                    'disc1' => $itemData['disc1'],
                    'disc2' => $itemData['disc2'],
                    'disc3' => $itemData['disc3'],
                    'net_price' => $netPrice,
                    'amount' => $amount,
                ];
            }

            // Create transaction
            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'invoice_number' => $transactionData['invoice_number'],
                'invoice_date' => $transactionData['invoice_date'],
                'total' => $totalAmount,
            ]);

            // Create detail transactions
            foreach ($transactionItems as $item) {
                DetailTransaction::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'invoice_number' => $transaction->invoice_number,
                    'product_code' => $item['product']->product_code,
                    'product_name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'price_at_time' => $item['price_at_time'],
                    'disc1' => $item['disc1'],
                    'disc2' => $item['disc2'],
                    'disc3' => $item['disc3'],
                    'net_price' => $item['net_price'],
                    'amount' => $item['amount'],
                ]);

                // Update product stock
                $item['product']->decrement('stock', $item['quantity']);
            }
        }
    }
}
