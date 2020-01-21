<?php
/**
 * #@package redberylit_plugin
 */

namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class DriverCallbacks extends BaseController
{
    /*Home Page */
    public function driver()
    {
        return require_once("$this->plugin_path./templates/driver.php");

    }

    public function vehicle_licence_class()
    {
        return require_once("$this->plugin_path./templates/licence_class.php");

    }
}
