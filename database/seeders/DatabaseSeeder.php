<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\DetailTransaction;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Customer::factory(5)->create();
        Product::factory(5)->create();
        Transaction::factory(5)->create();
        DetailTransaction::factory(5)->create();
    }
}
