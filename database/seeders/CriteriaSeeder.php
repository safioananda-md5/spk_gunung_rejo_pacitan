<?php

namespace Database\Seeders;

use App\Models\Criteria;
use App\Models\SubCriteria;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('sub_criteria')->truncate();
        DB::table('criterias')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $criteria = Criteria::create([
            'name' => 'Pendapatan Orang Tua',
            'category' => 'core',
            'weight' => 30,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $subcriteria = SubCriteria::insert(
            [
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 1,
                    'upper_value' => 3000000,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 2,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => 2000000,
                    'final_value' => 3000000,
                    'sameas_value' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 3,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => 1000000,
                    'final_value' => 2000000,
                    'sameas_value' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 4,
                    'upper_value' => null,
                    'under_value' => 1000000,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]
        );

        $criteria = Criteria::create([
            'name' => 'Kondisi Orang Tua',
            'category' => 'core',
            'weight' => 30,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $subcriteria = SubCriteria::insert(
            [
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 1,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Orang Tua Lengkap',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 2,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Piatu',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 3,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Yatim',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 4,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Yatim Piatu',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]
        );

        $criteria = Criteria::create([
            'name' => 'Status Tempat Tinggal',
            'category' => 'secondary',
            'weight' => 15,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $subcriteria = SubCriteria::insert(
            [
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 1,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Rumah Milik Sendiri',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 2,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Menumpang',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 3,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Kontrakan',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 4,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Kos',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 5,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Bangunan Liar',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]
        );

        $criteria = Criteria::create([
            'name' => 'Mengalami Bencana',
            'category' => 'secondary',
            'weight' => 10,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $subcriteria = SubCriteria::insert(
            [
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 1,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Tidak Mengalami Bencana',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 5,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Ya Mengalami Bencana',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]
        );

        $criteria = Criteria::create([
            'name' => 'Kondisi Sosial-Ekonomi',
            'category' => 'secondary',
            'weight' => 15,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $subcriteria = SubCriteria::insert(
            [
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 1,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Tidak Ada',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 2,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Memiliki Lebih Dari 3 Saudara',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 3,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Keluarga Terpidana',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 4,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Mengalami Kelainan Fisik',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'criteria_id' => $criteria->id,
                    'scale' => 5,
                    'upper_value' => null,
                    'under_value' => null,
                    'initial_value' => null,
                    'final_value' => null,
                    'sameas_value' => 'Orang Tua Terkena PHK',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]
        );
    }
}
