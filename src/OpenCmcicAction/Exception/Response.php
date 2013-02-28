<?php

namespace OpenCmcicAction\Exception;

class Response extends Exception
{
    public function __construct($message = '', $code = 2, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}