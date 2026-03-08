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
        DB::table('alternatives')->update(['deleted_at' => null]);
        DB::table('alternative_penerimaans')->truncate();
        DB::table('criteria_alternative')->truncate();
        DB::table('penerimaans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
