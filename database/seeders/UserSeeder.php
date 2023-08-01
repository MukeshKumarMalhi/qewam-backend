<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'customer_id' => 1,
                'name' => 'User 1',
                'email' => 'user1@mail.com',
                'password' => 12345678,
                'created_at' => '2023-07-21',
                'updated_at' => '2023-07-21'
            ],
            [
                'customer_id' => 1,
                'name' => 'User 2',
                'email' => 'user2@mail.com',
                'password' => 12345678,
                'created_at' => '2023-07-21',
                'updated_at' => '2023-07-21'
            ],
            [
                'customer_id' => 1,
                'name' => 'User 3',
                'email' => 'user3@mail.com',
                'password' => 12345678,
                'created_at' => '2023-07-26',
                'updated_at' => '2023-07-26',
            ],
            [
                'customer_id' => 1,
                'name' => 'User 4',
                'email' => 'user4@mail.com',
                'password' => 12345678,
                'created_at' => '2023-07-21',
                'updated_at' => '2023-07-21',
            ]
        ]);
    }
}
