<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $now = Carbon::now();
        $csv = new CsvtoArray();
        $file = __DIR__.'/../../resources/csv/employee.csv';
        $header = ['nip','hak_akses','email','name','division','province','city'];
        $data = $csv->csv_to_array($file, $header);
        $data = array_map(function ($arr) use ($now) {
            $arr['division_id'] = DB::table('divisions')->select('id')->where('name',$arr['division'])->value('id');
            unset($arr['division']);
            $arr['province_id'] = DB::table('regions')->select('id')->where('name',$arr['province'])->value('id');
            unset($arr['province']);
            $arr['city_id'] = DB::table('cities')->select('id')->where('name',$arr['city'])->value('id');
            unset($arr['city']);
            return $arr + ['created_at' => $now, 'updated_at' => $now];
        }, $data);

        DB::table('employees')->insertOrIgnore($data);
    }
}
