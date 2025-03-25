<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
           [
               'name' => 'standard_load_rule',
               'value' => 'Group standard rule is here',
               'created_at' => now(),
               'updated_at' => now(),
           ]
        ]);
    }
}
