<?php

class SpatialMatch_Manager_LicenseKeys
{
    const ID = 'spatialmatch-license-keys';
    
    private static $initialized = false;
    
    public static function initialize()
    {
        if (self::$initialized == false)
        {
            // Initialize the license key array
        
            $keys = get_option(self::ID);

            if (empty($keys))
            {
                update_option(self::ID, array());            
            }
            
            /*
             * We used to have a SINGLE license key stored under 'spatialmatch-license-key'.
             * Look for this setting and move it to the new list of license keys.
             */
            
            $legacyKey = get_option('spatialmatch-license-key');
    
            if ($legacyKey !== false)
            {
                self::addLicenseKey($legacyKey);
                
                delete_option('spatialmatch-license-key');
            }
            
            self::$initialized = false;
        }
    }

    public static function getLicenseKeys()
    {
        return get_option(self::ID);
    }

    public static function hasLicenseKey()
    {
        $keys = self::getLicenseKeys();
                
        return !empty($keys);
    }
    
    public static function addLicenseKey($licenseKey)
    {
        $licenseKey = trim($licenseKey);
        
        if (!empty($licenseKey))
        {
            $keys = self::getLicenseKeys();
        
            if (in_array($licenseKey, $keys) === false)
            {
                array_push($keys, $licenseKey);

                update_option(self::ID, $keys);
            }
        }        
    }
    
    public static function removeLicenseKey($licenseKey)
    {
        $licenseKey = trim($licenseKey);
     
        if (!empty($licenseKey))
        {
            $keys = self::getLicenseKeys();

            $filtered = array();
            
            foreach ($keys as $key)
            {
                if ($key != $licenseKey)
                {
                    array_push($filtered, $key);
                }
            }

            update_option(self::ID, $filtered);
        }
    }
}
