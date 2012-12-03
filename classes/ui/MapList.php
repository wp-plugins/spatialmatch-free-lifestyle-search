<?php

if (!class_exists('WP_List_Table'))
{
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

require_once(SpatialMatch::$pluginDir . '/classes/controller/Admin.php');
require_once(SpatialMatch::$pluginDir . '/classes/controller/Shortcode.php');
require_once(SpatialMatch::$pluginDir . '/classes/manager/Map.php');

class SpatialMatch_UI_MapList extends WP_List_Table
{
    function __construct()
    {
        parent::__construct(array
        (
            'singular'  => 'Map',
            'plural'    => 'Maps',
            'ajax'      => false
        ));        
    }
    
    function get_columns()
    {
        return array
        (
            'cb'            => '<input type="checkbox" />',
            'id'            => 'ID',
            'title'         => 'Title',
            'description'   => 'Description',
            'shortcode'     => 'Short Code'
        );
    }
    
    function get_sortable_columns()
    {
    	return array
        (
	    'id'            => array('id', false),
            'title'         => array('title', false),
            'description'   => array('description', false)
	);
    }
    
    function get_bulk_actions()
    {
        return array
        (
            'delete' => 'Delete'
        );
    }
    
    function prepare_items()
    {
        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();

        $currentPage = $this->get_pagenum();
        
        $this->_column_headers = array($columns, array(), $sortable);

        $options = array();
                
        if (!empty($_REQUEST['orderby']))
        {
            $options['sortField']  = $_REQUEST['orderby'];
        }
        
        if (!empty($_REQUEST['order']))
        {
            $options['sortOrder'] = $_REQUEST['order'];
        }

        if (!empty($_REQUEST['s']))
        {
            $options['keyword'] = $_REQUEST['s'];
        }
        
        $maps = SpatialMatch_Manager_Map::find($options);

        $totalItems = count($maps);
        $itemsPerPage = 10;        

        $this->items = array_slice($maps, (($currentPage - 1 ) * $itemsPerPage), $itemsPerPage);

        $this->set_pagination_args(array
        (
            'total_items' => $totalItems,
            'per_page'    => $itemsPerPage,                     
            'total_pages' => ceil($totalItems / $itemsPerPage)
        ) );        
    }
    
    function column_cb($item)
    {
        return "<input type='checkbox' name='id[]' id='cb-item-action-" . $item->id . "' value='" . $item->id . "' />";        
    }
    
    function column_title($item)
    {
        $s = "<a class='row-title' href='?page=" . SpatialMatch_Controller_Admin::MENU_ID . "&action=edit&id=" . $item->id .
            "' title='Edit &quot;" . esc_html($item->title) . "&quot;'>" . esc_html($item->title) . "</a>";

        $actions = array
        (
            'edit'      => "<a href='?page=" . SpatialMatch_Controller_Admin::MENU_ID . "&action=edit&id=" . $item->id .
                           "' title='Edit &quot;" . esc_html($item->title) . "&quot;'>Edit</a>",

            'delete'    => "<a href='?page=" . SpatialMatch_Controller_Admin::MENU_ID . "&action=delete&id=" . $item->id .
                           "' title='Delete &quot;" . esc_html($item->title) . "&quot;' " .
                           "onclick=\"return confirm('Are you sure you want to delete the map &quot;" . esc_js($item->title) .
                           "&quot;?');\">Delete</a>"
        );

        return $s . ' ' . $this->row_actions($actions);        
    }

    function column_shortcode($item)
    {
        $s = "<input type='text' style='width:100%;' readonly='true' onclick='this.select()' onfocus='this.select()' " . 
            "value='[" . SpatialMatch_Controller_Shortcode::MAP_SHORTCODE . " id=" . $item->id . "]' />";
            
        return $s;        
    }
    
    function column_default($item, $column_name)
    {
        return !empty($item->{$column_name}) ? $item->{$column_name} : false;
    }
    
    function no_items()
    {
        echo "No maps found.  <a href='?page=" . SpatialMatch_Controller_Admin::MENU_ID . "-add'>Add one now!</a>";
    }    
}
