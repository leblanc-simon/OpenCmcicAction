<?php

namespace OpenCmcicAction\Response;

class Response
{
    private $response = null;
    private $curl = null;
    
    private $datas = null;
    private $error = null;
    
    
    public function __construct($response, $curl)
    {
        $this->response = $response;
        $this->curl = $curl;
        
        $this->process();
    }
    
    public function __destruct()
    {
        if ($this->curl !== null && is_resource($this->curl) === true) {
            curl_close($this->curl);
        }
    }
    
    
    protected function process()
    {
        $has_error = curl_errno($this->curl);
        if ($has_error) {
            $this->error = curl_error($this->curl);
            return;
        }
        
        $this->datas = curl_getinfo($this->curl);
    }
    
    public function hasError()
    {
        return !($this->error === null);
    }
    
    public function getError()
    {
        return $this->error;
    }
    
    public function getDatas()
    {
        return $this->datas;
    }
    
    public function getResponse()
    {
        return $this->response;
    }
}