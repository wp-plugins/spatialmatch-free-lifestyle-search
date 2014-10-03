<?php

require_once(SpatialMatch::$pluginDir . '/classes/util/Validator.php');
require_once(SpatialMatch::$pluginDir . '/classes/util/String.php');
require_once(SpatialMatch::$pluginDir . '/classes/util/URL.php');
require_once(SpatialMatch::$pluginDir . '/classes/manager/LicenseKeys.php');
require_once(SpatialMatch::$pluginDir . '/classes/manager/Map.php');
require_once(SpatialMatch::$pluginDir . '/classes/manager/Settings.php');
require_once(SpatialMatch::$pluginDir . '/classes/model/Map.php');
require_once(SpatialMatch::$pluginDir . '/classes/ui/MapList.php');

class SpatialMatch_Controller_Admin
{
    const MENU_ID = 'spatialmatch-admin';

    function __construct()
    {
        // Hooks

        add_action('admin_init', array(&$this, 'doAdminInit'));
        add_action('admin_menu', array(&$this, 'doAdminMenu'));
        add_action('admin_notices', array(&$this, 'doAdminNotices'));
    }

    function doAdminInit()
    {
        wp_enqueue_script('spatialmatch-admin', SpatialMatch::$pluginURL . '/scripts/admin.js');
        wp_enqueue_style('spatialmatch-admin', SpatialMatch::$pluginURL . '/css/admin.css');
    }

    function doAdminMenu()
    {
        add_menu_page('SpatialMatch',
                      'SpatialMatch&reg;',
                      'administrator',
                      self::MENU_ID,
                      array(&$this, 'defaultPageHandler'),
                      esc_url(SpatialMatch::$pluginURL . '/images/admin-menu.png'));

        add_submenu_page(self::MENU_ID,
                         'SpatialMatch | Maps',
                         'All Maps',
                         'administrator',
                         self::MENU_ID,
                         array(&$this, 'defaultPageHandler'));

        add_submenu_page(self::MENU_ID,
                         'SpatialMatch | Add New Map',
                         'Add New Map',
                         'administrator',
                         self::MENU_ID . '-add',
                         array(&$this, 'addPageHandler'));

        add_submenu_page(self::MENU_ID,
                         'SpatialMatch | License Keys',
                         'License Keys',
                         'administrator',
                         self::MENU_ID . '-licenses',
                         array(&$this, 'licenseKeysPageHandler'));

        add_submenu_page(self::MENU_ID,
                         'SpatialMatch | Settings',
                         'Settings',
                         'administrator',
                         self::MENU_ID . '-settings',
                         array(&$this, 'settingsPageHandler'));

        add_submenu_page(self::MENU_ID,
                         'SpatialMatch | Help',
                         'Help',
                         'administrator',
                         self::MENU_ID . '-help',
                         array(&$this, 'helpPageHandler'));
    }

    function doAdminNotices()
    {
        if (SpatialMatch_Manager_LicenseKeys::hasLicenseKey() === false)
        {
            $qs = $_SERVER['QUERY_STRING'];

            if (strpos($qs, 'action=add-license') === false)
            {
?>
                <div class='updated'>
                    <p>
                        <strong>The SpatialMatch&reg; Plugin for WordPress is almost ready.</strong>
                        To get your FREE license key, click <a href='http://www.freelifestylesearch.com' target='_blank'>here</a>
                        and use promo code <strong>WP12</strong> and complete the simple form.  If you already have a license key, click
                        <a href='<?php echo admin_url('admin.php?page=' . self::MENU_ID . '-licenses&action=add-license')?>'>here</a>.
                    </p>
                </div>
<?php
            }
        }
    }

    function defaultPageHandler($action)
    {
        $this->routeAction($this->getCurrentAction());
    }

    function addPageHandler()
    {
        $action = $this->getCurrentAction();

        $this->routeAction(!empty($action) ? $action : 'add');
    }

    function licenseKeysPageHandler()
    {
        $action = $this->getCurrentAction();

        $this->routeAction(!empty($action) ? $action : 'licenses');
    }

    function settingsPageHandler()
    {
        $action = $this->getCurrentAction();

        $this->routeAction(!empty($action) ? $action : 'settings');
    }

    function helpPageHandler()
    {
        $action = $this->getCurrentAction();

        $this->routeAction(!empty($action) ? $action : 'help');
    }

