<?php
/**
 * This file is part of the OpenCmcicAction package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenCmcicAction\Core;


/**
 * Config class
 *
 * @package     OpenCmcicAction\Core
 * @version     1.0.0
 * @license     http://opensource.org/licenses/MIT  MIT
 * @author      Simon Leblanc <contact@leblanc-simon.eu>
 */
class Config
{
    static private $datas = array();
    
    /**
     * Add an array in the configuration
     *
     * @param   array   $datas  The configuration to add
     * @access  public
     * @static
     */
    static public function add(array $datas)
    {
        self::$datas = array_merge(self::$datas, $datas);
    }
    
    
    /**
     * Add a variable in the configuration
     *
     * @param   string  $name       the name of the configuration variable
     * @param   mixed   $value      the value of the configuration variable
     * @param   bool    $replace    true : replace the active configuration if already exists, false else
     * @return  bool                true : the configuration is set, false else
     * @access  public
     * @static
     */
    static public function set($name, $value, $replace = true)
    {
        if ($replace === true || isset(self::$datas[$name]) === false) {
            self::$datas[$name] = $value;
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Get the value of the configuration
     *
     * @param   string  $name       the name of the configuration you want the value
     * @param   mixed   $default    the default value if the configuration doesn't exist
     * @return  mixed               the value of the configuration (if it exists, else the default value)
     * @access  public
     * @static
     */
    static public function get($name, $default = null)
    {
        return isset(self::$datas[$name]) === false ? $default : self::$datas[$name];
    }
}