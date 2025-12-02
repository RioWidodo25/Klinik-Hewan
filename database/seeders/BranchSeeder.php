<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Klinik Hewan Cabang Jakarta Pusat',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta 10220',
                'phone' => '021-5551234',
                'email' => 'jakpus@klinikhewan.com',
                'operational_hours' => "Senin - Jumat: 08.00 - 20.00\nSabtu: 08.00 - 17.00\nMinggu: 09.00 - 15.00",
                'latitude' => -6.208763,
                'longitude' => 106.845599,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Klinik Hewan Cabang Bekasi',
                'address' => 'Jl. Ahmad Yani No. 456, Bekasi Selatan, Kota Bekasi 17141',
                'phone' => '021-8881234',
                'email' => 'bekasi@klinikhewan.com',
                'operational_hours' => "Senin - Jumat: 09.00 - 19.00\nSabtu: 09.00 - 16.00\nMinggu: Tutup",
                'latitude' => -6.243086,
                'longitude' => 106.992416,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Klinik Hewan Cabang Tangerang',
                'address' => 'Jl. BSD Raya No. 789, BSD City, Tangerang Selatan 15321',
                'phone' => '021-7771234',
                'email' => 'tangerang@klinikhewan.com',
                'operational_hours' => "Senin - Minggu: 08.00 - 20.00",
                'latitude' => -6.301366,
                'longitude' => 106.664123,
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
