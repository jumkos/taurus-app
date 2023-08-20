<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $now = Carbon::now();
        $csv = new CsvtoArray();
        $file = __DIR__.'/../../resources/csv/branch.csv';
        $header = ['regions_name','name'];
        $data = $csv->csv_to_array($file, $header);
        $data = array_map(function ($arr) use ($now) {
            $arr['regions_id'] = DB::table('regions')->select('id')->where('name',$arr['regions_name'])->value('id');
            unset($arr['regions_name']);
            return $arr + ['created_at' => $now, 'updated_at' => $now];
        }, $data);

        DB::table('branches')->insertOrIgnore($data);
    }
}
