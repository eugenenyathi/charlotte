<?php

namespace App\Http\Helpers;

use App\DataTransferObjects\ResidenceDto;
use App\Http\Services\HomeService;

class HomeHelpers
{

    public function __construct(private HomeService $homeService)
    {
    }

    public function res_data($studentID, $gender, $isResCurrentResidence,  $residence)
    {
        $dto = new ResidenceDto($studentID, $gender, $isResCurrentResidence, $residence);
        $dto->roomLocation = $this->homeService->roomSpecifics($residence->hostel, $residence->room);
        return $dto->data();
    }

    public function formatPreviousResData($studentID, $gender, $previousRes)
    {
        $previousResData = [];

        if (!count($previousRes)) return $previousRes;

        foreach ($previousRes as $res) {
            $previousResData[] = $this->res_data($studentID, $gender, false,  $res);
        }

        return $previousResData;
    }
}
