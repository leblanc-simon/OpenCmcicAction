<?php
/**
 * This file is part of the OpenCmcicAction package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenCmcicAction\Response;


/**
 * Base response class
 *
 * @package     OpenCmcicAction\Response
 * @version     1.0.0
 * @license     http://opensource.org/licenses/MIT  MIT
 * @author      Simon Leblanc <contact@leblanc-simon.eu>
 */
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