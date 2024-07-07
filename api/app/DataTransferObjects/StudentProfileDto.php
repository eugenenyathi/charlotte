<?php

namespace App\DataTransferObjects;

use App\Traits\Utils;
use Illuminate\Support\Str;

class StudentProfileDto
{
    use Utils;

    public function __construct(public $profile)
    {
    }

    public function data()
    {
        return [
            'studentNumber' => $this->profile->student_id,
            'fullName' => $this->profile->fullName,
            'faculty' => $this->profile->faculty,
            'program' => $this->profile->program,
            'studentType' => $this->profile->student_type,
            'part' => $this->profile->part,
            'enrolled' => $this->profile->enrolled,
            'timestamp' => $this->timestamp($this->profile->student_id)
        ];
    }
}
