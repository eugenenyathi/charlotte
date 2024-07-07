<?php

namespace App\DataTransferObjects;

use App\Traits\Utils;

class RequestRoommateDto
{

    use Utils;

    public function __construct(public $roommate)
    {
    }

    public function data()
    {
        $fullName = $this->getFullName($this->roommate->selected_roommate);

        return [
            "id" => $this->roommate->selected_roommate,
            'fullName' => $fullName,
            "program" => $this->program($this->roommate->selected_roommate),
            "response" => $this->roommate->selection_confirmed
        ];
    }
}
