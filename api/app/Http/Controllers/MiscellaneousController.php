<?php

namespace App\Http\Controllers;

use App\Models\Residence;
use App\Constants\StudentConstants;
use App\Models\RequestCandidate;
use App\Http\Services\GenerateRequestsService;
use App\Models\BoysHostel;
use App\Models\GirlsHostel;
use App\Traits\HttpResponses;
use App\Traits\VUtils;

class MiscellaneousController extends Controller
{

    use VUtils;
    use HttpResponses;

    private $activeStudentType;

    public function __construct(private GenerateRequestsService $requestsService)
    {
        $this->activeStudentType = $this->getActiveStudentType();
    }

    public function fakeRes()
    {
        $students = ["L0481923M", "L0492368S", "L0521648T"];
        $room = 351;
        $hostel = 'suburb';

        foreach ($students as $student) {
            Residence::create([
                'student_id' => $student,
                'part' => $this->part($student),
                'hostel' => $hostel,
                'room' => $room,
                // !!!! REMOVE THIS !!!!!!
                'roommates' => 3
            ]);
        }


        switch ($hostel) {
            case 'girls_hostel':
                GirlsHostel::where('room_number', $room)
                    ->update(['occupied' => 'Yes']);


            case 'boys_hostel':
                BoysHostel::where('room_number', $room)
                    ->update(['occupied' => 'Yes']);
        }


        return $this->sendResponse("Done");
    }

    public function pullStudents()
    {

        $gender = 'Female';
        $level = 2.2;

        $pool = $this->requestsService->genderFirstSameLevelStudents($gender, $level);

        $students = $this->requestsService->freeMates($pool);


        return $this->sendData($students);
    }

    public function count()
    {

        $wonderers = [];
        $males = [];
        $females = [];

        foreach (StudentConstants::GENDER as $gender) {
            foreach (StudentConstants::LEVELS as $level) {
                $students = $this->requestsService->genderFirstSameLevelStudents($gender, $level, $this->activeStudentType);

                // dd($students);

                if (!$students) continue;

                foreach ($students as $student) {

                    $studentExists = RequestCandidate::where('requester_id', $student->student_id)
                        ->orWhere('selected_roommate', $student->student_id)->exists();

                    if (!$studentExists) $wonderers[] = $student->student_id;
                }
            }
        }

        foreach ($wonderers as $student) {
            $studentInLoop = $this->studentProfile($student);

            if ($studentInLoop['gender'] === 'Female') $females[] = $studentInLoop;
            else $males[] = $studentInLoop;
        }

        $profiles = [
            'females' => $females,
            'males' => $males
        ];

        // return $this->sendData($wonderers);
        return $this->sendData($profiles);
    }

    public function countGenderFirstSameLevelStudents($level)
    {

        $data = [
            [
                'gender' => 'Females',
                'number' => count($this->requestsService->genderFirstLevelStudents('Female', $level)),
            ],            [
                'gender' => 'Males',
                'number' => count($this->requestsService->genderFirstLevelStudents('Male', $level)),
            ]

        ];

        return $this->sendData($data);
    }

    public function stats()
    {
        $data = [
            'Processed' => $this->requestsService->requestProcessedStatus($this->activeStudentType, 'Yes'),
            'Not processed' => $this->requestsService->requestProcessedStatus($this->activeStudentType, 'No'),
            'Unprocessed students' => $this->requestsService->getStudentWithUnprocessedRequests($this->activeStudentType)
        ];

        return $this->sendData($data);
    }


    public function clearAll()
    {
        $this->requestsService->clearAll();
        return $this->sendResponse('Done.');
    }

    public function reverseProcessedRequests()
    {

        $this->requestsService->reverseProcessedRequests();
        return $this->sendResponse('Reversing Done.');
    }

    public function clearRooms()
    {
        $this->requestsService->clearRooms();
        return $this->sendResponse('Rooms cleared');
    }


    public function destroyAll()
    {
        $this->requestsService->destroyAll();
        return $this->sendResponse('Wipe done');
    }
}
