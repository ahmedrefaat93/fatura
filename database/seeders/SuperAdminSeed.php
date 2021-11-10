<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class SuperAdminSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin1234'),
            'is_super_admin'=>1
        ]);

    }
}
