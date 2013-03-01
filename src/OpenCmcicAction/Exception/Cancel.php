<?php

namespace OpenCmcicAction\Exception;

class Cancel extends Response
{
    public function __construct($message = '', $code = 4, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}