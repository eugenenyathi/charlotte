<?php

namespace App\Http\Helpers;

use App\Traits\Utils;
use App\Traits\HttpResponses;
use App\Constants\RequestStatus;
use App\Constants\SelectionResponse;
use App\Http\Services\RequestsService;
use App\DataTransferObjects\RequestResponseDto;
use App\DataTransferObjects\RequestRequesterDto;
use App\DataTransferObjects\RequestRoommateDto;

class RequestsHelpers
{
    use Utils;
    use HttpResponses;

    public function __construct(public RequestsService $requestsService)
    {
    }

    public function hasManyRequesters($requesters, $studentID)
    {

        $requesters_data = [];

        foreach ($requesters as $singleRequester) {
            //fetch the roommates of this single requester
            $roommates = $this->pullPreferredMates($singleRequester->requester_id, $studentID, RequestStatus::SELECTED);
            $_requester = new RequestRequesterDto($singleRequester->requester_id);

            $requesters_data[] = [
                'requester' => $_requester->data(),
                'roommates' => $roommates
            ];
        }

        return $this->sendData([
            'status' => RequestStatus::WAITING,
            'type' => 'multi',
            'requesters' => $requesters_data
        ]);
    }

    public function hasSingleRequester($studentID)
    {
        // fetch the requester that selected the roommate
        $requesterId = $this->requestsService->getRequesterWhoSelectedTheRoommate($studentID);
        //fetch the other roommates
        $roommates = $this->pullPreferredMates($requesterId, $studentID, RequestStatus::SELECTED);
        //check if the roommate has confirmed his/her selection request
        $selectionResponse = $this->requestsService->getSelectedRoommateResponse($studentID);

        switch ($selectionResponse->selection_confirmed) {
            case SelectionResponse::YES:
                //append the requester to the list of roommates
                $_requester = new RequestRequesterDto($requesterId);
                $roommates[] = $_requester->data();
                $response = new RequestResponseDto(RequestStatus::CONFIRMED, $roommates);

                return $this->sendData($response->data());

            case SelectionResponse::NO:
                return $this->sendData(['status' => RequestStatus::CANCELLED]);

            case SelectionResponse::WAITING:
                $_requester = new RequestRequesterDto($requesterId);

                return $this->sendData([
                    'status' => RequestStatus::WAITING,
                    'type' => 'single',
                    'requester' => $_requester->data(),
                    'roommates' => $roommates
                ]);
        }
    }

    public function declineOtherRequesters($requesterId, $choiceMakerId)
    {
        //fetch the requester(s) that selected the student as a roommate
        $requesters = $this->requestsService->getRequestersWhoSelectedTheRoommate($choiceMakerId);

        if (count($requesters) < 1) return;

        foreach ($requesters as $requester) {
            if ($requesterId !== $choiceMakerId)
                $this->requestsService
                    ->updateSelectedRoommateSelectionByRequesterID($requester->requester_id, $choiceMakerId, SelectionResponse::NO);
        }
    }

    public function pullPreferredMates($requesterId, $selectedRoommate = '', $type = RequestStatus::REQUESTER)
    {
        $roommates = [];

        switch ($type) {
            case RequestStatus::REQUESTER:
                //fetch the newly created request candidates
                $roommates = $this->requestsService->getRequesterNewlyCreatedRequestCandidates($requesterId);
                break;
            case RequestStatus::SELECTED:
                //fetch the newly created request candidates
                $roommates =
                    $this->requestsService
                    ->getSelectedNewlyCreatedRequestCandidates($requesterId, $selectedRoommate);
                break;
        }

        $data = [];

        foreach ($roommates as $roommate) {
            $_roommate = new RequestRoommateDto($roommate);
            $data[] = $_roommate->data();
        }

        return $data;
    }

    public function freeMates($request)
    {
        $candidates = [$request->roomie1, $request->roomie2, $request->roomie3];
        $notAvailableCandidates = [];

        //run the check if any of them are taken
        foreach ($candidates as $candidateID) {
            $candidateExists = $this->requestsService->doesCandidateHaveAConfirmedSelection($candidateID);

            if ($candidateExists) {
                $notAvailableCandidates[] = [
                    'id' => $candidateID,
                    'fullName' => $this->getFullName($candidateID),
                    'program' => $this->program($candidateID),
                ];
            }
        }

        $dto = new RequestResponseDto(RequestStatus::FAILED, $notAvailableCandidates);

        return count($notAvailableCandidates) ? $dto->data() : [];
    }

    public function purgeOldRoommates($request)
    {
        //grab all existing roommates
        $existingRoommates = $this->requestsService->getRequesterSelectedRoommates($request->requester)->toArray();

        foreach ($existingRoommates as $existingRoommate) {
            if (!in_array($existingRoommate, $request->roommates)) {
                $this->requestsService->deleteRequestCandidate($request->requester, $existingRoommate);
            }
        }
    }

    public function candidateDTOs($requestCandidates)
    {
        //preparing response
        $candidates = [];
        foreach ($requestCandidates as $candidate) {
            $dto = new RequestRoommateDto($candidate);
            $candidates[] = $dto->data();
        }

        return $candidates;
    }

    public function addFullNameToRoommates($roommates)
    {
        $__roommates = [];

        //loop through over the roommates and add their names
        foreach ($roommates as $roommate) {
            $__roommates[] = [
                "id" => $roommate->student_id,
                'fullName' => $this->getFullName($roommate->student_id),
            ];
        }

        return $__roommates;
    }
}
