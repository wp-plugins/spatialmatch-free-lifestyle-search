<?php

$wpdir = isset($_GET['wp']) ? trim($_GET['wp']) : null;

if (!isset($wpdir) || (strlen($wpdir) == 0) || (file_exists($wpdir) == false))
{
    echo 'Error: WP_8317-6(A)';     // This is just a random error code to make it look 'important'
    exit;
}

$tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'map';

// Bootstap an admin page just like they do in '<wordpress>/wp-admin/media-upload.php'
 
require_once($wpdir . '/wp-admin/admin.php');

require_once('../../SpatialMatch.php');

require_once(SpatialMatch::$pluginDir . '/classes/util/String.php');

if (!is_admin())
{
    echo 'You do not have permission to access this page.';
    exit;
}

wp_iframe('spatialmatch_output_shortcode_editor_body', $tab);

exit;

function spatialmatch_output_shortcode_editor_body ($tab)
{
    $maps = SpatialMatch_Manager_Map::find();
?>
    <div class='spatialmatch-shortcode-editor'>
        
<?php    
        if (count($maps) > 0)
        {
?>        
            <div class='header'>
                <ul class='menubar'>                
                    <li>
                        <a id='x-link-map' href='#' onclick='__sm_activate_tab("map");return false'>Embedded Map</a>
                    </li>
                    
                    <li>
                        <a id='x-link-popup' href='#' onclick='__sm_activate_tab("popup");return false'>Popup Map</a>
                    </li>
                </ul>
            </div>                
    
            <div class='tab-wrapper'>
            
                <div class='tab-content' id='x-tab-map' style='display:none'>

                    <label>Select the map that you want to show on this page:</label>
            
                    <select id='x-selector-map'>
<?php
                        foreach ($maps as $map)
                        {
?>
                            <option value='<?php echo $map->id; ?>'><?php echo SpatialMatch_Util_String::truncate($map->title, 50) ?></option>
<?php
                        }
?>                    
                    </select>
        
                    <table>
        
                        <tr>
                            <td>
                                <label for='x-width-map'>Map Width:</label>
                            </td>
                            
                            <td>        
                                <input type='text' class='dimensionbox widefat' id='x-width-map' />
                            </td>
                            
                            <td>(pixels or %)</td>
                        </tr>
                                
                        <tr>
                            <td>
                                <label for='x-height-map'>Map Height:</label>
                            </td>
                    
                            <td>
                                <input type='text' class='dimensionbox widefat' id='x-height-map' />
                            </td>
                            
                            <td>(pixels)</td>
                        </tr>
                        
                    </table>

                    <input class='checkbox' type='checkbox' id='x-title-map' value='1'> &nbsp;
                        <label for='x-title-map'>Show the map title.</label>

                </div>
                
                <div class='tab-content' id='x-tab-popup' style='display:none'>

                    <label>Select the map that you want to popup:</label>
            
                    <select id='x-selector-popup'>
<?php
                        foreach ($maps as $map)
                        {
?>
                            <option value='<?php echo $map->id; ?>'><?php echo SpatialMatch_Util_String::truncate($map->title, 50) ?></option>
<?php
                        }
?>                    
                    </select>

                    <label for='x-appearance-popup'>        
                        The <strong>anchor</strong> is what the user will click on to popup this map.<br /> What do you want this anchor to look like?
                    </label>
            
                    <select id='x-appearance-popup'>
                
                        <option value='link'>Standard Web Link</option>
                        <option value='button'>Button</option>
                        <option value='image'>Image</option>
                
                    </select>                
        
                    <label for='x-text-popup'>Anchor Text or Image URL:</label><br />
        
                    <input type='text' class='widefat' id='x-text-popup' />
                
                    <br /><span class='description'>Required for an image.   If you leave this blank, the map title will be used as the link or button text.</span>
                            
                    <table>
        
                        <tr>
                            <td>
                                <label for='x-width-popup'>Popup Width:</label>
                            </td>
                            
                            <td>        
                                <input type='text' class='widefat dimensionbox' id='x-width-popup' />
                            </td>
                            
                            <td>(pixels or %)</td>
                        </tr>
                                
                        <tr>
                            <td>
                                <label for='x-height-popup'>Popup Height:</label>
                            </td>
                    
                            <td>
                                <input type='text' class='widefat dimensionbox' id='x-height-popup' />
                            </td>
                            
                            <td>(pixels)</td>
                        </tr>
                        
                    </table>
                
                </div>
                
            </div>
            
            <div class='button-box'>
                <input id='x-ok' class='button button-primary' type='button' value='OK' onclick='__sm_submit_shortcode()'>
                <input id='x-ok' class='button button-secondary' type='button' value='Cancel' onclick='tb_remove()'>
            </div>
            
            <script type='text/javascript'>
                __sm_activate_tab = function (id)
                {
                    var activeTab = jQuery('#x-tab-' + id);
                    
                    if (activeTab != null)
                    {
                        var allTabs = activeTab.parent('div').children('div');
    
                        for (var ii = 0; ii <  allTabs.length; ii++)
                        {
                            var tab = jQuery(allTabs[ii]);
    
                            (tab.attr('id') == activeTab.attr('id')) ? tab.show() : tab.hide();
                        }
                        
                        var activeLink = jQuery('#x-link-' + id);
                        
                        var allLinks = activeLink.parents('ul.menubar').find('a');
                        
                        for (var jj = 0; jj <  allLinks.length; jj++)
                        {
                            var link = jQuery(allLinks[jj]);
    
                            (link.attr('id') == activeLink.attr('id')) ? link.addClass('active') : link.removeClass('active');
                        }
                    }
                }
            
                __sm_submit_shortcode = function ()
                {
                    var tab = jQuery('.tab-content:visible');
                    
                    var shortcode = null;
                    
                    if (tab.attr('id') == 'x-tab-map')
                    {
                        var id = jQuery.trim(jQuery('#x-selector-map').val());  

                        if ((id == null) || (id == ''))
                        {
                            alert('Please select a SpatialMatch&reg; map.');
            
                            return;
                        }

                        var shortcode = '[<?php echo SpatialMatch_Controller_Shortcode::MAP_SHORTCODE?> id=' + id;

                        if (jQuery('#x-title-map').is(':checked'))
                        {
                            shortcode += ' show_title=true';                
                        }
        
                        var width = jQuery.trim(jQuery('#x-width-map').val());
        
                        if ((width != null) && (width != ''))
                        {
                            shortcode += (' width=' + width);
                        }
        
                        var height = jQuery.trim(jQuery('#x-height-map').val());
        
                        if ((height != null) && (height != ''))
                        {
                            shortcode += (' height=' + height);
                        }

                        shortcode += ']';
                    }
                    else if (tab.attr('id') == 'x-tab-popup')
                    {
                        var id = jQuery.trim(jQuery('#x-selector-popup').val());  

                        if ((id == null) || (id == ''))
                        {
                            alert('Please select a SpatialMatch&reg; map.');
            
                            return;
                        }
                        
                        var shortcode = '[<?php echo SpatialMatch_Controller_Shortcode::POPUP_SHORTCODE?> id=' + id;

                        var appearance = jQuery.trim(jQuery('#x-appearance-popup').val());  
            
                        if ((appearance != null) && (appearance != ''))                        
                        {
                            shortcode += (' appearance=' + appearance);
                        }

                        var width = jQuery.trim(jQuery('#x-width-popup').val());
        
                        if ((width != null) && (width != ''))
                        {
                            shortcode += (' width=' + width);
                        }
        
                        var height = jQuery.trim(jQuery('#x-height-popup').val());
        
                        if ((height != null) && (height != ''))
                        {
                            shortcode += (' height=' + height);
                        }

                        shortcode += ']';
                        
                        var text = jQuery.trim(jQuery('#x-text-popup').val());  

                        if ((text != null) && (text != ''))                        
                        {
                            shortcode += (text + '[/<?php echo SpatialMatch_Controller_Shortcode::POPUP_SHORTCODE?>]');
                        }                        
                    }
                    
                    if (shortcode != null)
                    {
                        var win = window.dialogArguments || opener || parent || top;        
                        
                        win.send_to_editor(shortcode);
                    }

                    tb_remove();
                }
                
                __sm_activate_tab('<?php echo $tab ?>');
            </script>
    
        </div>    
<?php
        }
        else
        {
?>
            <div class='warning'>
                You have not created any SpatialMatch&reg; maps. &nbsp;
                <a href='admin.php?page=<?php echo SpatialMatch_Controller_Admin::MENU_ID ?>-add'>Add one now!</a>
            </div>
<?php
        }
?>
    </div>
<?php    
}
