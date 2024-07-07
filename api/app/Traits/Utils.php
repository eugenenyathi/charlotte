<?php

namespace App\Traits;

use App\Models\Profile;
use App\Models\Student;
use App\Models\Tuition;
use App\Models\CheckInOut;
use App\Models\LoginTimestamps;
use App\Models\SearchException;
use Illuminate\Support\Facades\DB;
use App\Constants\FacultyConstants;
use App\Constants\StudentConstants;


trait Utils
{


    protected function faculty($studentID)
    {
        $faculty = DB::table('profile')
            ->where('student_id', $studentID)
            ->join('programs', 'profile.program_id', '=', 'programs.program_id')
            ->join('faculties', 'programs.faculty_id', '=', 'faculties.faculty_id')
            ->select('faculties.faculty')
            ->first();


        return $faculty->faculty;
    }

    protected function facultyID($studentID)
    {
        $faculty = DB::table('profile')
            ->where('student_id', $studentID)
            ->join('programs', 'profile.program_id', '=', 'programs.program_id')
            ->select('programs.faculty_id')
            ->first();


        return $faculty->faculty_id;
    }

    protected function facultyTuition($studentID)
    {
        //get what type of student is it
        $studentType = $this->studentType($studentID);

        if ($studentType === StudentConstants::CON_STUDENT) $tuitionColumn = 'con_amount';
        else $tuitionColumn = 'block_amount';

        $tuition = DB::table('profile')
            ->where('profile.student_id', $studentID)
            ->join('programs', 'profile.program_id', '=', 'programs.program_id')
            ->join('tuition', 'programs.faculty_id', '=', 'tuition.faculty_id')
            ->select($tuitionColumn)
            ->first();

        return $tuition->$tuitionColumn;
        // return $tuition;
    }

    protected function program($studentID)
    {
        $program = DB::table('profile')
            ->where('profile.student_id', $studentID)
            ->join('programs', 'profile.program_id', '=', 'programs.program_id')
            ->select('programs.program')
            ->first();

        return $program->program;
    }

    protected function programID($studentID)
    {
        $program = Profile::select('program_id')->where('student_id', $studentID)->first();

        return $program->program_id;
    }

    protected function programExceptions()
    {
        $programs = SearchException::all();
        $data = [];

        foreach ($programs as $program) {
            $data[] = $program->program_id;
        }

        return $data;
    }


    protected function studentType($studentID)
    {
        $profile = Profile::select('student_type')->where('student_id', $studentID)->first();

        return $profile->student_type;
    }

    protected function timestamp($studentID)
    {
        //get current login timestamp
        $timestamp = LoginTimestamps::select('current_stamp')
            ->where('student_id', $studentID)->first();

        return $timestamp->current_stamp;
    }


    public function hostelFees($studentID)
    {
        $hostel = DB::table('profile')
            ->where('profile.student_id', $studentID)
            ->join('hostel_fees', 'profile.student_type', '=', 'hostel_fees.student_type')
            ->select('hostel_fees.fee')
            ->first();

        return $hostel->fee;
    }


    public function checkInOut($studentID)
    {
        //get the type of a student
        $studentType = $this->studentType($studentID);

        if ($studentType === StudentConstants::CON_STUDENT) $dateColumn = 'con_students_date';
        else $dateColumn = 'block_students_date';

        $checkInOut = CheckInOut::select('type', 'con_students_date', 'block_students_date')->get();

        $data = [
            'checkIn' => $checkInOut[0]->$dateColumn,
            'checkOut' => $checkInOut[1]->$dateColumn,
        ];

        return $data;
    }

    public function part($studentID)
    {
        $profile = Profile::select('part')->where('student_id', $studentID)->first();

        return $profile->part;
    }


    public function previousPart($studentID)
    {
        $levels = [1.1, 1.2, 2.1, 2.2, 4.1, 4.2];
        //fetch student level
        $student = Profile::select('part')->where('student_id', $studentID)->first();
        $indexOfCurrentLevel = array_search($student->part, $levels);
        $previousLevel = $levels[$indexOfCurrentLevel - 1];

        return $previousLevel;
    }

    public function gender($studentID)
    {
        $student = Student::select('gender')->where('student_id', $studentID)->first();
        return $student->gender;
    }

    public function genderFirstLevelStudents($gender, $level)
    {
        $query = DB::table('students')
            ->join('profile', 'students.student_id', '=', 'profile.student_id')
            ->join('programs', 'profile.program_id', '=', 'programs.program_id')
            ->select('students.student_id')
            ->where('students.gender', $gender)
            ->where('profile.part', $level)
            ->whereNot('programs.faculty_id', FacultyConstants::COMMERCE_FACULTY['faculty_id'])
            ->get();

        return $query;
    }

    protected function random($firstIndex, $lastIndex)
    {
        return mt_rand($firstIndex, $lastIndex);
    }

    public function studentProfile($studentID)
    {
        // $student = Profile::where('student_id', $studentID)->first();
        $student = DB::table('students')
            ->join('profile', 'students.student_id', '=', 'profile.student_id')
            ->select([
                'students.fullName', 'students.gender',
                'profile.student_type', 'profile.part', 'profile.enrolled'
            ])
            ->where('students.student_id', $studentID)
            ->first();

        $data = [
            'studentNumber' => $studentID,
            'name' => $student->fullName,
            'gender' => $student->gender,
            'faculty' => $this->faculty($studentID),
            'program' => $this->program($studentID),
            'studentType' => $student->student_type,
            'part' => $student->part,
            'enrolled' => $student->enrolled,
        ];

        // return $this->sendData($data);
        return $data;
    }

    public function getFullName($studentID)
    {
        $student = Student::select('fullName')->where('student_id', $studentID)->first();

        return $student->fullName;
    }
}
