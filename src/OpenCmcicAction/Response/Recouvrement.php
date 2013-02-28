<?php

namespace OpenCmcicAction\Response;

use OpenCmcicAction\Exception\Recouvrement as ERecouvrement;

class Recouvrement extends Response
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
                    $this->datas['recouvrement'] = (int)$row[1];
                } elseif ($row[0] === 'lib') {
                    $this->datas['label'] = $row[1];
                }
            }
        }
        
        if (isset($this->datas['recouvrement']) === true && $this->datas['recouvrement'] === 1) {
            return true;
        }
        
        throw new ERecouvrement('Fail to recouvrement');
    }
    
    public function getDatas()
    {
        return $this->datas;
    }
}