<?php

namespace Database\Seeders;

use App\Models\Tuition;
use App\Constants\TuitionConstants;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TuitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (TuitionConstants::TUITION as $tuition) {
            Tuition::create($tuition);
        }
    }
}
