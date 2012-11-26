<?php

require_once(SpatialMatch::$pluginDir . '/classes/widget/LifestyleSearch.php');
require_once(SpatialMatch::$pluginDir . '/classes/widget/Map.php');
require_once(SpatialMatch::$pluginDir . '/classes/widget/Popup.php');
require_once(SpatialMatch::$pluginDir . '/classes/widget/PropertySearch.php');

class SpatialMatch_Controller_Widget
{
    function __construct()
    {
        add_action('widgets_init', array(&$this, 'doWidgetsInit'));
    }

    function doWidgetsInit ()
    {
        register_widget('SpatialMatch_Widget_LifestyleSearch');
        register_widget('SpatialMatch_Widget_Map');
        register_widget('SpatialMatch_Widget_Popup');
        register_widget('SpatialMatch_Widget_PropertySearch');
    }    
}
