<?php

namespace Fleetfoot\OTP\Exceptions;

use Exception;

class MaxAllowedAttemptsExceededException extends Exception
{
    public function __contruct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
