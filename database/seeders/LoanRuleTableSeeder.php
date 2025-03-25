<?php

namespace Database\Seeders;

use App\Models\LoanRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanRuleTableSeeder extends Seeder
{
    public function run(): void
    {
        LoanRule::create([
            'title' => '1 Week',
            'duration' => 7
        ]);
        LoanRule::create([
            'title' => '2 Week',
            'duration' => 14
        ]);
        LoanRule::create([
            'title' => '1 Month',
            'duration' => 30
        ]);
    }
}
