<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Location;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'customer_code' => 'CUST001',
                'name' => 'PT Maju Bersama',
                'location' => [
                    'address' => 'Jl. Sudirman No. 123',
                    'province' => 'DKI Jakarta',
                    'cities' => 'Jakarta Pusat',
                    'district' => 'Tanah Abang',
                    'sub_district' => 'Bendungan Hilir',
                    'postal_code' => '10210',
                ]
            ],
            [
                'customer_code' => 'CUST002',
                'name' => 'CV Sejahtera Abadi',
                'location' => [
                    'address' => 'Jl. Malioboro No. 45',
                    'province' => 'Daerah Istimewa Yogyakarta',
                    'cities' => 'Yogyakarta',
                    'district' => 'Gedongtengen',
                    'sub_district' => 'Sosromenduran',
                    'postal_code' => '55271',
                ]
            ],
            [
                'customer_code' => 'CUST003',
                'name' => 'Toko Elektronik Berkah',
                'location' => [
                    'address' => 'Jl. Braga No. 78',
                    'province' => 'Jawa Barat',
                    'cities' => 'Bandung',
                    'district' => 'Sumur Bandung',
                    'sub_district' => 'Braga',
                    'postal_code' => '40111',
                ]
            ],
            [
                'customer_code' => 'CUST004',
                'name' => 'PT Teknologi Nusantara',
                'location' => [
                    'address' => 'Jl. Imam Bonjol No. 56',
                    'province' => 'Jawa Timur',
                    'cities' => 'Surabaya',
                    'district' => 'Genteng',
                    'sub_district' => 'Embong Kaliasin',
                    'postal_code' => '60271',
                ]
            ],
            [
                'customer_code' => 'CUST005',
                'name' => 'UD Sumber Rejeki',
                'location' => [
                    'address' => 'Jl. Gajah Mada No. 234',
                    'province' => 'Jawa Tengah',
                    'cities' => 'Semarang',
                    'district' => 'Semarang Tengah',
                    'sub_district' => 'Sekayu',
                    'postal_code' => '50132',
                ]
            ],
            [
                'customer_code' => 'CUST006',
                'name' => 'PT Digital Solution',
                'location' => [
                    'address' => 'Jl. Asia Afrika No. 89',
                    'province' => 'Jawa Barat',
                    'cities' => 'Bandung',
                    'district' => 'Sumur Bandung',
                    'sub_district' => 'Kebon Pisang',
                    'postal_code' => '40112',
                ]
            ],
            [
                'customer_code' => 'CUST007',
                'name' => 'Toko Furniture Modern',
                'location' => [
                    'address' => 'Jl. Hayam Wuruk No. 167',
                    'province' => 'DKI Jakarta',
                    'cities' => 'Jakarta Barat',
                    'district' => 'Grogol Petamburan',
                    'sub_district' => 'Tanjung Duren Utara',
                    'postal_code' => '11470',
                ]
            ],
            [
                'customer_code' => 'CUST008',
                'name' => 'CV Karya Mandiri',
                'location' => [
                    'address' => 'Jl. Diponegoro No. 91',
                    'province' => 'Jawa Tengah',
                    'cities' => 'Solo',
                    'district' => 'Banjarsari',
                    'sub_district' => 'Keprabon',
                    'postal_code' => '57131',
                ]
            ],
            [
                'customer_code' => 'CUST009',
                'name' => 'PT Inovasi Teknologi',
                'location' => [
                    'address' => 'Jl. TB Simatupang No. 321',
                    'province' => 'DKI Jakarta',
                    'cities' => 'Jakarta Selatan',
                    'district' => 'Cilandak',
                    'sub_district' => 'Lebak Bulus',
                    'postal_code' => '12440',
                ]
            ],
            [
                'customer_code' => 'CUST010',
                'name' => 'Toko Komputer Masa Depan',
                'location' => [
                    'address' => 'Jl. Raya Darmo No. 456',
                    'province' => 'Jawa Timur',
                    'cities' => 'Surabaya',
                    'district' => 'Wonokromo',
                    'sub_district' => 'Darmo',
                    'postal_code' => '60241',
                ]
            ],
        ];

        foreach ($customers as $customerData) {
            // Create location first
            $location = Location::create($customerData['location']);

            // Create customer with location_id
            Customer::create([
                'customer_code' => $customerData['customer_code'],
                'name' => $customerData['name'],
                'location_id' => $location->id,
            ]);
        }
    }
}
