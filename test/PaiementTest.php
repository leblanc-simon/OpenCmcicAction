<?php

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.test.php';

class PaiementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers  \OpenCmcicAction\Request\Paiement::process
     * @covers  \OpenCmcicAction\Response\Paiement::check
     */
    public function testPaiement()
    {
        $assert = array(
            array(
                "reference" => "TE1362089387",
                "date" => "2013-02-28T23:09:47Z",
                "amount" => "10",
                "currency" => "EUR",
            ),
            array(
                "reference" => "EI1362093746",
                "date" => "2013-03-01T00:22:26Z",
                "amount" => "10",
                "currency" => "EUR",
            ),
        );

        $request = new OpenCmcicAction\Request\Paiement();
        
        $paiements = $request->process();
        
        $this->assertCount(count($assert), $paiements, 'Number of paiement fail');
        $this->assertEquals($assert, $paiements);
    }
}