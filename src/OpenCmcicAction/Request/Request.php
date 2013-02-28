<?php

namespace OpenCmcicAction\Request;

use OpenCmcicAction\Core\Config;
use OpenCmcicAction\Core\Exception;

abstract class Request
{
    private $handle_stderr = false;
    
    public function formatDate(\DateTime $date)
    {
        return $date->format('d/m/Y:H:i:s');
    }
    
    protected function send($url, $datas, $class_name = 'Response')
    {
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_URL, Config::get('cmcic_server').$url);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        
        if (Config::get('log_dir', null) !== null) {
            $this->handle_stderr = fopen(Config::get('log_dir').DIRECTORY_SEPARATOR.'cmcic_request_stderr_'.date('Y-m-d').'.log', 'ab');
            if (is_resource($this->handle_stderr) === true) {
                curl_setopt($curl, CURLOPT_STDERR, $this->handle_stderr);
            }
        }
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        
        $response = curl_exec($curl);
        
        $class_name = '\\OpenCmcicAction\\Response\\'.$class_name;
        
        return new $class_name($response, $curl);
    }
    
    public function __destruct()
    {
        if (is_resource($this->handle_stderr) === true) {
            fclose($this->handle_stderr);
        }
    }
}