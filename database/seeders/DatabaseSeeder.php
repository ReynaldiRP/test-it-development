<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\DetailTransaction;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\DistrictsSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Laravolt\Indonesia\Seeds\VillagesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Customer::factory(5)->create();
        // Product::factory(5)->create();
        // Transaction::factory(5)->create();
        // DetailTransaction::factory(5)->create();
        // $this->call([
        //     ProvincesSeeder::class,
        //     CitiesSeeder::class,
        //     DistrictsSeeder::class,
        //     VillagesSeeder::class,
        // ]);
    }
}
