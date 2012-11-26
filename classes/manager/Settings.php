<?php

class SpatialMatch_Manager_Settings
{
    const ID = 'spatialmatch-settings';
    
    private static $initialized = false;
    
    public static function initialize()
    {
        if (self::$initialized == false)
        {
            if (is_admin())
            {
                add_action('admin_init', array(__CLASS__, 'doAdminInit'));
            }
                        
            self::$initialized = false;
        }
    }

    static function doAdminInit()
    {
        register_setting(self::ID, self::ID, array(__CLASS__, 'doValidateSettings'));
    }
    
    static function doValidateSettings ($input)
    {
        return $input;
    }
}
