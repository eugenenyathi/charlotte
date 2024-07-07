<?php

namespace App\Http\Helpers;

use App\Http\Services\VEngineService;
use App\Traits\Utils;
use App\Traits\VUtils;

class VEngineHelpers
{
    use Utils;
    use VUtils;

    public function __construct(public VEngineService $vEngineService)
    {
    }

    public function freeRequesters($requesters)
    {
        return $requesters->filter(function ($requester) {
            return !$this->vEngineService->isRequesterRequestProcessed($requester->student_id);
        })->values();
    }

    public function freeSelectedRoommates($requester_id)
    {
        //Get all the selected roommates of the current requester who confirmed
        $selectedRoommates = $this->vEngineService->selectedRoommatesWithConfirmation($requester_id);

        return $this->freeMates($selectedRoommates);
    }

    public function freeMates($students)
    {
        return $students->filter(function ($student) {
            return !$this->vEngineService->selectedMatesHaveRooms($student->selected_roommate);
        })->values();
    }

    /*
     This method gets, depending on the parameter
     1. A requester with zero roommate confirmation by default
     2. A requester with 1/2 roommate confirmation 
    */
    public function set($confirmations = 0, $requesters, $currentLoopingRequester)
    {
        //Loop through the requesters that match the current level & gender
        //and seek a requester who is FREE & has no confirmations from the selected roommates
        $filteredRequesters = $requesters->filter(function ($requester) use ($currentLoopingRequester) {
            return $requester->student_id != $currentLoopingRequester;
        });

        return $filteredRequesters->filter(function ($requester) use ($confirmations) {
            $selectedRoommates = $this->freeSelectedRoommates($requester->student_id);
            return $this->isFreeRequester($requester->student_id) && count($selectedRoommates) === $confirmations;
        })->values();
    }

    public function isFreeRequester($requester_id)
    {
        // Basic rule - if the record exists then the requester is not free
        //return false
        $requesterRequestProcessed =
            $this->vEngineService->isRequesterRequestProcessed($requester_id);

        if ($requesterRequestProcessed) return false;
        else return true;
    }


    public function gatherRequesters($requesters, $currentLoopingRequester, $currentLoopingLevel, $currentLoopingGender)
    {
        $crowdRequesters = $this->set(0, $requesters, $currentLoopingRequester);

        $lowerLevel = $this->lowerLevel($currentLoopingLevel);

        $lowerLevelRequesters =
            $this->vEngineService->genderFirstSameLevelRequesters($currentLoopingGender, $lowerLevel);

        //if we get one, look into another level and get one, that makes two
        if (count($crowdRequesters) > 1) {
            return $crowdRequesters;
        } else if (count($crowdRequesters) && count($crowdRequesters) < 2) {
            $orphanRequester = $this->set(0, $lowerLevelRequesters, null);
            if (count($orphanRequester)) $crowdRequesters[] = $orphanRequester[0];
        } else {
            $crowdRequesters = $this->set(0, $lowerLevelRequesters, null);
        }

        return $crowdRequesters;
    }
}
