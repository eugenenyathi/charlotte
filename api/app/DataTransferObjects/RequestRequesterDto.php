<?php

namespace App\DataTransferObjects;

use App\Constants\SelectionResponse;
use App\Traits\Utils;

class RequestRequesterDto
{

    use Utils;

    public function __construct(public $requesterId)
    {
    }

    public function data()
    {
        $fullName = $this->getFullName($this->requesterId);

        return [
            "id" => $this->requesterId,
            'fullName' => $fullName,
            "program" => $this->program($this->requesterId),
            "gender" => $this->gender($this->requesterId),
            "response" => SelectionResponse::YES
        ];
    }
}
