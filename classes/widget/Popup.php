<?php

require_once(SpatialMatch::$pluginDir . '/classes/manager/Map.php');
require_once(SpatialMatch::$pluginDir . '/classes/util/String.php');

class SpatialMatch_Widget_Popup extends WP_Widget
{
    const ID = 'spatialmatch-widget-popup';
    
    function __construct()
    {
        $options = array
        (
            'description' => 'Use this widget to display a link or button that, when clicked, will popup a SpatialMatch&reg; map.'
        );

        parent::__construct(self::ID, 'HJI SpatialMatch: Popup', $options);
    }
    
    function form ($instance)
    {
        $this->view = new stdClass();
        
        $this->view->maps = SpatialMatch_Manager_Map::find();
        $this->view->instance = $instance;
        $this->view->widget = $this;
     
        require(SpatialMatch::$pluginDir . '/classes/view/widget-popup-form.phtml');
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

        $text = isset($instance['text']) ? trim($instance['text']) : null;
        
        if (!isset($text) || (strlen($text) == 0))
        {
            $text = trim($map->title);
        }
        
        $licenseKey = (isset($map->licenseKey)) && (strlen($map->licenseKey) > 0) ? $map->licenseKey : '';
        
        $bookmark = (isset($map->bookmark)) && (strlen($map->bookmark) > 0) ? $map->bookmark : '';

        echo SpatialMatch_Manager_Map::popupHTML($map->title, $text, $licenseKey, $bookmark,
                                                 $instance['appearance'], $instance['width'], $instance['height']);

        if (isset($args['after_widget']))
        {
            echo $args['after_widget'];
        }
    }
}