    private function routeAction($action)
    {
        $this->view = new stdClass();

        $this->view->errors = array();

        if ($action == 'add')
        {
            $this->addMap();
        }
        else if ($action == 'create')
        {
            $this->createMap();
        }
        else if ($action == 'delete')
        {
            $this->deleteMap();
        }
        else if ($action == 'edit')
        {
            $this->editMap();
        }
        else if ($action == 'update')
        {
            $this->updateMap();
        }
        else if ($action == 'licenses')
        {
            $this->showLicenseKeys();
        }
        else if ($action == 'settings')
        {
            $this->showSettings();
        }
        else if ($action == 'help')
        {
            $this->showHelp();
        }
        else if ($action == 'add-license')
        {
            $this->addLicenseKey();
        }
        else if ($action == 'commit-license')
        {
            $this->commitLicenseKey();
        }
        else if ($action == 'delete-license')
        {
            $this->deleteLicenseKey();
        }
        else
        {
            $this->showMapList();
        }
    }

    private function addMap()
    {
        $licenseKeys = SpatialMatch_Manager_LicenseKeys::getLicenseKeys();

        if (!empty($licenseKeys))
        {
            $this->view->map = new SpatialMatch_Model_Map();
            $this->view->licenseKeys = $licenseKeys;

            require_once(SpatialMatch::$pluginDir . '/classes/view/admin-map-add.phtml');
        }
        else
        {
            array_push($this->view->errors, 'You must specify a license key before you can create a map.');

            $this->addLicenseKey();
        }
    }

    private function createMap()
    {
        $map = new SpatialMatch_Model_Map();

        $map->title = SpatialMatch_Util_URL::getParameter('spatialmatch-map-title');

        if (!isset($map->title) || (strlen($map->title) == 0))
        {
            array_push($this->view->errors, 'You must specify a title for this map.');
        }

        $map->licenseKey = SpatialMatch_Util_URL::getParameter('spatialmatch-map-license-key');

        if (!isset($map->licenseKey) || (strlen($map->licenseKey) == 0))
        {
            array_push($this->view->errors, 'You must specify a license key for this map.');
        }

        $map->bookmark = SpatialMatch_Util_URL::getParameter('spatialmatch-map-bookmark');

        $map->description = SpatialMatch_Util_URL::getParameter('spatialmatch-map-description');

        if (count($this->view->errors) == 0)
        {
            $result = SpatialMatch_Manager_Map::create($map);

            if ($result !== false)
            {
                $this->view->message = 'A new map has been created.';

                $this->showMapList();

                return;
            }

            array_push($this->view->errors, 'An unexpected error occurred while creating the map.');
        }

        $this->view->map = $map;
        $this->view->licenseKeys = SpatialMatch_Manager_LicenseKeys::getLicenseKeys();

        require_once(SpatialMatch::$pluginDir . '/classes/view/admin-map-add.phtml');
    }

    private function editMap()
    {
        $id = (int) SpatialMatch_Util_URL::getParameter('id');

        if (isset($id) && !is_nan($id) && ($id > 0))
        {
            $map = SpatialMatch_Manager_Map::lookup($id);

            if (isset($map))
            {
                if (!empty($map->licenseKey))
                {
                    $this->view->map = $map;
                    $this->view->licenseKeys = SpatialMatch_Manager_LicenseKeys::getLicenseKeys();

                    require_once(SpatialMatch::$pluginDir . '/classes/view/admin-map-edit.phtml');

                    return;
                }
                else
                {
                    array_push($this->view->errors, 'You must specify a license key before you can edit this map.');

                    $this->addLicenseKey();

                    return;
                }
            }
            else
            {
                array_push($this->view->errors, 'The specified map does not exist.');
            }
        }

        $this->showMapList();
    }

    private function deleteMap()
    {
        $ids = (is_array($_REQUEST['id'])) ? $_REQUEST['id'] : array($_REQUEST['id']);

        $count = 0;

        if (!empty($ids))
        {
            foreach ($ids as $id)
            {
                if (isset($id) && !is_nan($id) && ($id > 0))
                {
                    if (SpatialMatch_Manager_Map::delete($id) !== false)
                    {
                        $count++;
                    }
                }
            }

            if ($count > 0)
            {
                $this->view->message = $count . (($count == 1) ? ' map has' : ' maps have') . ' been deleted.';
            }
        }

        $this->showMapList();
    }

