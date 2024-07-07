<?php

namespace App\Http\Controllers;

use App\Traits\Utils;
use App\Traits\VUtils;
use App\Constants\HostelConstants;
use App\Traits\HttpResponses;
use App\Http\Services\VAuditService;
use App\Http\Services\VEngineService;
use App\DataTransferObjects\CreateResidenceDto;

class VAuditController extends Controller
{
    /*
    1. Get all requests from the general requests table
    2. Manually check if the student exists in the residence table
    3. If not look for a room with three students & is relevant in terms
    of gender and or level
    */
    use HttpResponses;
    use Utils;
    use VUtils;

    private $currentLoopingStudent;
    private $currentLoopingStudentGender;

    private $roomsWithLessStudents;
    private $roomsWithoutStudents;

    private $activeStudentType;
    private $roomOccupationType;

    public function __construct(private VAuditService $auditService, private VEngineService $vEngineService)
    {
        $this->auditService->setRoomOccupationType();
    }

    public function auditInit()
    {
        //1
        $requests = $this->auditService->selectGeneralRequestsByStudentType();

        //2
        foreach ($requests as $request) {
            //check if the student has a room
            $studentHasRoom = $this->auditService->doesStudentHaveARoom($request->student_id);

            //if student has a room then pass
            //otherwise let's give them a room
            switch ($studentHasRoom) {
                case true:
                    $this->auditService->updateGeneralRequestProcessedStatus($request->student_id);
                    break;
                case false:
                    $this->currentLoopingStudent = $request->student_id;
                    //first get the gender of the student
                    $this->currentLoopingStudentGender = $this->gender($request->student_id);
                    //3
                    $this->processRooms();
                    break;
            }
        }


        return $this->sendData("Audit Done");
    }

    private function processRooms()
    {
        /**
         * 1. check if those that are occupied are in full capacity
         *    if not add to the list of rooms
         * 2. check if all rooms in the current gender context
         *    hv been occupied if not add otherwise also
         */

        $this->rooms();

        if (count($this->roomsWithLessStudents)) {
            //1.1 Get the room with the closest/exact level
            //with the current loop student
            $room = $this->matchRoomWithLevel($this->roomsWithLessStudents);
            $this->assignRoom($room);
        } elseif (count($this->roomsWithoutStudents)) {
            //grab the first room in the list
            $room = $this->roomsWithoutStudents[0];
            $this->assignRoom($room);
        }
    }

    private function assignRoom($room)
    {
        //first verify if the room is already occupied, if occupied don't update
        $roomIsOccupied = $this->auditService->isRoomOccupied($this->currentLoopingStudentGender, $room);

        if (!$roomIsOccupied)
            $this->auditService->updateRoomOccupationStatus($this->currentLoopingStudentGender, $room);

        //add the student to the residence
        $this->vEngineService->createResidence(
            new CreateResidenceDto(
                $this->currentLoopingStudent,
                $room,
                $this->currentLoopingStudentGender
            )
        );

        //check if the current looping student is a requester
        if ($this->auditService->isRequester($this->currentLoopingStudent))
            $this->auditService->updateRequesterProcessedStatus($this->currentLoopingStudent);


        //update the general requests table
        $this->auditService->updateGeneralRequestProcessedStatus($this->currentLoopingStudent);
    }

    private function matchRoomWithLevel($roomsWithLessStudents)
    {
        $currentLoopingLevel = $this->part($this->currentLoopingStudent);
        $closestStudentLevel = $this->lowerLevel($currentLoopingLevel);

        // Get the room with the closest/exact level
        // with the current loop student
        foreach ($roomsWithLessStudents as $room) {
            //get the first student attached to that room
            $firstStudent = $this->auditService->getFirstStudentFromTheRoom($room);
            //now get this student's level/part
            $firstStudentLevel = $this->part($firstStudent->student_id);

            //now compare this level with the current looping level
            //if it qualifies return the room 
            if ($firstStudentLevel === $currentLoopingLevel || $firstStudentLevel === $closestStudentLevel) {
                return $room;
            }
        }

        //otherwise return the first room that comes
        return $roomsWithLessStudents[0];
    }

    private function rooms()
    {
        $this->roomsWithLessStudents = [];
        $this->roomsWithoutStudents = [];

        /***
         * 1. Get all rooms that are usable and not student type occupied by the current gender
         * 2. Get all rooms in the residence table
         * 3. Check each room if it is in full capacity in the residence table
         * 4. If room is not in full capacity add the room to roomsWithLessStudents 
         * 5. Check if room from the allRooms is in the residence room
         * 6. If not, add the room to the roomsWithoutStudents array
         */

        $allRooms = $this->auditService->getAllRooms($this->currentLoopingStudentGender);
        $residenceRooms = $this->auditService->getAllResidenceRooms($this->currentLoopingStudentGender);

        $this->roomsWithLessStudents = $residenceRooms->filter(function ($room) {
            return $this->roomHasLessStudents($room);
        });

        $this->roomsWithoutStudents = $allRooms->filter(function ($room) use ($residenceRooms) {
            return !in_array($room, $residenceRooms->toArray());
        });
    }

    private function roomHasLessStudents($room)
    {
        $query = $this->auditService->getNumberOfStudentsInTheRoom($room, $this->currentLoopingStudentGender);

        if ($query->student_count < HostelConstants::MAX_STUDENTS_PER_ROOM) return true;
        else false;
    }
}
