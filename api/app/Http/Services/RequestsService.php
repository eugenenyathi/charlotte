<?php

namespace App\Http\Services;

use App\Traits\Utils;
use App\Traits\VUtils;
use App\Models\Requests;
use App\Models\Requester;
use App\Models\Residence;
use App\Models\GirlsHostel;
use App\Models\RequestCandidate;
use Illuminate\Support\Facades\DB;
use App\Constants\SelectionResponse;
use App\Models\Payment;

class RequestsService
{
    use Utils;
    use VUtils;

    public $activeStudentType;

    public function __construct()
    {
        $this->activeStudentType = $this->getActiveStudentType();
    }

    public function getRegistrationStatus($studentID)
    {
        $registration = Payment::select('registered')->where('student_id', $studentID)->first();
        return $registration->registered;
    }

    public function createRequester($studentID, $gender)
    {
        Requester::create([
            'student_id' => $studentID,
            'student_type' => $this->activeStudentType,
            'gender' => $gender
        ]);
    }

    public function createGeneralRequest($studentID)
    {
        Requests::create([
            'student_id' => $studentID,
            'student_type' => $this->activeStudentType,
        ]);
    }

    public function createRequestCandidate($requester, $selectedRoommateID, $requesterGender)
    {
        RequestCandidate::create([
            'requester_id' => $requester,
            'selected_roommate' => $selectedRoommateID,
            'student_type' => $this->activeStudentType,
            'gender' => $requesterGender
        ]);
    }

    public function hasStudentBeenAllocatedARoom($studentID)
    {
        return Requests::where('student_id', $studentID)
            ->where('processed', 'Yes')->exists();
    }

    public function getStudentRoom($studentID)
    {
        return Residence::select(['hostel', 'room'])->where('student_id', $studentID)->first();
    }

    public function getStudentRoommates($studentID, $room)
    {
        return DB::table('residence')
            ->where('residence.room', $room)
            ->whereNot('residence.student_id', $studentID)
            ->join('students', 'residence.student_id', '=', 'students.student_id')
            ->select('students.student_id', 'students.fullName')
            ->get();
    }

    //TODO: function needs to be revisited
    public function haveStudentTypeRoomsBeenAllocated($roomOccupationType)
    {
        return GirlsHostel::where($roomOccupationType, 'Yes')->exists();
    }

    public function isStudentARoomRequester($studentID)
    {
        return Requester::where('student_id', $studentID)->exists();
    }

    public function hasStudentBeenSelectedAsRoommate($studentID)
    {
        return RequestCandidate::where('selected_roommate', $studentID)->exists();
    }

    public function getRequestersWhoSelectedTheRoommate($selectedRoommateID)
    {
        return RequestCandidate::select('requester_id')
            ->where('selected_roommate', $selectedRoommateID)
            ->where('selection_confirmed', SelectionResponse::WAITING)
            ->get();
    }

    public function getRequesterWhoSelectedTheRoommate($selectedRoommateID)
    {
        $requester = RequestCandidate::select('requester_id')
            ->where('selected_roommate', $selectedRoommateID)->first();

        return $requester->requester_id;
    }

    public function getSelectedRoommateResponse($selectedRoommateID)
    {
        return RequestCandidate::select('selection_confirmed')
            ->where('selected_roommate', $selectedRoommateID)->first();
    }

    public function updateSelectedRoommateSelectionByRequesterID($requesterID, $selectedRoommateID, $confirmationStatus)
    {
        RequestCandidate::where('requester_id', $requesterID)
            ->where('selected_roommate', $selectedRoommateID)
            ->update(['selection_confirmed' => $confirmationStatus]);
    }

    public function updateSelectedRoommateSelectionWhereNotRequester($requesterID, $selectedRoommateID, $confirmationStatus)
    {
        RequestCandidate::whereNot('requester_id', $requesterID)
            ->where('selected_roommate', $selectedRoommateID)
            ->update(['selection_confirmed' => $confirmationStatus]);
    }

    public function updateSelectedRoommateSelectionConfirmationStatus($studentID, $confirmationStatus)
    {
        RequestCandidate::where('selected_roommate', $studentID)
            ->update(['selection_confirmed' => $confirmationStatus]);
    }

    public function getRequesterSelectedRoommates($requesterID)
    {
        return RequestCandidate::select('selected_roommate')
            ->where('requester_id', $requesterID)
            ->get()
            ->pluck('selected_roommate');
    }

    public function getRequesterNewlyCreatedRequestCandidates($requesterID)
    {
        return RequestCandidate::select(['selected_roommate', 'selection_confirmed'])
            ->where('requester_id', $requesterID)
            ->get();
    }
    public function getSelectedNewlyCreatedRequestCandidates($requesterID, $selectedRoommateID)
    {
        return RequestCandidate::select(['selected_roommate', 'selection_confirmed'])
            ->where('requester_id', $requesterID)
            ->whereNot('selected_roommate', $selectedRoommateID)
            ->get();
    }

    public function candidateIsARoommateForRequester($requesterID, $roommateID)
    {
        return RequestCandidate::where('requester_id', $requesterID)
            ->where('selected_roommate', $roommateID)
            ->exists();
    }

    public function doesCandidateHaveAConfirmedSelection($selectedRoommateID)
    {
        return RequestCandidate::where('selection_confirmed', SelectionResponse::YES)
            ->where('selected_roommate', $selectedRoommateID)->exists();
    }

    public function candidateHasAConfirmedSelection($requesterID, $roommateID)
    {
        return RequestCandidate::where('selection_confirmed', SelectionResponse::YES)
            ->where('requester_id', $requesterID)
            ->where('selected_roommate', $roommateID)
            ->exists();
    }

    public function candidateHasAConfirmedSelectionWhereNotRequester($requesterID, $roommateID)
    {
        return RequestCandidate::where('selection_confirmed', SelectionResponse::YES)
            ->whereNot('requester_id', $requesterID)
            ->where('selected_roommate', $roommateID)
            ->exists();
    }

    public function deleteRequestCandidate($requesterID, $studentID)
    {
        RequestCandidate::where('requester_id', $requesterID)
            ->where('selected_roommate', $studentID)
            ->delete();
    }

    public function destroyRequest($studentID)
    {
        Requester::where('student_id', $studentID)->delete();
        RequestCandidate::where('requester_id', $studentID)->delete();
        Requests::where('student_id', $studentID)->delete();
    }
}
