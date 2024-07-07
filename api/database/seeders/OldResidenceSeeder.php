<?php

namespace Database\Seeders;

use App\Traits\Utils;
use App\Models\Student;
use App\Models\OldResidence;
use Illuminate\Database\Seeder;
use App\Constants\HostelConstants;
use App\Constants\StudentConstants;
use App\Traits\VUtils;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OldResidenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */



    use Utils;
    use VUtils;

    private $student_type;
    private $hostel;
    private $side;
    private $floor;
    private $floorSide;
    private $room;
    private $part;


    public function run()
    {
        //get all students
        $students = $this->fetchStudents();

        foreach ($students as $student) {
            $this->init($student->student_id);

            OldResidence::create([
                'student_id' => $student->student_id,
                'student_type' => $this->student_type,
                'hostel' => $this->hostel,
                'room' => $this->room,
                'part' => $this->part,
            ]);
        }
    }

    private function init($studentID)
    {
        $gender = $this->gender($studentID);
        //student_type
        $this->student_type = $this->studentType($studentID);
        //get hostel
        $this->hostel = $gender == StudentConstants::FEMALE ? HostelConstants::GIRLS_HOSTEL : HostelConstants::BOYS_HOSTEL;
        //get room
        $this->room = $this->room();
        //set part
        $this->part = $this->getPart($studentID);
    }

    //TODO: OLD RESIDENCE FUNC THAT NEEDS ATTENTION
    private function room()
    {
        return $this->random(101, 360);;
    }


    private function getPart($studentID)
    {
        $studentPart = $this->part($studentID);
        $previousLevel = $this->previousLevel($studentPart);

        return $previousLevel;
    }

    private function previousLevel($studentPart)
    {
        switch ($studentPart) {
            case 4.2:
                return 4.1;
            case 4.1:
                return 2.2;
            case 3.2:
                return 2.2;
            case 3.1:
                return 2.2;
            case 2.2:
                return 2.1;
            case 2.1:
                return 1.2;
            case 1.2:
                return 1.1;
        }
    }

    //students without accounts
    private function fetchStudents()
    {
        $students = DB::table('students')
            ->select('students.student_id')
            ->join("profile", "students.student_id", "profile.student_id")
            ->whereNot("profile.part", 1.1)
            ->get();

        return $students->filter(function ($student) {
            return !OldResidence::where('student_id', $student->student_id)->exists();
        });
    }
}
