<?php

namespace App\DataTransferObjects;

use App\Constants\StudentConstants;

class ResidenceDto
{
    private $studentID;
    private $gender;
    private $isResCurrentResidence;
    private $residence;

    public $hostel;
    public $roomLocation;
    private $roomWing;
    private $roomFloorSide;


    public function __construct($studentID, $gender, $isResCurrentResidence, $residence, $roomLocation = null)
    {
        $this->studentID = $studentID;
        $this->gender = $gender;
        $this->isResCurrentResidence = $isResCurrentResidence;
        $this->residence = $residence;
        $this->roomLocation = $roomLocation;
    }

    public function data()
    {
        $this->roomSpecifics();

        return [
            "isResCurrentResidence" => $this->isResCurrentResidence,
            "hostel" => $this->hostel,
            "floor" => $this->roomLocation->floor,
            "wing" => $this->roomWing,
            "floorSide" => $this->roomFloorSide,
            "room" => $this->residence->room,
            "part" => $this->residence->part,
        ];
    }

    public function roomSpecifics()
    {

        switch ($this->gender) {
            case StudentConstants::FEMALE:
                $this->hostel = "girls";
                $this->roomWing = $this->roomLocation->girls_wing;
                $this->roomFloorSide = $this->roomLocation->girls_floor_side;
                break;
            case StudentConstants::MALE:
                $this->hostel = "boys";
                $this->roomWing = $this->roomLocation->boys_wing;
                $this->roomFloorSide = $this->roomLocation->boys_floor_side;
                break;
        }
    }
}
