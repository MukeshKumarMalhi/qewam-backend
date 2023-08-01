<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('invoices')->insert([
            [
                'customer_id' => 1,
                'start_date' => '2023-07-24',
                'end_date' => '2023-07-27',
                'created_at' => '2023-07-27',
                'updated_at' => '2023-07-27'
            ],
            [
                'customer_id' => 1,
                'start_date' => '2023-07-24',
                'end_date' => '2023-07-28',
                'created_at' => '2023-07-28',
                'updated_at' => '2023-07-28'
            ]
        ]);
    }
}
