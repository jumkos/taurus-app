<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \App\Models\Division::create([
            'name' => 'Enterprise Banking',
        ]);
        \App\Models\Division::create([
            'name' => 'Transaction Banking',
        ]);
        \App\Models\Division::create([
            'name' => 'Financial Institution',
        ]);
        \App\Models\Division::create([
            'name' => 'SME Banking',
        ]);
        \App\Models\Division::create([
            'name' => 'Consumer - Mortgage',
        ]);
        \App\Models\Division::create([
            'name' => 'Consumer - Personal Loan',
        ]);
        \App\Models\Division::create([
            'name' => 'Consumer - Credit Card',
        ]);
    }
}
