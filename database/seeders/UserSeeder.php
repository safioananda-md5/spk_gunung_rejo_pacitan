<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->truncate();

        DB::table('users')->insert([
            [
                'name' => "Admin SMK Muhammadiyah 1 Taman",
                'email' => 'admin@smkmuh1taman.sch.id',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('Admin_123'),
                'role' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => "User SMK Muhammadiyah 1 Taman",
                'email' => 'user@smkmuh1taman.sch.id',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('User_123'),
                'role' => 'user',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
