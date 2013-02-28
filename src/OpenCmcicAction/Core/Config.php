<?php

namespace OpenCmcicAction\Core;

/**
 * 
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