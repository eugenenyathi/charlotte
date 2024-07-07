<?php

namespace App\Http\Controllers;

use App\Traits\Utils;
use App\Models\Requester;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\RequestCandidate;
use Illuminate\Support\Facades\DB;
use App\Constants\RequestsConstants;
use App\Constants\RoommatePreferenceConstants;
use App\Http\Services\SearchService;
use App\Http\Requests\ValidateStudentID;
use App\Constants\SearchConstants;
use App\DataTransferObjects\SearchResultDto;
use App\Http\Services\RoommatePreferenceService;

class SearchController extends Controller
{
    use HttpResponses;
    use Utils;

    public function __construct(private SearchService $searchService, private RoommatePreferenceService $roommatePreferenceService)
    {
    }

    public function search(ValidateStudentID $request, $search_query)
    {
        $request->validated();

        /* Step 1: Get the current student details */
        $student = $this->searchService->getStudentDetails($request->studentID);

        $whatSearch = $this->__whatSearch($student);
        $searchResults = [];

        /* 2. Query the students table based on the determined search type */
        switch ($whatSearch) {
            case SearchConstants::SPECIAL_SEARCH:
                $searchResults = $this->specialSearch($search_query, $student);
                break;
            case SearchConstants::NORMAL_SEARCH:
                $searchResults = $this->searchService->searchQuery($student, SearchConstants::NORMAL_SEARCH, $search_query);
                break;
        }

        /* Step 3: differentiate the students that are already chosen by others from the rest */
        $filteredResults = $this->sortStudents($searchResults);

        /* Step 3: send back the results */
        return $this->sendData($filteredResults);
    }

    public function match($studentID)
    {
        //1. check if the students roommate preferences are set!
        $roommatePreferenceExists = $this->roommatePreferenceService->roommatePreferenceExists($studentID);

        if (!$roommatePreferenceExists) return $this->sendData(['preference_set' => false]);

        $roommatePreferences = $this->roommatePreferenceService->getRoommatePreferences($studentID);
        $student = $this->searchService->getStudentDetails($studentID);
        $whatSearch = $this->__whatSearch($student);
        $searchResults = [];


        switch ($whatSearch) {
            case SearchConstants::SPECIAL_SEARCH:
                $searchResults = $this->specialSearch('L0', $student);
                break;
            case SearchConstants::NORMAL_SEARCH:
                $searchResults = $this->searchService->searchQuery($student, SearchConstants::NORMAL_SEARCH, 'L0');
                break;
        }

        //2. We want to get all students that match both preferences
        $matchingRoommates = $this->getMatchingRoommates($searchResults, $roommatePreferences);
        $filteredMatchingRoommates = $this->sortStudents($matchingRoommates);

        return $this->sendData(['preference_set' => true, 'matchingRoommates' => $filteredMatchingRoommates]);
    }

    private function specialSearch($search_query, $student)
    {
        /*
        If the student is a part three and the program is in the exceptions 
        then expand the search index to part 4's
        */

        $programExceptions = SearchConstants::EXCEPTIONS;
        $searchResults = $this->searchService->searchQuery($student, SearchConstants::SPECIAL_SEARCH, $search_query,);

        //remove all part 3's whose program is not in the exception
        return $searchResults->filter(function ($student) use ($programExceptions) {
            $studentPart = $this->part($student->student_id);
            if ($studentPart == 3.1 || $studentPart == 3.2) {
                $studentProgramID = $this->programID($student->student_id);
                return in_array($studentProgramID, $programExceptions);
            }
            return true;
        });
    }

    private function __whatSearch($student)
    {

        if ($student->part == 3.1 || $student->part == 3.2) {
            $programExceptions = SearchConstants::EXCEPTIONS;
            $studentProgramID = $this->programID($student->student_id);

            if (in_array($studentProgramID, $programExceptions)) {
                return SearchConstants::SPECIAL_SEARCH;
            }
        }

        return SearchConstants::NORMAL_SEARCH;
    }

    private function getMatchingRoommates($potentialRoommates, $roommatePreferences)
    {
        return $potentialRoommates->filter(function ($potentialRoommate) use ($roommatePreferences) {
            return $potentialRoommate->question_1 == $roommatePreferences->question_1
                && $potentialRoommate->question_2 == $roommatePreferences->question_2;
        });
    }

    private function sortStudents($searchResults)
    {
        $filteredResults = [];

        foreach ($searchResults as $student) {

            $searchResult = new SearchResultDto($student->student_id, $student->fullName, $student->program);

            //check if the student has made a request
            $studentHasRoomRequest = $this->searchService->doesStudentHaveARoomRequest($student->student_id);

            if ($studentHasRoomRequest) {

                $isRequester = $this->searchService->isStudentARoomRequester($student->student_id);

                switch ($isRequester) {
                    case true:
                        $searchResult->available = RequestsConstants::NOT_AVAILABLE;
                        break;
                    case false:
                        //check if the student has already confirmed yes:
                        $hasConfirmed = $this->searchService->hasStudentConfirmedSelection($student->student_id);
                        $searchResult->available = $hasConfirmed ? RequestsConstants::NOT_AVAILABLE : RequestsConstants::AVAILABLE;
                        break;
                }
            } else $searchResult->available =  RequestsConstants::AVAILABLE;


            $filteredResults[] = $searchResult->data();
        }

        return $filteredResults;
    }
}
