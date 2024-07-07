<?php

namespace App\Http\Controllers;

use App\Traits\Utils;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Constants\StudentConstants;
use App\DataTransferObjects\StudentProfileDto;
use App\Http\Services\UtilsService;

class UtilsController extends Controller
{
    use HttpResponses;
    use Utils;

    public function __construct(private UtilsService $utilsService)
    {
    }

    public function randomStudent()
    {
        //get student numbers
        $conStudentIDs = $this->utilsService->getStudentIdsByStudentType(StudentConstants::CON_STUDENT);
        $blockStudentIDs = $this->utilsService->getStudentIdsByStudentType(StudentConstants::BLOCK_STUDENT);

        //get random student id
        $conStudentID = $conStudentIDs[$this->random(0, count($conStudentIDs) - 1)];
        $blockStudentID = $blockStudentIDs[$this->random(0, count($blockStudentIDs) - 1)];

        $conStudent = $this->utilsService->getStudentProfile($conStudentID->student_id);
        $blockStudent = $this->utilsService->getStudentProfile($blockStudentID->student_id);

        $data = [
            'Conventional student' => $conStudent,
            'Block student' => $blockStudent,
        ];

        return $this->sendData($data);
    }

    public function studentProfile($studentID)
    {
        $profile = $this->utilsService->getStudentProfile($studentID);
        $dto = new StudentProfileDto($profile);

        return $this->sendData($dto->data());
    }

    protected function studentSpread()
    {
        $con = $this->utilsService->countStudentType(StudentConstants::CON_STUDENT);
        $block = $this->utilsService->countStudentType(StudentConstants::BLOCK_STUDENT);

        $exclude = [];

        foreach (StudentConstants::STUDENT_TYPE as $studentType) {
            $exclude[] = $this->utilsService->studentSpread($studentType);
        }


        $data = [
            'Conventional' => $con,
            'Block' => $block,
            'Excluding Commercials' => [
                'cons' => $exclude[0],
                'block' => $exclude[1],
            ]
        ];

        return $this->sendData($data);
    }
}
