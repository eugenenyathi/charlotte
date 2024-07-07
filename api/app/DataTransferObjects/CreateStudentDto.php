<?php

namespace App\DataTransferObjects;

use App\Traits\VUtils;

class CreateStudentDto
{
    public $studentType = 'con';
    public $studentID;
    public $part;
    public $hostel;
    public $room;

    use VUtils;

    public function __construct($studentID, $part, $hostel, $room)
    {

        $this->studentID = $studentID;
        $this->studentType =
            $this->getActiveStudentType();
        $this->part = $part;
        $this->hostel = $hostel;
        $this->room = $room;
    }
}
