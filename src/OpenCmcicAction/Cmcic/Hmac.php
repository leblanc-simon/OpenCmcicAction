<?php
/**
 * This file is part of the OpenCmcicAction package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenCmcicAction\Cmcic;


/**
 * Hmac generator class
 *
 * @package     OpenCmcicAction\Cmcic
 * @version     1.0.0
 * @license     http://opensource.org/licenses/MIT  MIT
 * @author      Simon Leblanc <contact@leblanc-simon.eu>
 */
class Hmac
{
    private $_sUsableKey;   // The usable TPE key
    
    
    public function __construct(Tpe $oTpe)
    {
        $this->_sUsableKey = $this->_getUsableKey($oTpe);
    }
    
    
    /**
     * Return the key to be used in the hmac function
     */
    private function _getUsableKey(Tpe $oTpe)
    {
        $hexStrKey  = substr($oTpe->getKey(), 0, 38);
        $hexFinal   = "" . substr($oTpe->getKey(), 38, 2) . "00";
        $cca0=ord($hexFinal);
        
        if ($cca0>70 && $cca0<97) {
            $hexStrKey .= chr($cca0-23) . substr($hexFinal, 1, 1);
        } else {
            if (substr($hexFinal, 1, 1)=="M") {
                $hexStrKey .= substr($hexFinal, 0, 1) . "0";
            } else {
                $hexStrKey .= substr($hexFinal, 0, 2);
            }
        }
        
        return pack("H*", $hexStrKey);
    }
    
    
    /**
     * Return the HMAC for a data string
     */
    public function computeHmac($sData)
    {
        return strtolower(hash_hmac("sha1", $sData, $this->_sUsableKey));
    }
    
    
    /**
     * RFC 2104 HMAC implementation for PHP >= 4.3.0 - Creates a SHA1 HMAC.
     * Eliminates the need to install mhash to compute a HMAC
     * Adjusted from the md5 version by Lance Rushing .
     */
    public function hmac_sha1 ($key, $data)
    {
        $length = 64; // block length for SHA1
        if (strlen($key) > $length) { $key = pack("H*",sha1($key)); }
        $key  = str_pad($key, $length, chr(0x00));
        $ipad = str_pad('', $length, chr(0x36));
        $opad = str_pad('', $length, chr(0x5c));
        $k_ipad = $key ^ $ipad ;
        $k_opad = $key ^ $opad;
        
        return sha1($k_opad  . pack("H*",sha1($k_ipad . $data)));
    }
}