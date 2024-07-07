<?php

namespace App\Http\Controllers;

use App\Constants\SelectionResponse;
use App\Constants\StudentConstants;
use App\DataTransferObjects\GenerateRequestRoommateDto;
use App\Http\Services\GenerateRequestsService;
use App\Traits\Utils;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Traits\VUtils;
use Illuminate\Support\Facades\DB;

class GenerateRequestsController extends Controller
{


    /**
     * Game plan
     * 
     *  1. Fetch all the students of the same level and gender
     *  2. Loop through the students
     *  3. Check if the current student in loop has not made any requests
     *  4. Check if the current student is not part of any set of room mates
     *  5. If not make that student a requester
     *  6. Based on Step 5 Select roommates for the student or find the students roommates
     */

    use Utils;
    use VUtils;
    use HttpResponses;

    private $response = 3; //represents the response of a selected roommate
    private $results = [];
    private $currentLoopingRequester;

    public function __construct(private GenerateRequestsService $requestsService)
    {
    }

    public function init()
    {
        // Commented out because when those that are added through the UI, will be destroyed
        // $this->requestsService->destroyAll();
        /*
         This loop based on gender will make sure we 
         allocate all the rooms to the females first
        */

        foreach (StudentConstants::GENDER as $gender) {
            foreach (StudentConstants::LEVELS as $level) {

                /* 
                    Step 1: Get the students of the same level with same gender & same student type
                */
                $pool = $this->requestsService->genderFirstSameLevelStudents($gender, $level);

                // if there is nothing look into another level
                if (count($pool)) {
                    /* 
                    Step 2&3: Out of the fetched students, get those that hv zero room requests
                    or have not been selected as a roommate 
                */
                    $students = $this->requestsService->freeMates($pool);

                    //lets create requests
                    $this->createRequests($students, $gender);

                    /* 
                    lets patch the create requests because some of
                    the selected roommates will decline and hence we need to 
                    create new requests for them
                */
                }
            }
        }

        $this->requestsService->duplicateRequestCandidates();
        return $this->sendResponse('Done');
    }

    private function createRequests($students, $gender)
    {

        foreach ($students as $student) {
            /*
                Step 4: Make sure the current student in the loop is not a requester
                but first check if the student is free
            */

            //Step 5&6
            if ($this->requestsService->isFree($student)) {

                //lets get roommates for this student
                $roommates = $this->selectRoommates($student->student_id, $students, $gender);

                if (!count($roommates)) return;
                else {
                    //Make the student a requester
                    $this->requestsService->createRequester($student->student_id, $gender);
                    //Add the student to the general requests
                    $this->requestsService->addToGeneralRequests($student->student_id);

                    foreach ($roommates as $roommate) {
                        //capture request candidates
                        $this->requestsService->addRequestCandidates($roommate);
                        //add the student to the general requests if only
                        //they have a positive response - yes for selection-confirmed
                        if ($roommate['selection_confirmed'] === SelectionResponse::YES) {
                            $this->requestsService->addToGeneralRequests($roommate['selected_roommate']);
                            $this->requestsService->setOtherSelectionsToNo($student->student_id, $roommate['selected_roommate']);
                        }
                    }
                }
            }
        }
    }

    private function selectRoommates($requesterId, $potentialRoommates, $gender)
    {
        //run the usual check
        $students = $this->requestsService->freeMates($potentialRoommates);
        if (count($students) < 3) return [];

        $roommates = [];

        foreach ($students as $student) {
            if (count($roommates) === 3) break;
            if ($student->student_id !== $requesterId) {
                $_roommate = new GenerateRequestRoommateDto($requesterId, $student->student_id, $gender);
                if (count($_roommate->data())) $roommates[] = $_roommate->data();
            }
        }

        return $this->setRoommateResponse($roommates);
    }

    private function setRoommateResponse($roommates)
    {
        $_roommates = [];

        /**
         * Response represents the number of roommates who have a positive selection
         * if we are on 3, it means all 3 will say yes to the selection, if we are on 2
         * it means 2 will say yes, 1 will decline
         */

        foreach ($roommates as $index => $roommate) {

            switch ($this->response) {
                case 3:
                    $roommate['selection_confirmed'] = SelectionResponse::YES;
                    break;
                case 2:
                    if ($index === 2) $roommate['selection_confirmed'] = SelectionResponse::NO;
                    else $roommate['selection_confirmed'] = SelectionResponse::YES;
                    break;
                case 1:
                    if ($index === 0) $roommate['selection_confirmed'] = SelectionResponse::YES;
                    else $roommate['selection_confirmed'] = SelectionResponse::NO;
                    break;
                default:
                    $roommate['selection_confirmed'] = SelectionResponse::NO;
            }

            $_roommates[] = $roommate;
        }

        if ($this->response === 3) $this->response = 2;
        elseif ($this->response === 2) $this->response = 1;
        elseif ($this->response === 1) $this->response = 0;
        else $this->response = 3;

        return $_roommates;
    }
}
