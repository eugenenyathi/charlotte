<?php

namespace Database\Seeders;

use App\Constants\FacultyConstants;
use Illuminate\Database\Seeder;
use App\Models\Faculty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FacultiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (FacultyConstants::FACULTIES as $faculty) {
            Faculty::create([
                'faculty_id' => $faculty['faculty_id'],
                'faculty' => $faculty['faculty_name']
            ]);
        }
    }
}
