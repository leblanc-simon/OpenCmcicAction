<?php
/**
 * This file is part of the OpenCmcicAction package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.test.php';

class PaiementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers  \OpenCmcicAction\Request\Paiement::process
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