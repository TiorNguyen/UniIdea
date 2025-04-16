<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name'       => 'Admin 1',
                'email'      => 'admin1@gmail.com',
                'password'   => Hash::make('admin123'),
                'address'    => 'Địa chỉ Admin 1',
                'gender'     => 'male',
                'date_of_birth' => '1990-01-01',
                'phone'      => '0123456789',
                'avatar'     => null,
                'description'=> 'Mô tả Admin 1',
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Admin 2',
                'email'      => 'admin2@gmail.com',
                'password'   => Hash::make('admin123'),
                'address'    => 'Địa chỉ Admin 2',
                'gender'     => 'female',
                'date_of_birth' => '1992-02-02',
                'phone'      => '0987654321',
                'avatar'     => null,
                'description'=> 'Mô tả Admin 2',
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
