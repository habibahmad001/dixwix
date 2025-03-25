<?php

namespace Database\Seeders;

use App\Models\Grouptype;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Grouptype::create([
            'name' => 'Lender',
            'description' => 'Group in which each item is being paid against some price'
        ]);

        Grouptype::create([
            'name' => 'Borrower',
            'description' => 'Group in which each item is being paid against some price'
        ]);
    }
}
