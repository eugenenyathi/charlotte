<?php

namespace App\Http\Services;

use App\Models\Profile;
use Illuminate\Support\Facades\DB;
use App\Constants\FacultyConstants;


class UtilsService
{
    public function getStudentIdsByStudentType($studentType)
    {
        return DB::table('students')
            ->join('profile', 'students.student_id', '=', 'profile.student_id')
            ->whereNotIn('profile.part', [3.1, 3.2])
            ->where('profile.student_type', $studentType)
            ->get();
    }

    public function getStudentInformation($studentID)
    {
        return DB::table("students")
            ->where('students.student_id', $studentID)
            ->join("profile", "students.student_id", '=', "profile.student_id")
            ->join("programs", "profile.program_id", '=', "programs.program_id")
            ->select('students.student_id', 'students.fullName', 'students.national_id', 'students.gender', "programs.program")
            ->first();
    }

    public function getStudentProfile($studentID)
    {
        return DB::table('students')
            ->join('profile', 'students.student_id', '=', 'profile.student_id')
            ->join("programs", "profile.program_id", '=', "programs.program_id")
            ->join("faculties", "programs.faculty_id", '=', "faculties.faculty_id")
            ->select(
                'students.student_id',
                'students.fullName',
                'students.gender',
                'profile.student_type',
                'faculties.faculty',
                'programs.program',
                'profile.part',
                'profile.enrolled'
            )
            ->where('students.student_id', $studentID)
            ->first();
    }

    public function countStudentType($studentType)
    {
        return Profile::where('student_type', $studentType)->get()->count();
    }

    public function studentSpread($studentType)
    {
        return DB::table('profile')
            ->join('programs', 'profile.program_id', '=', 'programs.program_id')
            ->where('profile.student_type', $studentType)
            ->whereNot('programs.faculty_id', FacultyConstants::COMMERCE_FACULTY['faculty_id'])
            ->get()
            ->count();
    }
}
