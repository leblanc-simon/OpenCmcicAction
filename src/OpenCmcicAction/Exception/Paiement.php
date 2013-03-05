<?php

namespace OpenCmcicAction\Exception;

class Paiement extends Response
{
    public function __construct($message = '', $code = 5, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}