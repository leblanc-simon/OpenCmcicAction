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

use OpenCmcicAction\Exception\Exception;
use OpenCmcicAction\Exception\Recouvrement as ERecouvrement;


/**
 * Recouvrement response class
 *
 * @package     OpenCmcicAction\Response
 * @version     1.0.0
 * @license     http://opensource.org/licenses/MIT  MIT
 * @author      Simon Leblanc <contact@leblanc-simon.eu>
 */
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