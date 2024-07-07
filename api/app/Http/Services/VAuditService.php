<?php

namespace App\Http\Services;

use App\Constants\StudentConstants;
use App\Constants\HostelConstants;
use App\Constants\RequestProcessed;
use App\Constants\RoomStatus;
use App\Traits\Utils;
use App\Traits\VUtils;
use App\Models\BoysHostel;
use App\Models\Requests;
use App\Models\GirlsHostel;
use App\Models\Requester;
use App\Models\Residence;
use Illuminate\Support\Facades\DB;


class VAuditService
{
    use Utils;
    use VUtils;

    private $activeStudentType;
    private $roomOccupationType;

    public function __construct()
    {
        $this->setRoomOccupationType();
    }

    public function selectGeneralRequestsByStudentType()
    {
        return Requests::select('student_id')
            ->where('student_type', $this->activeStudentType)
            ->get();
    }

    public function doesStudentHaveARoom($studentID)
    {
        return Residence::where('student_id', $studentID)->exists();
    }

    public function isRequester($studentID)
    {
        return Requester::where('student_id', $studentID)
            ->exists();
    }

    public function updateGeneralRequestProcessedStatus($studentID)
    {
        Requests::where('student_id', $studentID)
            ->update(['processed' => RequestProcessed::YES]);
    }

    public function updateRequesterProcessedStatus($requesterID)
    {
        Requester::where('student_id', $requesterID)
            ->update(['processed' => RequestProcessed::YES]);
    }

    public function isRoomOccupied($gender, $room)
    {
        switch ($gender) {
            case StudentConstants::FEMALE:
                return GirlsHostel::where('room', $room)
                    ->where($this->roomOccupationType, 'Yes')
                    ->exists();
            case StudentConstants::MALE:
                return BoysHostel::where('room', $room)
                    ->where($this->roomOccupationType, 'Yes')
                    ->exists();
        }
    }

    public function updateRoomOccupationStatus($gender, $room)
    {
        switch ($gender) {
            case StudentConstants::FEMALE:
                GirlsHostel::where('room', $room)
                    ->update([$this->roomOccupationType => 'Yes']);
                break;
            case StudentConstants::MALE:
                BoysHostel::where('room', $room)
                    ->update([$this->roomOccupationType => 'Yes']);
                break;
        }
    }

    public function getAllRooms($gender)
    {
        switch ($gender) {
            case StudentConstants::FEMALE:
                return GirlsHostel::select('room')
                    ->where('usable', RoomStatus::USABLE)
                    ->where($this->roomOccupationType, 'No')
                    ->get()
                    ->pluck('room');
                break;
            case StudentConstants::MALE:
                return BoysHostel::select('room')
                    ->where('usable', RoomStatus::USABLE)
                    ->where($this->roomOccupationType, 'No')
                    ->get()
                    ->pluck('room');
                break;
        }
    }

    //TODO: Verify the pluck thingy
    public function getAllResidenceRooms($gender)
    {
        return Residence::select('room')
            ->where('hostel', $this->getHostel($gender))
            ->where('student_type', $this->activeStudentType)
            ->get()
            ->pluck('room');
    }

    public function getNumberOfStudentsInTheRoom($room, $gender)
    {
        return Residence::select(DB::raw('room, count(room) as student_count'))
            ->where('hostel', $this->getHostel($gender))
            ->where('room', $room)
            ->where('student_type', $this->activeStudentType)
            ->groupBy('room')
            ->first();
    }

    public function getHostel($gender)
    {
        return $gender == StudentConstants::FEMALE ? HostelConstants::GIRLS_HOSTEL : HostelConstants::BOYS_HOSTEL;
    }

    public function getFirstStudentFromTheRoom($room)
    {
        return Residence::select('student_id')->where('room', $room)->first();
    }

    public function setRoomOccupationType()
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
}
