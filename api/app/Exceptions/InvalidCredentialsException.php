<?php

namespace App\Exceptions;

use Exception;
use App\Traits\HttpResponses;

class InvalidCredentialsException extends Exception
{
    use HttpResponses;

    public function report()
    {
    }

    public function render()
    {
        return $this->sendError('Invalid Credentials', 401);
    }
}
