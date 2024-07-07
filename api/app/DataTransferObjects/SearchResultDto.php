<?php

namespace App\DataTransferObjects;

use App\Constants\RequestsConstants;

class SearchResultDto
{
    public $studentId;
    public $fullName;
    public $program;
    public $available;

    public function __construct($studentId, $fullName, $program, $available = RequestsConstants::NOT_AVAILABLE)
    {
        $this->studentId = $studentId;
        $this->fullName = $fullName;
        $this->program = $program;
        $this->available = $available;
    }

    public function data()
    {
        return   [
            'id' => $this->studentId,
            'fullName' => $this->fullName,
            'program' => $this->program,
            'available' => $this->available
        ];
    }
}
