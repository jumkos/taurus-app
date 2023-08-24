<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $now = Carbon::now();
        $csv = new CsvtoArray();
        $file = __DIR__.'/../../resources/csv/product_category.csv';
        $header = ['product_types_name','name'];
        $data = $csv->csv_to_array($file, $header);
        $data = array_map(function ($arr) use ($now) {
            $arr['product_types_id'] = DB::table('product_types')->select('id')->where('name',$arr['product_types_name'])->value('id');
            unset($arr['product_types_name']);
            return $arr + ['created_at' => $now, 'updated_at' => $now];
        }, $data);

        DB::table('product_categories')->insertOrIgnore($data);
    }
}
