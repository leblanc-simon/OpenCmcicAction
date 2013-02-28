<?php

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.test.php';

class RecouvrementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers  \OpenCmcicAction\Request\Recouvrement::process
     * @covers  \OpenCmcicAction\Response\Recouvrement::check
     */
    public function testRecouvrement()
    {
        $recouvrement = new OpenCmcicAction\Request\Recouvrement('EI1362093746', 10, '2013-03-01 00:22:26');
        
        $response = $recouvrement->process();
        
        $this->assertTrue($response->check(), 'Recouvrement fail');
    }
    
    /**
     * @covers  \OpenCmcicAction\Request\Recouvrement::process
     * @covers  \OpenCmcicAction\Response\Recouvrement::check
     * @expectedException \OpenCmcicAction\Exception\Recouvrement
     * @expectedExceptionMessage Fail to recouvrement
     * @expectedExceptionCode 3
     */
    public function testRecouvrementFail()
    {
        $recouvrement = new OpenCmcicAction\Request\Recouvrement('AA00000000', 10, '2013-02-28 23:09:47');
        
        $response = $recouvrement->process();
        $response->check();
    }
}