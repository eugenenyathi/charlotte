<?php

namespace App\Http\Services;

use App\Constants\FacultyConstants;
use App\Constants\StudentConstants;
use App\Traits\Utils;
use App\Traits\VUtils;
use App\Models\RequestCandidate;
use Illuminate\Support\Facades\DB;

class SearchService
{
    use Utils;
    use VUtils;

    private $activeStudentType;

    public function __construct()
    {
        $this->activeStudentType = $this->getActiveStudentType();
    }

    public function getStudentDetails($studentID)
    {
        return DB::table('students')
            ->join('profile', 'students.student_id', '=', 'profile.student_id')
            ->join('programs', 'profile.program_id', '=', 'programs.program_id')
            ->join('faculties', 'programs.faculty_id', '=', 'faculties.faculty_id')
            ->select('students.student_id', 'students.gender', 'profile.part', 'faculties.faculty_id')
            ->where('students.student_id', $studentID)
            ->first();
    }

    public function doesStudentHaveARoomRequest($studentID)
    {
        return RequestCandidate::where('requester_id', $studentID)
            ->orWhere('selected_roommate', $studentID)->exists();
    }

    public function isStudentARoomRequester($studentID)
    {
        return RequestCandidate::where('requester_id', $studentID)->exists();
    }

    public function hasStudentConfirmedSelection($studentID)
    {
        return RequestCandidate::where('selection_confirmed', 'Yes')
            ->where('selected_roommate', $studentID)->exists();
    }

    public function searchQuery($student, $searchType, $search_query)
    {
        $searchResults = DB::table('students')
            ->join('profile', 'students.student_id', '=', 'profile.student_id')
            ->join('programs', 'profile.program_id', '=', 'programs.program_id')
            ->join('roommate_preferences', 'students.student_id', '=', 'roommate_preferences.student_id')
            ->join('payments', 'students.student_id', '=', 'payments.student_id')
            ->select(
                'students.student_id',
                'students.fullName',
                'programs.program',
                'roommate_preferences.question_1',
                'roommate_preferences.question_2'
            )
            ->where('students.gender', $student->gender)
            ->where('profile.student_type', $this->activeStudentType)
            ->where('payments.registered', StudentConstants::REGISTERED)
            ->where('students.student_id', 'LIKE', $search_query . '%')
            ->whereNot('students.student_id', $student->student_id)
            ->where(function ($query) use ($student) {
                $query->where('programs.faculty_id', $student->faculty_id)
                    ->orWhere(function ($innerQuery) use ($student) {
                        // Get students from opposite faculty based on $facultyId
                        switch ($student->faculty_id) {
                            case FacultyConstants::ENGINEERING_FACULTY['faculty_id']:
                                $innerQuery->where('programs.faculty_id', FacultyConstants::AGRIC_FACULTY['faculty_id']);
                                break;

                            case FacultyConstants::AGRIC_FACULTY['faculty_id']:
                                $innerQuery->where('programs.faculty_id', FacultyConstants::ENGINEERING_FACULTY['faculty_id']);
                                break;
                            default:
                                $innerQuery->where('programs.faculty_id', FacultyConstants::HUMANITIES_FACULTY['faculty_id']);
                        }
                    });
            })
            ->where(function ($query) use ($student, $searchType) {
                switch ($searchType) {
                    case 'normal':
                        $query->where('profile.part', $student->part);
                        break;
                    case 'special':
                        $query->whereBetween('profile.part', [3.1, 4.2]);
                        break;
                    default:
                        $query->where('profile.part', $student->part);
                }
            })
            ->get();

        return $searchResults;
    }
}
