<?php

require_once(SpatialMatch::$pluginDir . '/classes/manager/LicenseKeys.php');

class SpatialMatch_Widget_LifestyleSearch extends WP_Widget
{
    const ID = 'spatialmatch-widget-lifestyle-search';
    
    function __construct()
    {
        $options = array
        (
            'description' => 'This widget allows your users to search the lifestyles in an area.'
        );

        parent::__construct(self::ID, 'SpatialMatch: Lifestyle Search', $options);
    }
    
    function form ($instance)
    {
        $categories = array(
            'public_schools',
            'private_schools'
        );
        
        $instance = wp_parse_args((array)$instance, array('categories' => $categories,
                                                          'target' => 'popup',
                                                          'zoom' => 13));
        
        $this->view = new stdClass();
        
        $this->view->instance = $instance;
        $this->view->widget = $this;
        $this->view->licenseKeys = SpatialMatch_Manager_LicenseKeys::getLicenseKeys();
     
        require(SpatialMatch::$pluginDir . '/classes/view/widget-lifestyle-search-form.phtml');
    }
    
    function widget ($args, $instance)
    {
        $this->view = new stdClass();
        
        $this->view->instance = $instance;        
        $this->view->widget = $args;
        
        // Validate license key
        
        $licenseKey = isset($instance['license_key']) ? trim($instance['license_key']) : null;

        if (empty($licenseKey))
        {
            $licenseKeys = SpatialMatch_Manager_LicenseKeys::getLicenseKeys();
            
            if (count($licenseKeys) > 0)
            {
                $licenseKey = trim($licenseKeys[0]);
            }
        }
        
        if (empty($licenseKey))
        {
            echo '<span class="warning">Your SpatialMatch&reg; license key is invalid.</span>';        
                
            return;
        }
        
        $this->view->licenseKey = $licenseKey;
        
        if (isset($args['before_widget']))
        {
            echo $args['before_widget'];
        }

        require(SpatialMatch::$pluginDir . '/classes/view/widget-lifestyle-search-view.phtml');

        if (isset($args['after_widget']))
        {
            echo $args['after_widget'];
        }
    }
}
