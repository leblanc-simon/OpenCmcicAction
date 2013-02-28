<?php

namespace OpenCmcicAction\Exception;

class Recouvrement extends Response
{
    public function __construct($message = '', $code = 3, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}