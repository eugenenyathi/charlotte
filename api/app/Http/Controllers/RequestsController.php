<?php

namespace App\Http\Controllers;

use App\Traits\Utils;
use App\Traits\VUtils;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Constants\RequestStatus;
use App\Constants\HostelConstants;
use App\Constants\StudentConstants;
use App\Constants\SelectionResponse;
use App\Http\Helpers\RequestsHelpers;
use App\Http\Services\RequestsService;
use App\Http\Requests\RoomRequest;
use App\DataTransferObjects\HasRoomRequestDto;
use App\Http\Requests\RoommateResponseRequest;
use App\DataTransferObjects\RequestResponseDto;
use App\DataTransferObjects\RequestRoommateDto;
use App\DataTransferObjects\RequestRequesterDto;

class RequestsController extends Controller
{
    //
    use Utils;
    use VUtils;
    use HttpResponses;

    // private $helpers;

    public function __construct(private RequestsService $requestsService, private RequestsHelpers $helpers)
    {
    }

    public function createRequest(RoomRequest $request)
    {
        $request->validated($request->all());

        /**
         * 1. Check if all the selected are free roommates
         * 2. Add the request initiator to requester table
         * 3. Add the request to the candidates table
         */

        $freeMates = $this->helpers->freeMates($request);

        if (count($freeMates) > 0) return $this->sendData($freeMates);

        //if all check out then let's roll get requester gender
        $requesterGender = $this->gender($request->requester);
        //capture the requester
        $this->requestsService->createRequester($request->requester, $requesterGender);
        //record the request on general requests
        $this->requestsService->createGeneralRequest($request->requester);

        foreach ($request->roommates as $roommateID) {
            $this->requestsService->createRequestCandidate($request->requester, $roommateID, $requesterGender);
        }

        //fetch the newly created request candidates
        $requestCandidates = $this->requestsService->getRequesterNewlyCreatedRequestCandidates($request->requester);
        $candidates = $this->helpers->candidateDTOs($requestCandidates);

        $response = new RequestResponseDto(RequestStatus::SUCCESS, $candidates);
        return $this->sendData($response->data());
    }

    public function updateRequest(RoomRequest $request)
    {
        $request->validated($request->all());

        // Check if the requester exists in the table, if so its an editing request
        $studentIsARoomRequester = $this->requestsService->isStudentARoomRequester($request->requester);

        if (!$studentIsARoomRequester) return $this->sendError("Invalid update request");

        $requesterGender = $this->gender($request->requester);

        $this->helpers->purgeOldRoommates($request);

        foreach ($request->roommates as $roommateID) {

            /**
             * If roommates exists as a confirmed candidate for other requesters, throw an error
             * If the roommate exists as a candidate, if true -> check if they are a confirmed candidate
             * If true, skip
             * If roommate does not exists as a candidate, record
             */

            $roommateHasAConfirmedSelectionWhereNotRequester =
                $this->requestsService->candidateHasAConfirmedSelectionWhereNotRequester($request->requester, $roommateID);

            if ($roommateHasAConfirmedSelectionWhereNotRequester) {
                $roommateName = $this->getFullName($roommateID);
                return $this->sendError("Selected roommate $roommateName is not available for selection");
            } else {
                //check if the roommate exists as a candidate & then check if candidate has a positive response
                $candidateIsARoommateForRequester =
                    $this->requestsService->candidateIsARoommateForRequester(
                        $request->requester,
                        $roommateID
                    );

                if (!$candidateIsARoommateForRequester) {
                    $this->requestsService->createRequestCandidate($request->requester, $roommateID, $requesterGender);
                }
            }
        }

        //fetch the newly created request candidates
        $requestCandidates = $this->requestsService->getRequesterNewlyCreatedRequestCandidates($request->requester);
        $candidates = $this->helpers->candidateDTOs($requestCandidates);

        $response = new RequestResponseDto(RequestStatus::SUCCESS, $candidates);
        return $this->sendData($response->data());
    }

