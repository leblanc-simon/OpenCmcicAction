<?php

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.test.php';

class CancelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers  \OpenCmcicAction\Request\Cancel::process
     * @covers  \OpenCmcicAction\Response\Cancel::check
     */
    public function testCancel()
    {
        $cancel = new OpenCmcicAction\Request\Cancel('EI1362093746', 10, 10, '2013-03-01 00:22:26');
        
        $response = $cancel->process();
        
        $this->assertTrue($response->check(), 'Cancel fail');
    }
    
    /**
     * @covers  \OpenCmcicAction\Request\Cancel::process
     * @covers  \OpenCmcicAction\Response\Cancel::check
     * @expectedException \OpenCmcicAction\Exception\Cancel
     * @expectedExceptionMessage Fail to cancel
     * @expectedExceptionCode 4
     */
    public function testCancelFail()
    {
        $cancel = new OpenCmcicAction\Request\Cancel('AA00000000', 10, 10, '2013-02-28 23:09:47');
        
        $response = $cancel->process();
        $response->check();
    }
}