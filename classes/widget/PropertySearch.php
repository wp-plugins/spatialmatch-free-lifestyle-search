<?php

require_once(SpatialMatch::$pluginDir . '/classes/manager/LicenseKeys.php');

class SpatialMatch_Widget_PropertySearch extends WP_Widget
{
    const ID = 'spatialmatch-widget-home-search';
    
    const LISTING_TYPE_RESIDENTIAL = 'listings';
    const LISTING_TYPE_LAND = 'land';
    const LISTING_TYPE_RENTALS = 'rentals';
    const LISTING_TYPE_MULTIFAMILY = 'multifamily';
    const LISTING_TYPE_FARMS = 'farms';
    
    const ORIENTATION_VERTICAL = 'vertical';
    const ORIENTATION_HORIZONTAL = 'horizontal';

    const TARGET_POPUP = 'popup';
    const TARGET_PAGE = 'page';
    
    public static $FIELDS;
    
    function __construct()
    {
        if (self::$FIELDS == null)
        {
            self::$FIELDS = array
            (
                array(key   => 'location',
                      label => 'Location',
                      types => array('*')
                ),
                    
                array(key   => 'beds',
                      label => 'Bedrooms',
                      types => array(self::LISTING_TYPE_RESIDENTIAL,
                                     self::LISTING_TYPE_RENTALS,
                                     self::LISTING_TYPE_FARMS)
                ),
                    
                array(key   => 'baths',
                      label => 'Baths',
                      types => array(self::LISTING_TYPE_RESIDENTIAL,
                                     self::LISTING_TYPE_RENTALS,
                                     self::LISTING_TYPE_FARMS)
                ),
                
                array(key   => 'listingPrice',
                      label => 'Price',
                      types => array(self::LISTING_TYPE_RESIDENTIAL,
                                     self::LISTING_TYPE_MULTIFAMILY,
                                     self::LISTING_TYPE_FARMS)
                ),

                array(key   => 'leasePrice',
                      label => 'Price',
                      types => array(self::LISTING_TYPE_RENTALS)
                ),

                array(key   => 'landPrice',
                      label => 'Price',
                      types => array(self::LISTING_TYPE_LAND)
                ),

                array(key   => 'homeSize',
                      label => 'Home Size',
                      types => array(self::LISTING_TYPE_RESIDENTIAL,
                                     self::LISTING_TYPE_FARMS)
                ),

                array(key   => 'lotSize',
                      label => 'Lot Size',
                      types => array('*')
                ),

                array(key   => 'yearBuilt',
                      label => 'Year Built',
                      types => array(self::LISTING_TYPE_RESIDENTIAL,
                                     self::LISTING_TYPE_MULTIFAMILY,
                                     self::LISTING_TYPE_RENTALS,
                                     self::LISTING_TYPE_FARMS)
                ),

                array(key   => 'numUnits',
                      label => 'Number of Units',
                      types => array(self::LISTING_TYPE_MULTIFAMILY)
                ),

                array(key   => 'keywords',
                      label => 'Keywords',
                      types => array('*')
                )
            );
        }                

        $options = array
        (
            'description' => 'This widget creates a form that allows your users to search for for-sale property.'
        );

        parent::__construct(self::ID, 'SpatialMatch: Property Search', $options);
    }
    
    function form ($instance)
    {
        $instance = wp_parse_args((array)$instance, array('listingType' => self::LISTING_TYPE_RESIDENTIAL,
                                                          'fields' => array('location', 'homeSize', 'listingPrice', 'beds', 'baths'),
                                                          'target' => self::TARGET_POPUP,
                                                          'orientation' => self::ORIENTATION_VERTICAL,
                                                          'column_count' => 3,
                                                          'zoom' => 13));
        
        $this->view = new stdClass();
     
        $this->view->instance = $instance;
        $this->view->widget = $this;
        $this->view->licenseKeys = SpatialMatch_Manager_LicenseKeys::getLicenseKeys();
     
        require(SpatialMatch::$pluginDir . '/classes/view/widget-property-search-form.phtml');
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

        require(SpatialMatch::$pluginDir . '/classes/view/widget-property-search-view.phtml');

        if (isset($args['after_widget']))
        {
            echo $args['after_widget'];
        }
    }
}
