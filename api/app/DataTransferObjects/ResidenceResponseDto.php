<?php

namespace App\DataTransferObjects;


class ResidenceResponseDto
{
    public $currentRes;
    public $previousRes;

    public function __construct($currentRes, $previousRes)
    {
        $this->currentRes = $currentRes;
        $this->previousRes = $previousRes;
    }

    public function data()
    {
        return $this->residences();
    }

    public function residences()
    {
        if ($this->currentRes)
            $residences = [$this->currentRes];
        else
            $residences = [];

        if (!count($this->previousRes)) return $residences;

        foreach ($this->previousRes as $res) {
            $residences[] = $res;
        }

        return $residences;
    }
}
