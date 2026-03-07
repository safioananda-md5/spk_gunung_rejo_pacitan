<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\EmptyDBSeeder;
use Database\Seeders\WeightValueSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            EmptyDBSeeder::class,
            UserSeeder::class,
            CriteriaSeeder::class,
            WeightValueSeeder::class,
        ]);
    }
}
