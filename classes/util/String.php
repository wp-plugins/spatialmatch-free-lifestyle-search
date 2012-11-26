<?php

class SpatialMatch_Util_String
{
    static $__numberRegEx = null;
    
    static function truncate ($s, $length)
    {        
        $s = esc_html($s);
        
        if (strlen($s) > $length)
        {
            $s = substr($s, 0, $length) . '...';
        }
        
        return $s;
    }
    
    static function isNumber ($s)
    {
        if (self::$__numberRegEx == null)
        {
            self::$__numberRegEx = '/^\d+$/';
        }
    
        return preg_match(self::$__numberRegEx, $s);
    }
    
    static function toBoolean ($s)
    {
        if (!isset($s) || ($s === false))
        {
            return false;
        }
        
        if ($s === true)
        {
            return true;
        }
        
        $s = strtolower(trim($s));
        
        if (($s == 'true') || ($s == 'yes') || ($s == 'on'))
        {
            return true;
        }
        
        return false;
    }
}