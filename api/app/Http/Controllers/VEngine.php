<?php

namespace App\Http\Controllers;

use App\Traits\Utils;
use App\Traits\VUtils;
use App\Traits\HttpResponses;
use App\Constants\StudentConstants;
use App\Constants\SelectionResponse;
use App\Http\Helpers\VEngineHelpers;
use App\Http\Services\VAuditService;
use App\Http\Services\VEngineService;
use App\DataTransferObjects\CreateResidenceDto;
use App\DataTransferObjects\CreateRequestCandidateDto;

class VEngine extends Controller
{
    use HttpResponses;
    use Utils;
    use VUtils;

    private $requesters;
    private $residence;

    private $currentLoopingLevel;
    private $currentLoopingGender;
    private $currentLoopingRequester;

    public function __construct(
        private VEngineService $vEngineService,
        private VAuditService $vAuditService,
        private VEngineHelpers $vHelpers,
        private VAuditController $vAudit
    ) {
    }

    public function init()
    {
        /**
         * Game plan
         * 
         *  1. Fetch all requests(requesters) of the same level and gender
         *  2. Loop through the students
         *  3. Check if the current student in loop has not been given a room
         *  4. Get the roommates of the student & check if they have not been given a room
         *  5. Check the number of confirmations the student has from the selected roommates
         *  6. Allocate a room
         */

        foreach (StudentConstants::GENDER as $gender) {
            foreach (StudentConstants::LEVELS as $level) {
                //set these two globals
                $this->currentLoopingLevel = $level;
                $this->currentLoopingGender = $gender;

                //Step 1
                $requesters = $this->vEngineService->genderFirstSameLevelRequesters($gender, $level);

                //Step 3
                $__requesters = $this->vHelpers->freeRequesters($requesters);

                if (count($__requesters)) {
                    //lets begin the fun
                    $this->processRequests($__requesters, $gender);
                }
            }
        }

        //TODO: activate VAudit
        $this->vAudit->auditInit();

        return $this->sendResponse('Done');
    }