    //1. checking if the student has made a room request or has been selected as a roommate
    public function requestStatus($studentID)
    {
        //Get if the current student is a Con or a Block
        $studentType = $this->studentType($studentID);

        //check if the portal is open for block students 
        if (
            $studentType == StudentConstants::BLOCK_STUDENT
            && $this->requestsService->activeStudentType != StudentConstants::BLOCK_STUDENT
        )
            return $this->sendData(['status' => RequestStatus::PORTAL_CLOSED]); //TODO: FRONTEND NEEDS TO SHOW THIS

        //check if student is allegeable to access portal
        $registrationStatus = $this->requestsService->getRegistrationStatus($studentID);

        if ($registrationStatus === 0) return $this->sendData(['status' => RequestStatus::NOT_REGISTERED]);
        // checking if the student has been allocated a room
        $studentHasRoom = $this->requestsService->hasStudentBeenAllocatedARoom($studentID);

        switch ($studentHasRoom) {
            case true:
                //get the room & all the roommates for that room
                $room = $this->requestsService->getStudentRoom($studentID)->room;
                $roommates = $this->requestsService->getStudentRoommates($studentID, $room);

                $dto = new HasRoomRequestDto(RequestStatus::ALLOCATED, $studentID, $room, $roommates);

                return $this->sendData($dto->data());
            case false:
                //checking if rooms have been allocated for this particular student type
                if ($studentType === StudentConstants::CON_STUDENT) $roomOccupationType = HostelConstants::CON_OCCUPIED;
                else $roomOccupationType = HostelConstants::BLOCK_OCCUPIED;

                $roomsHaveBeenAllocated = $this->requestsService->haveStudentTypeRoomsBeenAllocated($roomOccupationType);

                if ($roomsHaveBeenAllocated)
                    return $this->sendData(['status' => RequestStatus::ROOMS_ASSIGNED]); //TODO: FRONTEND NEEDS TO SHOW THIS

                // checking if the student has made a room request
                $studentIsARoomRequester = $this->requestsService->isStudentARoomRequester($studentID);

                switch ($studentIsARoomRequester) {
                    case true:
                        $dto = new RequestResponseDto(RequestStatus::REQUESTER, $this->helpers->pullPreferredMates($studentID));
                        return $this->sendData($dto->data());

                    case false:
                        //check if the student has been selected as a roommate
                        $selected = $this->requestsService->hasStudentBeenSelectedAsRoommate($studentID);

                        if (!$selected)
                            return $this->sendData(['status' => RequestStatus::NOT_SELECTED]);


                        //fetch the requester(s) that selected the student as a roommate
                        $requester = $this->requestsService->getRequestersWhoSelectedTheRoommate($studentID);

                        //check how many requesters selected the student
                        //If they are more that one then it means the response of this
                        //student is neither 'yes' nor 'no' but 'waiting'

                        if (count($requester) > 1)
                            return $this->helpers->hasManyRequesters($requester, $studentID);
                        else return $this->helpers->hasSingleRequester($studentID);
                }
        }
    }

    public function roommateResponse(RoommateResponseRequest $request)
    {
        $request->validated($request->all());

        switch ($request->response) {
            case SelectionResponse::YES:

                $this->requestsService
                    ->updateSelectedRoommateSelectionByRequesterID($request->requesterID, $request->studentID, SelectionResponse::YES);
                /**
                 * If there is a selection by another requester which is
                 * not the one the selected roommate choice, we declining
                 * the request of that requester
                 */
                $this->helpers->declineOtherRequesters($request->requesterID, $request->studentID);
                //add the student to the general requests
                $this->requestsService->createGeneralRequest($request->studentID);
                //fetch the other roommates
                $roommates = $this->helpers->pullPreferredMates($request->requesterID, $request->studentID, RequestStatus::SELECTED);

                $_requester = new RequestRequesterDto($request->requesterID, SelectionResponse::YES);
                //append the requester to the list of roommates
                $roommates[] = $_requester->data();

                return $this->sendData($roommates);

            case SelectionResponse::NO:

                $this->requestsService
                    ->updateSelectedRoommateSelectionByRequesterID($request->requesterID, $request->studentID, SelectionResponse::NO);

                return $this->sendResponse('Your consent has been updated');

            default:
                return $this->sendResponse('Invalid response');
        }
    }

    public function revertResponse($studentID)
    {
        $this->requestsService
            ->updateSelectedRoommateSelectionConfirmationStatus($studentID, SelectionResponse::WAITING);

        return $this->sendResponse('Your consent has been updated');
    }

    public function destroyRequest($studentID)
    {
        $this->requestsService->destroyRequest($studentID);
        return $this->sendResponse('Delete successful');
    }
}
