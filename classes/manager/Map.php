<?php

require_once(SpatialMatch::$pluginDir . '/classes/util/Database.php');
require_once(SpatialMatch::$pluginDir . '/classes/util/Validator.php');
require_once(SpatialMatch::$pluginDir . '/classes/util/String.php');
require_once(SpatialMatch::$pluginDir . '/classes/manager/LicenseKeys.php');
require_once(SpatialMatch::$pluginDir . '/classes/manager/Settings.php');
require_once(SpatialMatch::$pluginDir . '/classes/model/Map.php');

class SpatialMatch_Manager_Map
{
    private static $writeLater;
    private static $initialized = false;
    private static $doAdWords = false;
    
    public static function initialize()
    {
        if (self::$initialized == false)
        {
            add_action('wp_head', array(__CLASS__, 'doHead'));
            add_action('wp_footer', array(__CLASS__, 'doFooter'));

            self::$writeLater = array();
        
            self::$initialized = true;
        }
    }
    
    public static function find()
    {
        global $wpdb;
        
        $tableName = $wpdb->prefix . SpatialMatch_Util_Database::MAP_TABLENAME;
        
        $query = $wpdb->prepare('SELECT * FROM ' . $tableName);
        
        return $wpdb->get_results($query);
    }
    
    public static function create($map)
    {
        global $wpdb;

        if (isset($map))
        {            
            $tableName = $wpdb->prefix . SpatialMatch_Util_Database::MAP_TABLENAME;

            $data = array
            (
                'license_key' => $map->licenseKey,
                'title' => $map->title,
                'bookmark' => $map->bookmark,
                'description' => $map->description
            );
        
            $result = $wpdb->insert($tableName, $data, array('%s', '%s', '%s', '%s'));
        
            if (isset($result) && ($result === 1))
            {
                $map->id = $wpdb->insert_id;
                
                return $map;
            }
        }
        
        return false;
    }
    
    public static function delete($id)
    {
        global $wpdb;

        if (isset($id))
        {
            $tableName = $wpdb->prefix . SpatialMatch_Util_Database::MAP_TABLENAME;

            $statement = $wpdb->prepare('DELETE FROM ' . $tableName . ' WHERE id = %s', $id);
            
            $result = $wpdb->query($statement);
        
            if (isset($result) && ($result === 1))
            {
                return true;
            }
        }
        
        return false;
    }
    
    public static function lookup($id)
    {
        global $wpdb;

        if (isset($id) && ($id > 0))
        {
            $tableName = $wpdb->prefix . SpatialMatch_Util_Database::MAP_TABLENAME;

            $statement = $wpdb->prepare('SELECT * FROM ' . $tableName . ' WHERE id = %s', $id);

            $result = $wpdb->get_row($statement);
            
            if (isset($result))
            {
                $map = new SpatialMatch_Model_Map($result->id,
                                                  $result->license_key,
                                                  $result->title,
                                                  $result->bookmark,
                                                  $result->description);
                
                /*
                 * If this map was created before we required the license key, then its
                 * license key will be blank. Set a default license key as necessary.
                 */
                
                $licenseKey = trim($map->licenseKey);
                
                if (empty($licenseKey))
                {
                    $keys = SpatialMatch_Manager_LicenseKeys::getLicenseKeys();
                    
                    if (!empty($keys) && (count($keys) > 0))
                    {
                        $map->licenseKey = $keys[0];
                    }
                }
                
                return $map;
            }            
        }
        
        return null;
    }
    
    public static function update($map)
    {
        global $wpdb;
        
        if (isset($map) && isset($map->id))
        {
            $tableName = $wpdb->prefix . SpatialMatch_Util_Database::MAP_TABLENAME;

            $data = array
            (
                'license_key' => $map->licenseKey,
                'title' => $map->title,
                'bookmark' => $map->bookmark,
                'description' => $map->description
            );
        
            $result = $wpdb->update($tableName, $data, array('id' => $map->id),
                                    array('%s', '%s', '%s', '%s'), array('%d'));
                    
            if (isset($result) && ($result === 1))
            {
                return $map;
            }
        }
        
        return false;
    }
    
