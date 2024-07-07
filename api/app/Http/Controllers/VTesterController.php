<?php

namespace App\Http\Controllers;

use App\Traits\VUtils;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Constants\StudentConstants;
use App\Constants\SelectionResponse;
use App\Http\Services\GenerateRequestsService;
use App\DataTransferObjects\GenerateRequestRoommateDto;

class VTesterController extends Controller
{
    use VUtils;
    use HttpResponses;

    private $gender = StudentConstants::FEMALE;
    private $level = 4.1;
    private $response = 3;
    private $activeStudentType;

    public function __construct(private GenerateRequestsService $requestsService)
    {
        $this->activeStudentType = $this->getActiveStudentType();
    }

    public function init($scenario)
    {
        $scenario = floatval($scenario);
        /**
         * 1. Get all part 2.1's of the specified gender
         * 2. 
         * 3. Scenario 1 -> If all three roommates confirmed, straight away allocate them a room
         * 4. Scenario 2 -> If two roommates confirmed, look for a requester with zero confirmations
         * 5. Scenario 2.1 -> If two roommates confirmed, split requester with one confirmation
         * 6. Scenario 2.2 -> Two roommates confirmed and no other possible confirmations
         * 6. Scenario 3 -> If one roommate confirmed this makes two roommates, find a requester with one confirmation
         * 7. Scenario 3.1 -> If one roommate confirmed this makes two roommates, find 2 requesters with zero confirmations
         */

        //DELETE ALL RECORDS 
        $this->requestsService->destroyAll();

        $pool = $this->requestsService->genderFirstSameLevelStudents($this->gender, $this->level);

        if (count($pool)) {

            switch ($scenario) {
                case 1.0:
                    $this->response = 3;
                    $this->createRequests($pool);
                    break;
                case 2.0:
                    //We want 2 positive responses & 1 requester with 0 responses
                    $this->response = 2;
                    $this->createRequests($pool);

                    $this->response = 0;
                    $this->createRequests($pool);
                    break;
                case 2.1:
                    //We want 2 positive responses & 1 requester with 1 positive responses
                    $this->response = 2;
                    $this->createRequests($pool);

                    $this->response = 1;
                    $this->createRequests($pool);
                    break;
                case 2.2:
                    //We want 2 positive responses & zero possible combinations
                    $this->response = 2;
                    $this->createRequests($pool);
                    break;

                case 3.0:
                    //We want 1 positive response & another 1 positive response
                    $this->response = 1;
                    $this->createRequests($pool);

                    $this->response = 1;
                    $this->createRequests($pool);
                    break;

                case 3.1:
                    //We want 1 positive response & another 2 sets of 0 positive responses
                    $this->response = 1;
                    $this->createRequests($pool);

                    $this->response = 0;
                    $this->createRequests($pool);

                    $this->response = 0;
                    $this->createRequests($pool);
                    break;
            }

            return $this->sendResponse("Scenario $scenario set!");
        }

        return $this->sendData("No students found!");
    }

    private function createRequests($pool)
    {
        $students = $this->requestsService->freeMates($pool);
        $students = array_slice($students, 0, 4);

        foreach ($students as $student) {
            /*
                Make sure the current student in the loop is not a requester
                but first check if the student is free   
            */

            if ($this->requestsService->isFree($student)) {

                //lets get roommates for this student
                $roommates = $this->roommates($student->student_id, $students, $this->gender);

                if (count($roommates) < 3) return;
                else {
                    //Make the student a requester
                    $this->requestsService->createRequester($student->student_id, $this->gender);
                    //Add the student to the general requests
                    $this->requestsService->addToGeneralRequests($student->student_id);

                    foreach ($roommates as $roommate) {
                        //capture request candidates
                        $this->requestsService->addRequestCandidates($roommate);
                        //add the student to the general requests if only
                        //they have a positive response - yes for selection-confirmed
                        if ($roommate['selection_confirmed'] === SelectionResponse::YES) {
                            $this->requestsService->addToGeneralRequests($roommate['selected_roommate']);
                        }
                    }
                }
            }
        }
    }

    private function roommates($requesterId, $potentialRoommates, $gender)
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

        return $this->roommateResponse($roommates);
    }

    private function roommateResponse($roommates)
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

        return $_roommates;
    }
}
