<?php

class SpatialMatch_Util_Validator
{
    private static $licenseKeyRegEx;
    
    public static function isValidLicenseKey($value)
    {
        if (!isset(self::$licenseKeyRegEx))
        {
            self::$licenseKeyRegEx = '/^[A-Z0-9]{4}\-[A-Z0-9]{4}\-[A-Z0-9]{4}(\-[A-Z0-9]{4})?$/';
        }
        
        return (preg_match(self::$licenseKeyRegEx, $value) != 0);
    }
}
