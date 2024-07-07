<?php

namespace App\DataTransferObjects;

use App\Traits\Utils;

class HasRoomRequestDto
{

    use Utils;

    public function __construct(public $status, public $studentID,  public $room, public $roommates)
    {
    }

    public function data()
    {
        return [
            'status' => $this->status,
            'name' => $this->getFullName($this->studentID),
            'room' => $this->room,
            'roommates' => $this->roommates
        ];
    }
}
