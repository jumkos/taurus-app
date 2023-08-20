<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //ProductType
        \App\Models\ProductType::create([
            'name' => 'Funding',
        ]);
        \App\Models\ProductType::create([
            'name' => 'Lending',
        ]);
        \App\Models\ProductType::create([
            'name' => 'Investment',
        ]);
        \App\Models\ProductType::create([
            'name' => 'Insurance',
        ]);
        \App\Models\ProductType::create([
            'name' => 'Cash Management',
        ]);
        \App\Models\ProductType::create([
            'name' => 'Trade Finance',
        ]);
        \App\Models\ProductType::create([
            'name' => 'Treasury',
        ]);
        \App\Models\ProductType::create([
            'name' => 'Syariah',
        ]);

        //ProductCategory
        //1
        \App\Models\ProductCategory::create([
            'name' => 'Tabungan',
            'product_types_id' => 1,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Giro',
            'product_types_id' => 1,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Deposito',
            'product_types_id' => 1,
        ]);

        //2
        \App\Models\ProductCategory::create([
            'name' => 'Kartu Kredit',
            'product_types_id' => 2,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Personal Loan',
            'product_types_id' => 2,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Kredit Kepemilikan Motor/ Mobil',
            'product_types_id' => 2,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Kredit Kepemilikan Rumah',
            'product_types_id' => 2,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Financial Institution',
            'product_types_id' => 2,
        ]);

        //3
        \App\Models\ProductCategory::create([
            'name' => 'Reksa Dana',
            'product_types_id' => 3,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'DRIP',
            'product_types_id' => 3,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Obligasi',
            'product_types_id' => 3,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Structured Product',
            'product_types_id' => 3,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Valuta Asing',
            'product_types_id' => 3,
        ]);

        //4
        \App\Models\ProductCategory::create([
            'name' => 'Asuransi Jiwa Tradisional',
            'product_types_id' => 4,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Asuransi Jiwa Unit Link',
            'product_types_id' => 4,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Asuransi Jiwa Dwiguna',
            'product_types_id' => 4,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Asuransi Kesehatan',
            'product_types_id' => 4,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Asuransi General',
            'product_types_id' => 4,
        ]);

        //5
        \App\Models\ProductCategory::create([
            'name' => 'Account Service',
            'product_types_id' => 5,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Collection Service',
            'product_types_id' => 5,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Payment Service',
            'product_types_id' => 5,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Liquidity Management',
            'product_types_id' => 5,
        ]);

        //7
        \App\Models\ProductCategory::create([
            'name' => 'Foreign Exchange',
            'product_types_id' => 7,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Derivative',
            'product_types_id' => 7,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Debt Securities',
            'product_types_id' => 7,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Structured Product',
            'product_types_id' => 7,
        ]);

        //8
        \App\Models\ProductCategory::create([
            'name' => 'Tabungan Syariah',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Deposito Syariah',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Giro BISA IB',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'KTU Ruko Syariah',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Leasing Syariah IB',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Proteksi Prima Amanah',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Pembiyaan Kepemilikan Rumah Syariah IB',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Pembiyaan Modal Kerja Syariah IB',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Pembiyaan Investasi Syariah',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Pembiyaan BPR Syariah',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Pembiyaan Koperasi Karyawan Syariah',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Pembiyaan Trade Finance IB',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Qanun Lembaga Keuangan Syariah Aceh',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Layanan Pembayaran Setoran Haji Khusus',
            'product_types_id' => 8,
        ]);
        \App\Models\ProductCategory::create([
            'name' => 'Nisbah Syariah',
            'product_types_id' => 8,
        ]);
    }
}
