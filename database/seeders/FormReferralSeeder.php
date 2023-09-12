<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormReferralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $now = Carbon::now();
        $csv = new CsvtoArray();
        $file = __DIR__.'/../../resources/csv/form_referral.csv';
        $header = ['label','value','tipe','mandatory','min_lenght','max_lenght','form','display_order'];
        $data = $csv->csv_to_array($file, $header);
        $data = array_map(function ($arr) use ($now) {
            return $arr + ['created_at' => $now, 'updated_at' => $now];
        }, $data);

        DB::table('form_referrals')->insertOrIgnore($data);
    }
}
