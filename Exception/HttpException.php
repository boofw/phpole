<?php namespace Polev\Phpole\Exception;

use Exception;

class HttpException extends Exception
{
    public function __construct ($message = null, $code = null, $previous = null)
    {
        $this->code = $message;
    }
}