    public static function embedHTML($licenseKey, $bookmark, $width = null, $height = null)
    {
        if (SpatialMatch_Util_Validator::isValidLicenseKey($licenseKey) !== true)
        {
            return '<span class="warning">Your SpatialMatch&reg; license key is invalid.</span>';        
        }

        $width = self::canonicalize($width, SpatialMatch_Model_Map::DEFAULT_WIDTH);
        $height = self::canonicalize($height, SpatialMatch_Model_Map::DEFAULT_HEIGHT);

        return '<div class="spatialmatch-map-wrapper">' . 
            '<script type="text/javascript">SpatialMatch.Map.embed({ licenseKey:"' . $licenseKey .
               '", bookmark:"' . $bookmark . '", width:"' . $width . '", height:"' . $height . '"})</script></div>';
    }
    
    public static function popupHTML($title, $text, $licenseKey, $bookmark, $appearance, $width = null, $height = null)
    {
        if (SpatialMatch_Util_Validator::isValidLicenseKey($licenseKey) !== true)
        {
            return '<span class="warning">Your SpatialMatch&reg; license key is invalid.</span>';        
        }

        $width = self::canonicalize($width, null);
        $height = self::canonicalize($height, null);

        $width = ($width != null) ? ($width = '\'' . $width . '\'') : 'null';
        $height = ($height != null) ? ($height = '\'' . $height . '\'') : 'null';

        $uid = uniqid('sm-popup-');        
        
        $config = '{ licenseKey:\'' . $licenseKey . '\',bookmark:\'' . $bookmark . '\',width:\'100%\',height:\'100%\',renderTo:\'' . $uid . '-map-wrapper\'}';
        
        $js = 'SpatialMatch.Map.popup(\'' . $uid . '\',' . $width . ',' . $height . ',' . $config . ');return false';

        $html = '<span class="spatialmatch-popup-wrapper">';
                
        if ($appearance == 'button')
        {
            $html .= '<input class="spatialmatch-popup-button button" type="button" value="' . esc_html($text) . '" onclick="' . $js . '" />';
        }
        else if ($appearance == 'image')
        {
            $html .= '<img src="' . esc_html($text) . '" class="spatialmatch-popup-image" onclick="' . $js . '" />';            
        }
        else
        {
            $html .= '<a href="#" class="spatialmatch-popup-link" onclick="' . $js . '">' . esc_html($text) . '</a>';            
        }
        
        $html .= '</span>';
        
        // This stuff needs written in the footer
        
        $stuff = '<div id="' . $uid . '" class="spatialmatch-popup-content-wrapper" style="overflow:hidden" title="' . esc_html($title) . '">' .
                    '<div id="' . $uid . '-map-wrapper" class="spatialmatch-map-wrapper"></div></div>';
                    
        array_push(self::$writeLater, $stuff);
        
        return $html;        
    }
 
    static function doHead()
    {
        // Google AdWords
        
        $settings = get_option(SpatialMatch_Manager_Settings::ID);
        
        if (!empty($settings))
        {
            $conversionId = isset($settings['adwords-conversion-id']) ? trim($settings['adwords-conversion-id']) : null;
            $conversionLabel = isset($settings['adwords-conversion-label']) ? trim($settings['adwords-conversion-label']) : null;
            
            if (!empty($conversionId) && !empty($conversionLabel))
            {
                require(SpatialMatch::$pluginDir . '/classes/view/adwords-header.phtml');
                
                self::$doAdWords = true;
            }
        }
    }

    static function doFooter()
    {
        foreach (self::$writeLater as $s)
        {
            echo $s;
        }
        
        if (self::$doAdWords == true)
        {
            require(SpatialMatch::$pluginDir . '/classes/view/adwords-footer.phtml');            
        }
    }
        
    private static function canonicalize($value, $default)
    {
        if (isset($value))
        {
            if (is_int($value) && !is_nan($value) && ($value > 0))
            {
                return $value;
            }
            else if (is_string($value))
            {
                if (SpatialMatch_Util_String::isNumber($value))
                {
                    $v = (int) $value;
                
                    if (!is_nan($v) && ($v > 0))
                    {
                        return $v;
                    }
                }
                else if (strlen($value) > 0)
                {
                    return $value;
                }
            }
            else
            {
                $v = (int) $value;

                if (!is_nan($v) && ($v > 0))
                {
                    return $v;
                }
            }
        }
        
        return $default;
    }
}
