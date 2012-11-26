<?php

class SpatialMatch_Util_Database
{
    const MAP_TABLENAME = 'spatialmatch_maps';
    
    const DATABASE_VERSION = "1.1";
    
    static private $initialized = false;
    
    public static function initialize()
    {
        global $wpdb;
    
        if (self::$initialized == false)
        {
            $installed_ver = get_option(self::MAP_TABLENAME . '-db-version');

            if ($installed_ver != self::DATABASE_VERSION)
            {
                $tableName = $wpdb->prefix . SpatialMatch_Util_Database::MAP_TABLENAME;
    
                $sql = 'CREATE TABLE ' . $tableName . ' (
                    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    title varchar(255) NOT NULL,
                    license_key varchar(128) NOT NULL,
                    description text DEFAULT NULL,
                    bookmark text DEFAULT NULL
                )';

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            
                dbDelta($sql);
            
                add_option(self::MAP_TABLENAME . '-db-version', self::DATABASE_VERSION);
            }
            
            self::$initialized = true;
        }
    }
}
