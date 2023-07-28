<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sessions')->insert([
            [
                'user_id' => 1,
                'registered' => '2023-07-25',
                'activated' => null,
                'appointment' => null,
                'price' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'registered' => null,
                'activated' => '2023-07-26',
                'appointment' => null,
                'price' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'registered' => null,
                'activated' => null,
                'appointment' => '2023-07-27',
                'price' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
