<?php

namespace App\Exceptions;

use Exception;
use App\Traits\HttpResponses;

class UnauthorizedAccessException extends Exception
{
    use HttpResponses;

    public function report()
    {
    }

    public function render()
    {
        return $this->sendError('Unauthorized Access');
    }
}
