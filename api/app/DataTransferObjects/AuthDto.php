<?php

namespace App\DataTransferObjects;

use App\Traits\Utils;
use Illuminate\Support\Str;

class AuthDto
{
    public $studentID;
    public $fullName;
    public $token;

    use Utils;

    public function __construct($studentID, $fullName, $token)
    {

        $this->studentID = $studentID;
        $this->fullName = $fullName;
        $this->token = $token;
    }

    public function data()
    {
        return [
            "studentNumber" => $this->studentID,
            "fullName" => $this->fullName,
            "token" => $this->token
        ];
    }
}
