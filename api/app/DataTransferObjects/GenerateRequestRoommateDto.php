<?php

namespace App\DataTransferObjects;

use App\Constants\SelectionResponse;
use App\Traits\Utils;
use App\Traits\VUtils;

class GenerateRequestRoommateDto
{

    use Utils;
    use VUtils;

    private $activeStudentType;

    public function __construct(public $requesterId, public $selectedRoommateId, public $gender)
    {
        $this->activeStudentType = $this->getActiveStudentType();
    }

    public function data()
    {
        return [
            'requester_id' => $this->requesterId,
            'selected_roommate' => $this->selectedRoommateId,
            'student_type' => $this->activeStudentType,
            'selection_confirmed' => '',
            'gender' => $this->gender
        ];
    }
}
