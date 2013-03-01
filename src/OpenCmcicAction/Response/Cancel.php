<?php

namespace OpenCmcicAction\Response;

use OpenCmcicAction\Exception\Exception;
use OpenCmcicAction\Exception\Cancel as ECancel;

class Cancel extends Response
{
    private $datas = array();
    
    public function check()
    {
        if ($this->hasError() === true) {
            throw new Exception($this->getError());
        }
        
        $response = $this->getResponse();
        
        $results = explode(chr(10), $response);
        
        
        foreach ($results as $result) {
            $row = explode('=', $result);
            
            if (count($row) === 2) {
                if ($row[0] === 'reference') {
                    $this->datas['reference'] = $row[1];
                } elseif ($row[0] === 'cdr') {
                    $this->datas['cancel'] = (int)$row[1];
                } elseif ($row[0] === 'lib') {
                    $this->datas['label'] = $row[1];
                }
            }
        }
        
        if (isset($this->datas['cancel']) === true && $this->datas['cancel'] === 1) {
            return true;
        }
        
        throw new ECancel('Fail to cancel'.$response);
    }
    
    public function getDatas()
    {
        return $this->datas;
    }
}