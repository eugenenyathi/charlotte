<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Traits\Utils;
use App\Models\Profile;
use App\Models\Student;
use Illuminate\Database\Seeder;
use App\Constants\ProgramConstants;
use App\Constants\StudentConstants;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProfileSeeder extends Seeder
{
    private $part;
    private $programID;
    private $enrolled;
    private $studentType;

    use Utils;

    public function run()
    {

        //get all StudentConstants who don't have a profile
        $students = $this->fetchStudents();

        $counter = 1;

        foreach ($students as $student) {

            //TODO: change back to this setting after primary testing
            // for every 4 conventional student create 1 block
            if ($counter > 4) {
                $this->studentType = StudentConstants::BLOCK_STUDENT;
                $counter = 0;
            } else $this->studentType = StudentConstants::CON_STUDENT;

            //setting values to the properties
            $this->init();

            Profile::create([
                'student_id' => $student->student_id,
                'program_id' => $this->programID,
                'part' => $this->part,
                'student_type' => $this->studentType,
                'enrolled' => $this->enrolled
            ]);

            $counter++;
        }
    }

    private function init()
    {

        /**
         * 1. Allocate a random part/level 
         * 2. Allocate a random program
         * 3. Set the enrollment year
         */

        $this->part = StudentConstants::LEVELS[$this->random(0, count(StudentConstants::LEVELS) - 1)];
        $this->programID = $this->randomProgramID();
        $this->enrolled = $this->enrollmentYear();
    }

    private function enrollmentYear()
    {
        $currentYear = Carbon::now()->year;

        switch ($this->part) {
            case 1.1:
                return $currentYear;
            case 1.2:
                return $currentYear;
            case 2.1:
                return $currentYear - 1;
            case 2.2:
                return $currentYear - 1;
            case 3.1:
                return $currentYear - 2;
            case 3.2:
                return $currentYear - 2;
            case 4.1:
                return $currentYear - 3;
            case 4.2:
                return $currentYear - 3;
        }
    }

    //Students without accounts
    private function fetchStudents()
    {
        $students = Student::select('student_id')->get();
        $hasNoAccount = [];

        foreach ($students as $student) {
            $hasAccount = Profile::where('student_id', $student->student_id)->exists();

            if ($hasAccount) continue;
            else $hasNoAccount[] = $student;
        }

        return $hasNoAccount;
    }

    private function randomProgramID()
    {
        return ProgramConstants::PROGRAM_IDS[$this->random(0, count(ProgramConstants::PROGRAM_IDS) - 1)];
    }
}
