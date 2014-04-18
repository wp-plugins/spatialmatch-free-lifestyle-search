<?php

require_once(SpatialMatch::$pluginDir . '/classes/manager/Map.php');
require_once(SpatialMatch::$pluginDir . '/classes/util/String.php');

class SpatialMatch_Widget_Map extends WP_Widget
{
    const ID = 'spatialmatch-widget-map';
    
    function __construct()
    {
        $options = array
        (
            'description' => 'Use this widget to display a SpatialMatch&reg; map in one of your widget areas.'
        );

        parent::__construct(self::ID, 'HJI SpatialMatch: Map', $options);
    }
    
    function form ($instance)
    {
        $this->view = new stdClass();
        
        $this->view->instance = $instance;        
        $this->view->maps = SpatialMatch_Manager_Map::find();
        $this->view->widget = $this;
     
        require(SpatialMatch::$pluginDir . '/classes/view/widget-map-form.phtml');
    }
    
    function widget ($args, $instance)
    {
        $map = SpatialMatch_Manager_Map::lookup($instance['map']);

        if (!isset($map))
        {
            echo '<span class="warning">Please select a valid SpatialMatch&reg; map.</span>';
            
            return;
        }

        if (isset($args['before_widget']))
        {
            echo $args['before_widget'];
        }

        if (isset($instance['show_title']) && ($instance['show_title'] == '1'))
        {
            if (isset($args['before_title']))
            {
                echo $args['before_title'];
            }
        
            echo esc_html($map->title);
            
            if (isset($args['after_title']))
            {
                echo $args['after_title'];
            }
        }

        $licenseKey = (isset($map->licenseKey)) && (strlen($map->licenseKey) > 0) ? $map->licenseKey : '';

        $bookmark = (isset($map->bookmark)) && (strlen($map->bookmark) > 0) ? $map->bookmark : '';
                    
        echo SpatialMatch_Manager_Map::embedHTML($licenseKey, $bookmark, $instance['width'], $instance['height']);
        
        if (isset($args['after_widget']))
        {
            echo $args['after_widget'];
        }
    }
}