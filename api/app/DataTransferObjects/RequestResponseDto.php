<?php

namespace App\DataTransferObjects;


class RequestResponseDto
{

    public function __construct(public $status, public $roommates)
    {
    }

    public function data()
    {
        return [
            'status' => $this->status,
            'roommates' => $this->roommates
        ];
    }
}
