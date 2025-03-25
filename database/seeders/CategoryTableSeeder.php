<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategoryTableSeeder extends Seeder
{
    public function run(): void
    {
        Type::create([
            'name' => 'Book',
            'percentage' => 10,
            'description' => 'Book Category',
            'created_by' => 1,
        ]);

        Type::create([
            'name' => 'CD',
            'percentage' => 15,
            'description' => 'CD Category',
            'created_by' => 1,
        ]);

        Type::create([
            'name' => 'Power Tools',
            'percentage' => 20,
            'description' => 'Power Tools',
            'created_by' => 1,
        ]);
    }
}