    private function processRequests($requesters, $gender)
    {

        foreach ($requesters as $requester) {
            //Set the global current looping requester state
            $this->currentLoopingRequester = $requester->student_id;

            //Step 4 - grab only the selected roommates that hv not been allocated a room 
            //and have a positive confirmation
            $selectedRoommates = $this->vHelpers->freeSelectedRoommates($requester->student_id);

            //Step 5
            //5.1.1 If all three roommates confirmed, straight away allocate them a room
            if (count($selectedRoommates) === 3) {
                $this->grantRoom($requester->student_id, $selectedRoommates);
            }

            //5.1.2 If two roommates confirmed, look for a requester with zero confirmations
            elseif (count($selectedRoommates) === 2) {
                $requesters = $this->vEngineService->getRequesters();
                //requester(s) with zero confirmations
                $orphanRequesters = $this->vHelpers->set(0, $requesters, $this->currentLoopingRequester);

                if (count($orphanRequesters)) {

                    /**
                     * 1. Delete all the selected roommates of the orphan requester
                     * 2. Add the orphaned requester as a selected roommate to the current looping requester
                     * 3. pull the newly created selected roommates
                     * 4. grant them a room
                     * 5. Update the requester processed status for the orphaned requester
                     */

                    $orphanRequester = $orphanRequesters[0];
                    //1
                    $this->vEngineService->deleteSelectedRoommates($orphanRequester->student_id);
                    $this->vEngineService->deleteRequester($orphanRequester->student_id);
                    //This is for the current looping requester
                    $this->vEngineService->deleteRoommateWhoDeclined($requester->student_id);

                    //2
                    $selectedRoommate = $orphanRequester->student_id;
                    $this->vEngineService->createRequestCandidate(
                        new CreateRequestCandidateDto(
                            $requester->student_id,
                            $selectedRoommate,
                            $gender
                        )
                    );

                    //3
                    $newlySelectedRoommates = $this->vHelpers->freeSelectedRoommates($requester->student_id);

                    //4&5 - Grant them a room
                    $this->grantRoom($requester->student_id, $newlySelectedRoommates);
                }
                //if we can't find an orphan requester
                else {
                    //Let's lookup for the number of sets where there is only one confirmation
                    //if it returns true -> split otherwise look for a requester with one confirmation and split
                    $requesters = $this->vEngineService->getRequesters();
                    $requestersWithOneConfirmation = $this->vHelpers->set(1, $requesters, $this->currentLoopingRequester);

                    if (count($requestersWithOneConfirmation)) {

                        $requesterWithOneConfirmation = $requestersWithOneConfirmation[0];

                        //1. Grab the roommate who confirmed and leave the requester
                        //2.0 Delete the current's requester roommate who declined
                        //2.1 Make the roommate a selected roommate for the current looping requester
                        //3. Set all selected roommates response to No for the requestersWithOneConfirmation
                        //4. Grab the newly created selected roommates squad
                        //5. Allocate them a room


                        //1 This roommate is for the requesterWithOneConfirmation
                        $roommateWhoConfirmed =
                            $this->vEngineService->onlyRoommateWhoConfirmed($requesterWithOneConfirmation->student_id);

                        //2.0
                        $this->vEngineService->deleteRoommateWhoDeclined($requester->student_id);

                        //2.1
                        $this->vEngineService->createRequestCandidate(
                            new CreateRequestCandidateDto(
                                $requester->student_id,
                                $roommateWhoConfirmed->selected_roommate,
                                $this->currentLoopingGender
                            )
                        );

                        //3
                        $this->vEngineService
                            ->updateRequestCandidateSelectionConfirmation(
                                $requesterWithOneConfirmation->student_id,
                                SelectionResponse::NO
                            );

                        //4
                        $newlySelectedRoommates = $this->vHelpers->freeSelectedRoommates($requester->student_id);

                        //5
                        $this->grantRoom(
                            $requester->student_id,
                            $newlySelectedRoommates
                        );
                    }
                    //if we can't find a 4th roommate give the three roommates a room
                    else {
                        $this->grantRoom(
                            $requester->student_id,
                            $selectedRoommates
                        );
                    }
                }
            }

            //5.1.3 If one roommate confirmed this makes two roommates, 
            // Option 1 -> find a requester with one confirmation
            // Option 2 -> find 2 requesters with zero confirmations
            elseif (count($selectedRoommates) === 1) {
                $requesters = $this->vEngineService->getRequesters();
                $requestersWithOneConfirmation = $this->vHelpers->set(1, $requesters, $this->currentLoopingRequester);
                // Option 1 -> find another squad of similar manner
                if (count($requestersWithOneConfirmation)) {
                    //1. grab that one roommate who confirmed for the requester with one confirmation
                    //2. add that roommate to the current looping requester roommates as a selected roommate
                    //3. add the requester with one confirmation as a roommate too
                    //4. grab the new selected roommates
                    //5. Grant them a room,
                    //6. Update requester processed status for the requesterWithOneConfirmation

                    $requesterWithOneConfirmation = $requestersWithOneConfirmation[0];

                    //1 This roommate is for the requesterWithOneConfirmation
                    $roommateWhoConfirmed =
                        $this->vEngineService->onlyRoommateWhoConfirmed($requesterWithOneConfirmation->student_id);

                    //2&3
                    foreach ([$roommateWhoConfirmed->selected_roommate, $requesterWithOneConfirmation->student_id] as $studentId) {
                        $this->vEngineService->createRequestCandidate(
                            new CreateRequestCandidateDto(
                                $requester->student_id,
                                $studentId,
                                $gender
                            )
                        );
                    }

                    //3.1 Delete the requester with one confirmation entry in the requesters 
                    $this->vEngineService->deleteRequester($requesterWithOneConfirmation->student_id);


                    //4 Get rid of all roommates of the requester with one confirmation
                    $this->vEngineService->deleteSelectedRoommates($requesterWithOneConfirmation->student_id);

                    //5 Get rid of all the selected roommates of the current looping requester with response No
                    $this->vEngineService->deleteAllSelectedRoommatesWithZeroConfirmations($requester->student_id);

                    $newlySelectedRoommates = $this->vHelpers->freeSelectedRoommates($requester->student_id);

                    //5&6
                    $this->grantRoom(
                        $requester->student_id,
                        $newlySelectedRoommates
                    );
                }
                //Option 2 -> find 2 requesters with zero confirmations 
                else {
                    /*
                     1.1 Let's first count if we have enough requesters with zero confirmations.
                     - If we don't, let's look into another level deep
                    */
                    $requestersWithZeroConfirmations = $this->vHelpers->gatherRequesters(
                        $requesters,
                        $this->currentLoopingRequester,
                        $this->currentLoopingLevel,
                        $this->currentLoopingGender
                    );

                    if (count($requestersWithZeroConfirmations) === 2) {
                        //add these requesters as roommates for the current looping requester
                        //and delete all selected roommates for these requesters
                        //and delete the orphanRequester from the requesters

                        foreach ($requestersWithZeroConfirmations as $orphanRequester) {
                            $this->vEngineService->createRequestCandidate(
                                new CreateRequestCandidateDto(
                                    $requester->student_id,
                                    $orphanRequester->student_id,
                                    $gender
                                )
                            );
                            $this->vEngineService->deleteSelectedRoommates($orphanRequester->student_id);
                            $this->vEngineService->deleteRequester($orphanRequester->student_id);
                        }

                        //delete all selected roommates of the current looping requester with no
                        //as the selection confirmation
                        $this->vEngineService->deleteAllSelectedRoommatesWithZeroConfirmations($requester->student_id);

                        //grab the newly created selected roommates
                        $newlySelectedRoommates = $this->vHelpers->freeSelectedRoommates($requester->student_id);

                        $this->grantRoom($requester->student_id, $newlySelectedRoommates);
                    }
                }

                /* 
                    if nun of these conditions are met and the 
                    requester was not allocated a roommate after a full circle
                    the audit system should pick it up and make necessary adjustments
                */
            }
        }
    }

    //TODO: update the hostel issue in this function
    private function grantRoom($requester_id, $selectedRoommates)
    {
        if (!$this->vEngineService->getFreeRoom($this->currentLoopingGender)) {
            return $this->sendResponse('Rooms are full');
        };

        //first update the requester table
        $this->vEngineService->updateRequesterProcessedStatus($requester_id);

        //update the rooms table
        $freeRoom = $this->vEngineService->getFreeRoom($this->currentLoopingGender);
        $this->vEngineService->updateRoomOccupationStatus($this->currentLoopingGender, $freeRoom);
        $this->createResidence($requester_id, $selectedRoommates, $freeRoom);
    }

    //TODO: This function needs to be adjusted 
    private function createResidence($requester_id, $selectedRoommates, $room)
    {
        //migrate current residence data to old residence 

        //record residence for the requester
        $this->vEngineService->createResidence(
            new CreateResidenceDto(
                $requester_id,
                $room,
                $this->currentLoopingGender
            )
        );

        //record the residence for the roommates
        foreach ($selectedRoommates as $roommate) {
            $this->vEngineService->createResidence(
                new CreateResidenceDto(
                    $roommate->selected_roommate,
                    $room,
                    $this->currentLoopingGender
                )
            );
        }
    }
}
