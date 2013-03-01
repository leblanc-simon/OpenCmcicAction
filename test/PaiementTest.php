<?php

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.test.php';

class CancelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers  \OpenCmcicAction\Request\Cancel::process
     * @covers  \OpenCmcicAction\Response\Cancel::check
     */
    public function testPaiement()
    {
        $paimeent = new OpenCmcicAction\Request\Paiement();
        
        $response = $paimeent->process();
    }
}