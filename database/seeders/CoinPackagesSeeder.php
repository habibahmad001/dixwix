<?php

namespace Database\Seeders;

use App\Models\CoinPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoinPackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => '100 Points',
                'coins' => 100,
                'price' => 0.9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '250 Points',
                'coins' => 250,
                'price' => 1.9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '500 Points',
                'coins' => 500,
                'price' => 3.9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($packages as $package) {
            CoinPackage::create($package);
        }
    }
}
