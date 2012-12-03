<?php

require_once(SpatialMatch::$pluginDir . '/classes/manager/Map.php');
require_once(SpatialMatch::$pluginDir . '/classes/util/String.php');

class SpatialMatch_Controller_Shortcode
{
    const MAP_SHORTCODE = 'spatialmatch_map';   
    const POPUP_SHORTCODE = 'spatialmatch_popup';   
    
    function __construct()
    {
        add_shortcode(self::MAP_SHORTCODE, array(&$this, 'mapShortcodeHandler'));
        add_shortcode(self::POPUP_SHORTCODE, array(&$this, 'popupShortcodeHandler'));
        
        add_filter('media_buttons_context', array(&$this, 'doMediaButtonsFilter'));
    }
    
    function mapShortcodeHandler ($attrs, $content = null)
    {
        extract(shortcode_atts(array('id' => '0',
                                     'show_title' => null,
                                     'width' => null,
                                     'height' => null), $attrs));

        $map = SpatialMatch_Manager_Map::lookup($id);

        if (!isset($map))
        {
            return '<span class="warning">Please specify a valid SpatialMatch&reg; map.</span>';
        }

        $s = '';
        
        if (SpatialMatch_Util_String::toBoolean($show_title) == true)
        {
            $s .= '<h3>' . $map->title . '</h3>';
        }
        
        $licenseKey = (isset($map->licenseKey)) && (strlen($map->licenseKey) > 0) ? $map->licenseKey : '';

        $bookmark = (isset($map->bookmark)) && (strlen($map->bookmark) > 0) ? $map->bookmark : '';
        
        $s .= SpatialMatch_Manager_Map::embedHTML($licenseKey, $bookmark, $width, $height);
        
        return $s;
    }

    function popupShortcodeHandler ($attrs, $content = null)
    {        
        extract(shortcode_atts(array('id' => '0',
                                     'appearance' => null,
                                     'width' => null,
                                     'height' => null), $attrs));
        
        $map = SpatialMatch_Manager_Map::lookup($id);

        if (!isset($map))
        {
            return '<span class="warning">Please specify a valid SpatialMatch&reg; map.</span>';
        }

        if (!isset($content) || (strlen($content) == 0))
        {
            $content = trim($map->title);
        }

        $licenseKey = (isset($map->licenseKey)) && (strlen($map->licenseKey) > 0) ? $map->licenseKey : '';

        $bookmark = (isset($map->bookmark)) && (strlen($map->bookmark) > 0) ? $map->bookmark : '';

        return SpatialMatch_Manager_Map::popupHTML($map->title, $content, $licenseKey, $bookmark, $appearance, $width, $height);        
    }
    
    function doMediaButtonsFilter ($content)
    {
        $wpdir = dirname(WP_CONTENT_DIR);
                   
        $url = SpatialMatch::$pluginName . '/classes/view/shortcode-editor-popup.php?width=640&height=601&wp=' . esc_html($wpdir) . '&TB_iframe=1';
        
        $content .= '<a href="#" onclick=\'tb_show("Add SpatialMatch&reg; Map", "' . plugins_url($url) . '");return false\'>' . 
            '<img alt="Add SpatialMatch&reg; Map" ' . 'src="' . SpatialMatch::$pluginURL . '/images/media-button.png" /></a>';

        return $content;
    }
}

