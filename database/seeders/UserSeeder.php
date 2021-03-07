<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
            [
                'name' => 'Javier Troya',
                'email' => 'javo@troya.co',
                'email_verified_at' => now(),
                'password' => bcrypt('123'),
                'created_at' => now(),
            ]
        );
    }
}