    private function updateMap()
    {
        $map = new SpatialMatch_Model_Map();

        $map->id = (int) SpatialMatch_Util_URL::getParameter('id');

        if (!isset($map->id) || is_nan($map->id) || ($map->id <= 0))
        {
            array_push($this->view->errors, 'No map ID was specified.');
        }

        $map->title = SpatialMatch_Util_URL::getParameter('spatialmatch-map-title');

        if (!isset($map->title) || (strlen($map->title) == 0))
        {
            array_push($this->view->errors, 'You must specify a title for this map.');
        }

        $map->licenseKey = SpatialMatch_Util_URL::getParameter('spatialmatch-map-license-key');

        if (!isset($map->licenseKey) || (strlen($map->licenseKey) == 0))
        {
            array_push($this->view->errors, 'You must specify a license key for this map.');
        }

        $map->bookmark = SpatialMatch_Util_URL::getParameter('spatialmatch-map-bookmark');

        $map->description = SpatialMatch_Util_URL::getParameter('spatialmatch-map-description');

        if (count($this->view->errors) == 0)
        {
            $result = SpatialMatch_Manager_Map::update($map);

            if ($result !== false)
            {
                $this->view->message = 'The map has been updated.';

                $this->showMapList();

                return;
            }

            array_push($this->view->errors, 'An unexpected error occurred while updating the map.');
        }

        $this->view->map = $map;
        $this->view->licenseKeys = SpatialMatch_Manager_LicenseKeys::getLicenseKeys();

        require(SpatialMatch::$pluginDir . '/classes/view/admin-map-edit.phtml');
    }

    private function showMapList()
    {
        $this->view->mapList = new SpatialMatch_UI_MapList();

        $this->view->mapList->prepare_items();

        require(SpatialMatch::$pluginDir . '/classes/view/admin-map-list.phtml');
    }

    private function showLicenseKeys()
    {
        $this->view->licenseKeys = SpatialMatch_Manager_LicenseKeys::getLicenseKeys();

        require(SpatialMatch::$pluginDir . '/classes/view/admin-license-list.phtml');
    }

    private function showSettings()
    {
        if (SpatialMatch_Util_String::toBoolean(SpatialMatch_Util_URL::getParameter('settings-updated')) == true)
        {
            $this->view->message = 'Your settings have been updated.';
        }

        $this->view->settings = get_option(SpatialMatch_Manager_Settings::ID);


        require(SpatialMatch::$pluginDir . '/classes/view/admin-settings.phtml');
    }

    private function showHelp()
    {
        $this->view->licenseKeys = SpatialMatch_Manager_LicenseKeys::getLicenseKeys();

        require(SpatialMatch::$pluginDir . '/classes/view/admin-help.phtml');
    }

    private function addLicenseKey()
    {
        $this->view->licenseKey = '';

        require_once(SpatialMatch::$pluginDir . '/classes/view/admin-license-add.phtml');
    }

    private function commitLicenseKey()
    {
        $licenseKey = SpatialMatch_Util_URL::getParameter('spatialmatch-license-key');

        if (!isset($licenseKey) || (strlen($licenseKey) == 0))
        {
            array_push($this->view->errors, 'You must specify a license key.');
        }
        else
        {
            if (SpatialMatch_Util_Validator::isValidLicenseKey($licenseKey) == false)
            {
                array_push($this->view->errors, 'The specified license key is not valid.');
            }
        }

        if (count($this->view->errors) == 0)
        {
            SpatialMatch_Manager_LicenseKeys::addLicenseKey($licenseKey);

            $this->view->message = 'A new license key has been added.';

            $this->showLicenseKeys();

            return;
        }

        $this->view->licenseKey = $licenseKey;

        require_once(SpatialMatch::$pluginDir . '/classes/view/admin-license-add.phtml');
    }

    private function deleteLicenseKey()
    {
        $licenseKey = SpatialMatch_Util_URL::getParameter('licenseKey');

        if (!empty($licenseKey))
        {
            SpatialMatch_Manager_LicenseKeys::removeLicenseKey($licenseKey);
        }

        $this->view->message = 'The license key has been removed.';

        $this->showLicenseKeys();
    }

    private function getCurrentAction()
    {
        $action = SpatialMatch_Util_URL::getParameter('action');

        if (empty($action) || ($action == '-1'))
        {
            $action = SpatialMatch_Util_URL::getParameter('action2');
        }

        return $action;
    }
}
