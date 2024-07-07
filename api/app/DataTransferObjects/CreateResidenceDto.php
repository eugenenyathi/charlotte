<?php

namespace App\DataTransferObjects;

use App\Constants\HostelConstants;
use App\Constants\StudentConstants;
use App\Traits\Utils;
use App\Traits\VUtils;

class CreateResidenceDto
{
    public $studentType = 'con';
    public $studentID;
    public $part;
    public $hostel;
    public $room;

    use VUtils;
    use Utils;

    public function __construct($studentID, $room, $studentGender)
    {
        $this->studentID = $studentID;
        $this->studentType = $this->getActiveStudentType();
        $this->part = $this->part($studentID);
        $this->hostel = $studentGender == StudentConstants::FEMALE ? HostelConstants::GIRLS_HOSTEL : HostelConstants::BOYS_HOSTEL;
        $this->room = $room;
    }
}
