<?php

namespace App\DataTransferObjects;

use App\Traits\VUtils;

class CreateRequestCandidateDto
{
    public $requesterID;
    public $selectedRoommate;
    public $studentType;
    public $gender;

    use VUtils;

    public function __construct($requesterID, $selectedRoommate, $gender)
    {
        $this->requesterID = $requesterID;
        $this->selectedRoommate = $selectedRoommate;
        $this->studentType = $this->getActiveStudentType();
        $this->gender = $gender;
    }
}
