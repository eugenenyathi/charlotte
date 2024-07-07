<?php

namespace App\Http\Controllers;

use App\Constants\SelectionResponse;
use App\Http\Services\GenerateRequestsService;
use App\Http\Services\RequestsService;
use App\Traits\Utils;
use App\Models\Profile;
use App\Models\Student;
use App\Models\Tuition;
use App\Models\Requests;
use App\Models\Requester;
use App\Models\Residence;
use App\Models\RoomRange;
use App\Models\CheckInOut;
use Illuminate\Support\Str;
use App\Models\OldResidence;
use App\Traits\ProgramsMall;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\SearchException;
use App\Traits\FakeCredentials;
use App\Models\RequestCandidate;
use App\Models\ActiveStudentType;
use App\Traits\VUtils;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    use HttpResponses;
    use FakeCredentials;
    use Utils;
    use VUtils;

    private $hostel;
    private $room;
    private $side;
    private $floor;
    private $floorSide;

    private $roomsWithLessStudents;
    private $roomsWithoutStudents;


    public function __construct(public RequestsService $requestsService, public GenerateRequestsService $generateRequestsService)
    {
    }


    public function index($studentID)
    {
        $data = $this->requestsService->getRequesterRoommates('L0023986O');

        return $this->sendData($data);
    }

    public function randomizedPreferences()
    {
        $preferences = [];
        $responses = ["yes", "no"];

        while (true) {
            if (count($preferences) === 3) break;
            $preferences[] = $responses[$this->random(0, 1)];
        }

        return $preferences;
    }

    public function findDuplicateConfirmations()
    {
        $students = RequestCandidate::where('selection_confirmed', SelectionResponse::YES)->get();

        return $students->filter(function ($student) {
            return RequestCandidate::where('selected_roommate', $student->selected_roommate)
                ->whereNot('requester_id', $student->requester_id)
                ->where('selection_confirmed', SelectionResponse::YES)
                ->exists();
        });
    }
}
