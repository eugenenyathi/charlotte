<?php

namespace App\Http\Services;

use App\Traits\Utils;
use App\Traits\VUtils;
use App\Models\Requests;
use App\Models\Requester;
use App\Models\RequestCandidate;
use Illuminate\Support\Facades\DB;
use App\Constants\FacultyConstants;
use App\Constants\RequestProcessed;
use App\Constants\StudentConstants;
use App\Constants\SelectionResponse;
use App\Constants\SearchConstants;
use App\Models\GirlsHostel;

class GenerateRequestsService
{
    use Utils;
    use VUtils;

    private $activeStudentType;

    public function __construct()
    {
        $this->activeStudentType = $this->getActiveStudentType();
    }

    public function genderFirstSameLevelStudents($gender, $level)
    {
        $query = DB::table('students')
            ->where('students.gender', $gender)
            ->join('profile', 'students.student_id', '=', 'profile.student_id')
            ->where('profile.part', $level)
            ->where('profile.student_type', $this->activeStudentType)
            ->join('programs', 'profile.program_id', '=', 'programs.program_id')
            ->select('students.student_id')
            ->whereNot('programs.faculty_id', FacultyConstants::COMMERCE_FACULTY['faculty_id'])
            ->get();


        if ($level === 3.1 || $level === 3.2) return $this->filterByProgram($query);
        else return $query;
    }

    public function filterByProgram($students)
    {
        $programExceptions = SearchConstants::EXCEPTIONS;

        return $students->filter(function ($student) use ($programExceptions) {
            $studentProgramID = $this->programID($student->student_id);
            return in_array($studentProgramID, $programExceptions);
        });
    }

    public function freeMates($students)
    {

        //Check if the student exists as a requester
        //or selected roommate in the request_candidates table
        $freeMates = [];

        foreach ($students as $student) {

            $studentIsARequester = $this->requesterEntryExists($student->student_id);

            if (!$studentIsARequester) {
                $studentIsASelectedRoommate = RequestCandidate::where('selected_roommate', $student->student_id)->exists();

                switch ($studentIsASelectedRoommate) {
                    case true:
                        $studentAcceptedSelection = RequestCandidate::where('selected_roommate', $student->student_id)
                            ->where('selection_confirmed', SelectionResponse::YES)
                            ->exists();

                        if (!$studentAcceptedSelection) $freeMates[] = $student;
                        break;
                    case false:
                        $freeMates[] = $student;
                        break;
                }
            }
        }

        return $freeMates;
    }

    public function isFree($student)
    {
        if ($this->requesterEntryExists($student->student_id)) return false;

        $studentIsASelectedRoommate = RequestCandidate::where('selected_roommate', $student->student_id)
            ->where('selection_confirmed', SelectionResponse::YES)->exists();

        if ($studentIsASelectedRoommate) return false;

        return true;
    }

    public function createRequester($studentID, $gender)
    {
        Requester::create([
            'student_id' => $studentID,
            'student_type' => $this->activeStudentType,
            'gender' => $gender
        ]);
    }

    public function addToGeneralRequests($studentID)
    {
        if ($this->generalRequestExists($studentID)) return;

        Requests::create([
            'student_id' => $studentID,
            'student_type' => $this->activeStudentType,
        ]);
    }

    public function addRequestCandidates($roommate)
    {
        RequestCandidate::create($roommate);
    }

    public function requesterEntryExists($studentID)
    {
        return Requester::where('student_id', $studentID)->exists();
    }

    public function generalRequestExists($studentID)
    {
        return Requests::where('student_id', $studentID)->exists();
    }

    public function requestProcessedStatus($filter)
    {
        $query = Requests::where('student_type', $this->activeStudentType)
            ->where('processed', $filter)->count();
        return $query;
    }

    public function getStudentWithUnprocessedRequests()
    {
        $query = Requests::select('student_id')
            ->where('student_type', $this->activeStudentType)
            ->where('processed', RequestProcessed::NO)->get();

        return $query;
    }

    public function setOtherSelectionsToNo($requesterId, $selectedRoommateId)
    {
        if (!$this->otherRequestersWhoSelectedRoommateExist($requesterId, $selectedRoommateId)) return;

        RequestCandidate::where('selected_roommate', $selectedRoommateId)->whereNot('requester_id', $requesterId)
            ->update(['selection_confirmed' => SelectionResponse::NO]);
    }

    public function otherRequestersWhoSelectedRoommateExist($requesterId, $selectedRoommateId)
    {
        return RequestCandidate::where('selected_roommate', $selectedRoommateId)->whereNot('requester_id', $requesterId)->exists();
    }

    public function duplicateRequestCandidates()
    {
        DB::statement('DROP table IF EXISTS rq');
        DB::statement('CREATE TABLE rq AS SELECT * FROM request_candidates;');
    }

    /// ============ MISCELLANEOUS ==================
    public function clearAll()
    {
        DB::statement("DELETE FROM students");
        DB::statement("DELETE FROM profile");
        DB::statement("DELETE FROM users");
        DB::statement("DELETE FROM user_login_timestamps");
        DB::statement("DELETE FROM payments");
        DB::statement("DELETE FROM residence");
        DB::statement("DELETE FROM old_residence");

        DB::statement("DELETE FROM requesters");
        DB::statement("DELETE FROM requests");
        DB::statement("DELETE FROM request_candidates");

        DB::statement("UPDATE girls_hostel SET con_occupied = 'No' ");
        DB::statement("UPDATE boys_hostel SET con_occupied = 'No' ");

        DB::statement("UPDATE girls_hostel SET block_occupied = 'No' ");
        DB::statement("UPDATE boys_hostel SET block_occupied = 'No' ");
    }

    public function reverseProcessedRequests()
    {

        DB::statement("UPDATE requesters SET processed = 'No' ");
        DB::statement("UPDATE requests SET processed = 'No' ");
    }

    public function clearRooms()
    {

        DB::statement("UPDATE girls_hostel SET con_occupied = 'No' ");
        DB::statement("UPDATE boys_hostel SET con_occupied = 'No' ");

        DB::statement("UPDATE girls_hostel SET block_occupied = 'No' ");
        DB::statement("UPDATE boys_hostel SET block_occupied = 'No' ");

        DB::statement("DELETE FROM residence");
    }

    public function destroyAll()
    {
        DB::statement('DELETE FROM requesters');
        DB::statement('DELETE FROM requests');
        DB::statement('DELETE FROM request_candidates');
        DB::statement('DROP table IF EXISTS rq');
        DB::statement('DELETE FROM residence');
        DB::statement('UPDATE girls_hostel SET con_occupied = "NO" ');
        DB::statement('UPDATE boys_hostel SET con_occupied = "NO" ');
        // DB::statement('DELETE FROM boys_hostel');

    }
}
