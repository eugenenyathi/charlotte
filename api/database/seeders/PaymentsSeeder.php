<?php

namespace Database\Seeders;

use App\Constants\StudentConstants;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Student;
use App\Models\Tuition;
use App\Traits\FakeCredentials;
use App\Traits\Utils;
use App\Traits\VUtils;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    use Utils;
    use VUtils;

    public function run()
    {
        //get all students
        $students = $this->fetchStudents();
        $activeStudentType = $this->getActiveStudentType();

        // dd(count($students));

        foreach ($students as $student) {

            $amountCleared = $this->amountCleared($student->student_id);
            // $this->registeredStatus($student->student_id, $activeStudentType, $amountCleared)
            Payment::create([
                'student_id' => $student->student_id,
                'amount_cleared' => $amountCleared,
                'registered' => $this->registeredStatus($student->student_id, $activeStudentType, $amountCleared),
            ]);
        }
    }

    private function registeredStatus($studentID, $activeStudentType, $amountCleared)
    {
        $studentFaculty = $this->facultyID($studentID);
        $facultyTuition = Tuition::where('faculty_id', $studentFaculty)->first();

        switch ($activeStudentType) {
            case StudentConstants::CON_STUDENT:
                $minimumRequiredTuition = $facultyTuition->con_amount * 0.60;
                return $amountCleared >= $minimumRequiredTuition ? 1 : 0;
                break;
            case StudentConstants::BLOCK_STUDENT:
                $minimumRequiredTuition = $facultyTuition->block * 0.60;
                return $amountCleared >= $minimumRequiredTuition ? 1 : 0;
                break;
        }
    }

    private function amountCleared($studentID)
    {
        $studentType = $this->studentType($studentID);

        switch ($studentType) {
            case StudentConstants::CON_STUDENT:
                return $this->random(400, 650);
            case StudentConstants::BLOCK_STUDENT:
                return $this->random(180, 400);
        }
    }

    //students without accounts
    private function fetchStudents()
    {
        $students = Student::select('student_id')->get();

        return $students->filter(function ($student) {
            $hasAccount = Payment::where('student_id', $student->student_id)->exists();
            return !$hasAccount;
        });
    }
}
