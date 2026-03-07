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
                'name' => "Sekretaris Desa Gunung Rejo",
                'email' => 'sekretaris@gunungrejo.go.id',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('sek123'),
                'role' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => "Kepala Desa Gunung Rejo",
                'email' => 'kades@gunungrejo.go.id',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('kades123'),
                'role' => 'kades',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
