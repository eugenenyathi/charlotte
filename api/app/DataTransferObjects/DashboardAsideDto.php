<?php

namespace App\DataTransferObjects;

use App\Traits\Utils;
use Illuminate\Support\Str;

class DashboardAsideDto
{
    public $hostelFees;
    public $checkInOut;
    public $previousTimeStamp;

    use Utils;

    public function __construct($hostelFees, $checkInOut, $previousTimeStamp)
    {

        $this->hostelFees = $hostelFees;
        $this->checkInOut = $checkInOut;
        $this->previousTimeStamp = $previousTimeStamp;
    }

    public function data()
    {
        return [
            "hostelFees" => $this->hostelFees,
            "checkIn" => $this->checkInOut['checkIn'],
            "checkOut" => $this->checkInOut['checkOut'],
            "timestamp" => $this->previousTimeStamp
        ];
    }
}
