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
                'activated' => '2023-07-26',
                'appointment' => null,
                'price' => 100,
                'created_at' => '2023-07-26',
                'updated_at' => '2023-07-26',
            ],
            [
                'user_id' => 1,
                'activated' => '2023-07-27',
                'appointment' => null,
                'price' => 100,
                'created_at' => '2023-07-27',
                'updated_at' => '2023-07-27',
            ],
            [
                'user_id' => 1,
                'activated' => null,
                'appointment' => '2023-07-28',
                'price' => 200,
                'created_at' => '2023-07-28',
                'updated_at' => '2023-07-28',
            ],
            [
                'user_id' => 2,
                'activated' => null,
                'appointment' => '2023-07-27',
                'price' => 200,
                'created_at' => '2023-07-27',
                'updated_at' => '2023-07-27',
            ],
            [
                'user_id' => 3,
                'activated' => '2023-07-27',
                'appointment' => null,
                'price' => 100,
                'created_at' => '2023-07-27',
                'updated_at' => '2023-07-27',
            ],
            [
                'user_id' => 4,
                'activated' => '2023-07-21',
                'appointment' => null,
                'price' => 100,
                'created_at' => '2023-07-21',
                'updated_at' => '2023-07-21',
            ],
            [
                'user_id' => 4,
                'activated' => null,
                'appointment' => '2023-07-21',
                'price' => 200,
                'created_at' => '2023-07-21',
                'updated_at' => '2023-07-21',
            ],
            [
                'user_id' => 4,
                'activated' => '2023-07-27',
                'appointment' => null,
                'price' => 100,
                'created_at' => '2023-07-27',
                'updated_at' => '2023-07-27',
            ],
            [
                'user_id' => 4,
                'activated' => null,
                'appointment' => '2023-07-27',
                'price' => 200,
                'created_at' => '2023-07-27',
                'updated_at' => '2023-07-27',
            ],
        ]);
    }
}
