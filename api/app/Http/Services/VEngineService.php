<?php

namespace App\Http\Services;

use App\Traits\Utils;
use App\Traits\VUtils;
use App\Models\Requests;
use App\Models\Requester;
use App\Models\Residence;
use App\Models\BoysHostel;
use App\Models\GirlsHostel;
use App\Constants\RoomStatus;
use App\Models\RequestCandidate;
use App\Constants\HostelConstants;
use Illuminate\Support\Facades\DB;
use App\Constants\RequestProcessed;
use App\Constants\RequestStatus;
use App\Constants\StudentConstants;
use App\Constants\SelectionResponse;
use App\DataTransferObjects\CreateResidenceDto;
use App\DataTransferObjects\CreateRequestCandidateDto;

class VEngineService
{
    use Utils;
    use VUtils;

    private $activeStudentType;
    private $roomOccupationType;

    public function __construct()
    {
        $this->activeStudentType = $this->getActiveStudentType();

        switch ($this->activeStudentType) {
            case StudentConstants::CON_STUDENT:
                $this->roomOccupationType = HostelConstants::CON_OCCUPIED;
                break;
            case StudentConstants::BLOCK_STUDENT:
                $this->roomOccupationType = HostelConstants::BLOCK_OCCUPIED;
                break;
        }
    }

    public function genderFirstSameLevelRequesters($gender, $level)
    {
        return DB::table('requesters')
            ->join('profile', 'requesters.student_id', '=', 'profile.student_id')
            ->select('requesters.student_id')
            ->where('requesters.gender', $gender)
            ->where('requesters.processed', RequestProcessed::NO)
            ->where('requesters.student_type', $this->activeStudentType)
            ->where('profile.part', $level)
            ->get();
    }

    public function createRequestCandidate(CreateRequestCandidateDto $dto)
    {
        RequestCandidate::create([
            'requester_id' => $dto->requesterID,
            'selected_roommate' => $dto->selectedRoommate,
            'student_type' => $dto->studentType,
            'gender' => $dto->gender,
            'selection_confirmed' => SelectionResponse::YES
        ]);
    }

    public function getRequesters()
    {
        return Requester::where('processed', RequestProcessed::NO)->get();
    }

    public function onlyRoommateWhoConfirmed($requesterID)
    {
        return RequestCandidate::select('selected_roommate')
            ->where('selection_confirmed', SelectionResponse::YES)
            ->where('requester_id', $requesterID)
            ->first();
    }

    public function updateRequestCandidateSelectionConfirmation($requesterID, $confirmation)
    {
        RequestCandidate::where('requester_id', $requesterID)
            ->update(['selection_confirmed' => $confirmation]);
    }

    public function deleteSelectedRoommates($studentID)
    {
        RequestCandidate::where('requester_id', $studentID)->delete();
    }

    public function deleteAllSelectedRoommatesWithZeroConfirmations($studentID)
    {
        RequestCandidate::where('requester_id', $studentID)
            ->where('selection_confirmed', SelectionResponse::NO)
            ->delete();
    }

    public function updateRequesterProcessedStatus($studentID)
    {
        Requester::where('student_id', $studentID)
            ->update(['processed' => RequestProcessed::YES]);
    }

    public function updateRequestProcessedStatus($studentID)
    {
        Requests::where('student_id', $studentID)->update([
            'processed' => RequestProcessed::YES
        ]);
    }

    public function deleteRequester($studentID)
    {
        Requester::where('student_id', $studentID)->delete();
    }

    public function deleteRoommateWhoDeclined($requesterID)
    {
        RequestCandidate::where('requester_id', $requesterID)
            ->where('selection_confirmed', SelectionResponse::NO)
            ->delete();
    }

    public function getFreeRoom($gender)
    {
        switch ($gender) {
            case StudentConstants::FEMALE:
                $girlsHostel = GirlsHostel::select('room')
                    ->where('usable', RoomStatus::USABLE)
                    ->where($this->roomOccupationType, RoomStatus::NOT_OCCUPIED)
                    ->orderBy('room', 'desc')
                    ->first();

                return $girlsHostel->room;
            case StudentConstants::MALE:
                $boysHostel = BoysHostel::select('room')
                    ->where('usable', RoomStatus::USABLE)
                    ->where($this->roomOccupationType, RoomStatus::NOT_OCCUPIED)
                    ->orderBy('room', 'desc')
                    ->first();

                return $boysHostel->room;
        }
    }

    public function updateRoomOccupationStatus($gender, $room)
    {
        switch ($gender) {
            case StudentConstants::FEMALE:
                GirlsHostel::where('room', $room)
                    ->update([$this->roomOccupationType => 'Yes']);

            case StudentConstants::MALE:
                BoysHostel::where('room', $room)
                    ->update([$this->roomOccupationType  => 'Yes']);
        }
    }

    public function createResidence(CreateResidenceDto $dto)
    {
        Residence::create([
            'student_id' => $dto->studentID,
            'student_type' => $dto->studentType,
            'part' => $dto->part,
            'hostel' => $dto->hostel,
            'room' => $dto->room,
        ]);
    }

    public function selectedRoommatesWithConfirmation($requesterID)
    {
        return RequestCandidate::select('selected_roommate')
            ->where('requester_id', $requesterID)
            ->where('selection_confirmed', SelectionResponse::YES)
            ->get();
    }

    public function isRequesterRequestProcessed($studentID)
    {
        return Requester::where('student_id', $studentID)
            ->where('processed', RequestProcessed::YES)
            ->exists();
    }

    public function isGeneralRequestProcessed($studentID)
    {
        return Requests::where('student_id', $studentID)
            ->where('processed', RequestProcessed::YES)
            ->exists();
    }

    public function selectedMatesHaveRooms($studentID)
    {
        return Residence::where('student_id', $studentID)->exists();
    }
}
