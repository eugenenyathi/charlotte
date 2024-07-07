<?php

namespace Database\Seeders;

use App\Constants\StudentConstants;
use Illuminate\Database\Seeder;
use App\Models\ActiveStudentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ActiveStudentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'student_type' => StudentConstants::CON_STUDENT,
                'active' => StudentConstants::ACTIVE
            ],
            [
                'student_type' => StudentConstants::BLOCK_STUDENT,
                'active' => StudentConstants::INACTIVE
            ]
        ];


        foreach ($data as $input) {
            ActiveStudentType::create($input);
        }
    }
}
