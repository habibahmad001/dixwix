<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            SettingsTableSeeder::class,
            CountryTableSeeder::class,
            GroupTypeTableSeeder::class,
            UserTableSeeder::class,
            MembershipPlanTableSeeder::class,
            CategoryTableSeeder::class,
            LoanRuleTableSeeder::class
        ]);
    }
}
