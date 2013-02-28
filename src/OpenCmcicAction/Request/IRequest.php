<?php

namespace OpenCmcicAction\Request;

interface IRequest
{
    public function process();
    public function check();
}