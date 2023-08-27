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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\StatusParameter::create([
            'name' => 'Assign to Marketing',
            'parrent_id' => 0,
        ]);
        \App\Models\StatusParameter::create([
            'name' => 'Contacted',
            'parrent_id' => 1,
        ]);
        \App\Models\StatusParameter::create([
            'name' => 'Data Collection',
            'parrent_id' => 2,
        ]);
        \App\Models\StatusParameter::create([
            'name' => 'Sent for Approval',
            'parrent_id' => 3,
        ]);
        \App\Models\StatusParameter::create([
            'name' => 'Reject',
            'parrent_id' => 2,
        ]);
        \App\Models\StatusParameter::create([
            'name' => 'Not Approved',
            'parrent_id' => 4,
        ]);
        \App\Models\StatusParameter::create([
            'name' => 'Approve',
            'parrent_id' => 4,
        ]);

        $this->call([
            DivisionSeeder::class,
            RegionSeeder::class,
            BranchSeeder::class,
            ProductTypeSeeder::class,
            ProductCategorySeeder::class,
        ]);
    }
}
