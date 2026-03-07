<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WeightValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('weight_value')->truncate();
        DB::table('weight_value')->insert([
            [
                'gap' => 0,
                'weight' => 5,
            ],
            [
                'gap' => 1,
                'weight' => 4.5,
            ],
            [
                'gap' => -1,
                'weight' => 4,
            ],
            [
                'gap' => 2,
                'weight' => 3.5,
            ],
            [
                'gap' => -2,
                'weight' => 3,
            ],
            [
                'gap' => 3,
                'weight' => 2.5,
            ],
            [
                'gap' => -3,
                'weight' => 2,
            ],
            [
                'gap' => 4,
                'weight' => 1.5,
            ],
            [
                'gap' => -4,
                'weight' => 1,
            ],
        ]);
    }
}
