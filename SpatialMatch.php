<?php

/*
    Plugin Name: HJI SpatialMatch
    Description: WordPress plugin for SpatialMatch.
    Version: 2.6.3
    Author URI: http://www.homejunction.com
    Author: Home Junction
*/

class SpatialMatch
{
    public static $pluginName;
    public static $pluginDir;
    public static $pluginURL;

    function __construct()
    {
        SpatialMatch::$pluginName = basename(dirname(__FILE__));

        // Hooks

        add_action('init', array(&$this, 'doInit'));
        add_action('plugins_loaded', array(&$this, 'doPluginsLoaded'));

        // Compute paths

        SpatialMatch::$pluginDir = WP_PLUGIN_DIR . '/' . self::$pluginName;
        SpatialMatch::$pluginURL = WP_PLUGIN_URL . '/' . self::$pluginName;

        // Includes

        register_activation_hook(SpatialMatch::$pluginDir . '/' . basename(__FILE__), array(&$this, 'doPluginActivation'));

        require_once(SpatialMatch::$pluginDir . '/classes/controller/Shortcode.php');
        require_once(SpatialMatch::$pluginDir . '/classes/controller/Widget.php');
        require_once(SpatialMatch::$pluginDir . '/classes/manager/LicenseKeys.php');
        require_once(SpatialMatch::$pluginDir . '/classes/manager/Map.php');
        require_once(SpatialMatch::$pluginDir . '/classes/manager/Settings.php');
        require_once(SpatialMatch::$pluginDir . '/classes/util/URL.php');

        // Controllers

        $this->shortcodeController = new SpatialMatch_Controller_Shortcode();
        $this->widgetController = new SpatialMatch_Controller_Widget();

        // Admin

        if (is_admin())
        {
            require_once(SpatialMatch::$pluginDir . '/classes/controller/Admin.php');

            // Controllers

            $this->adminController = new SpatialMatch_Controller_Admin();
        }
    }

    function doInit()
    {
        // Initialize Managers

        SpatialMatch_Manager_LicenseKeys::initialize();
        SpatialMatch_Manager_Map::initialize();
        SpatialMatch_Manager_Settings::initialize();

        // Enqueue scripts

        $deps = array('jquery', 'swfobject');

        if (!is_admin())
        {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('jquery-ui-slider');

            wp_enqueue_style('spatialmatch-jquery-ui', SpatialMatch::$pluginURL . '/third-party/jquery/css/jquery-ui-custom.css');

            array_push($deps, 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-slider');
        }

        wp_enqueue_script('spatialmatch', SpatialMatch::$pluginURL . '/scripts/spatialmatch.js', $deps);

        wp_enqueue_style('spatialmatch', SpatialMatch::$pluginURL . '/css/spatialmatch.css');
    }

    function doPluginActivation()
    {
        require_once(SpatialMatch::$pluginDir . '/classes/util/Database.php');

        SpatialMatch_Util_Database::initialize();
    }

    function doPluginsLoaded()
    {
        require_once(SpatialMatch::$pluginDir . '/classes/util/Database.php');

        SpatialMatch_Util_Database::initialize();
    }
}

new SpatialMatch();
