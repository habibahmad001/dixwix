<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MembershipPlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('membership_plans')->insert([
            [
                'name' => 'Basic',
                'allowed_groups' => 25,
                'allowed_items' => 50,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'allowed_groups' => 5,
                'allowed_items' => 500,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
         ]);
    }
}
