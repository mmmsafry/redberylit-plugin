<?php
/**
 * #@package redberylit_plugin
 */

namespace Inc\Pages;

use \Inc\Api\SettingsApi;
use \Inc\Base\BaseController;
use \Inc\Api\Callbacks\DriverCallbacks;


class Driver extends BaseController
{
    public $settings;
    public $pages = [];
    public $subpages = [];
    public $callbacks;

    public function register()
    {
        $this->settings = new SettingsApi();
        $this->callbacks = new DriverCallbacks();
        $this->setPages();
        $this->setSubPages();
        $this->settings->AddPages($this->pages)->withSubPage('Driver')->addSubPages($this->subpages)->register();
    }

    public function setPages()
    {
        $this->pages = [
            [
                'page_title' => 'Manager User',
                'menu_title' => 'Manager User',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_plugin_driver',
                'callback' => [$this->callbacks, 'driver'],
                'icon_url' => 'dashicons-admin-users',
                'position' => 31
            ]
        ];
    }

    public function setSubPages()
    {
        $this->subpages = [
            [
                'parent_slug' => 'redberylit_plugin_driver',
                'page_title' => 'Licence Class',
                'menu_title' => 'Licence  Class',
                'capability' => 'manage_options',
                'menu_slug' => 'vehicle_licence_class',
                'callback' => [$this->callbacks, 'vehicle_licence_class']
            ]
        ];
    }

}