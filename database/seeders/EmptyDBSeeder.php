<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmptyDBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('alternatives')->truncate();
        DB::table('criteria_alternative')->truncate();
        DB::table('criterias')->truncate();
        DB::table('sub_criteria')->truncate();
        DB::table('users')->truncate();
        DB::table('weight_value')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
