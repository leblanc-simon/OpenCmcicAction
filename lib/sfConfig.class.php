<?php

class sfConfig
{
  private static $datas = array();
  
  
  public static function get($name, $default = null)
  {
    return isset(self::$datas[$name]) ? self::$datas[$name] : $default;
  }
  
  
  public static function set($name, $value)
  {
    self::$datas[$name] = $value;
  }
}