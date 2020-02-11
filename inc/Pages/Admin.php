<?php
/**
 * #@package redberylit_plugin
 */

namespace Inc\Pages;

use Inc\Api\Callbacks\CustomTaxonomies;
use Inc\Api\Callbacks\WidgetManager;
use \Inc\Api\SettingsApi;
use \Inc\Base\BaseController;
use \Inc\Api\Callbacks\AdminCallbacks;
use \Inc\Api\Callbacks\CustomPostType;


class Admin extends BaseController
{
    public $settings;
    public $pages = [];
    public $subpages = [];
    public $callbacks;

    public function register()
    {
        $this->settings = new SettingsApi();
        $this->callbacks = new AdminCallbacks();

        $this->setPages();
        $this->setSubPages();

        $this->setSettings();
        $this->setSections();
        $this->setFields();

        $this->settings->AddPages($this->pages)->withSubPage('Self Drive')->addSubPages($this->subpages)->register();
    }

    public function setPages()
    {
        $this->pages = [
            [
                'page_title' => 'Manage Rates',
                'menu_title' => 'Rate Chart',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_plugin',
                'callback' => [$this->callbacks, 'selfDriveRate'], //adminDashboard
                'icon_url' => 'dashicons-media-spreadsheet',
                'position' => 30
            ]
        ];
    }

    /* [
            'page_title' => 'Manage Rates',
            'menu_title' => 'Manage Rate',
            'capability' => 'manage_options',
            'menu_slug' => 'redberylit_plugin',
            'callback' => [$this->callbacks, 'rateChart'], //adminDashboard
            'icon_url' => 'dashicons-media-spreadsheet',
            'position' => 30
        ]

    --- sub page move to main page
    [
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'Self Drive Rates',
                'menu_title' => 'Self Drive',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_rate_self_drive',
                'callback' => [$this->callbacks, 'selfDriveRate']
            ],
    */

    public function setSubPages()
    {
        $this->subpages = [
            [
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'With Drive Rates',
                'menu_title' => 'With Drive',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_rate_with_drive',
                'callback' => [$this->callbacks, 'withDriveRate']
            ], [
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'Airport & Taxi Transfer Rates',
                'menu_title' => 'Airport & Taxi',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_rate_airport_taxi',
                'callback' => [$this->callbacks, 'airportTaxi']
            ],
            [
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'Wedding Rates',
                'menu_title' => 'Wedding',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_rate_wedding',
                'callback' => [$this->callbacks, 'wedding']
            ],
            [
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'Rate Settings',
                'menu_title' => 'Settings',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_rate_settings',
                'callback' => [$this->callbacks, 'settings']
            ]/*
                   [
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'Vehicle Category',
                'menu_title' => 'Vehicle Category',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_vehicle_category',
                'callback' => [$this->callbacks, 'vehicleCategory']
            ]
                ,[
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'Rate Chart',
                'menu_title' => 'Rate Chart',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_rate_chart',
                'callback' => [$this->callbacks, 'rateChart']
            ]*//*,
            [
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'WP List Table',
                'menu_title' => 'WP List Table',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_wplisttable',
                'callback' => [$this->callbacks, 'wpListTable']
            ]*/
        ];
    }

    public function setSettings()
    {
        $args = [
            [
                'option_group' => 'readberylit_option_group',
                'option_name' => 'text_example',
                'callback' => [$this->callbacks, 'redberylitOptionGroup']
            ]
        ];
        $this->settings->setSettings($args);

    }

    public function setSections()
    {
        $args = array(
            array(
                'id' => 'redberylit_admin_index',
                'title' => 'Settings',
                'callback' => array($this->callbacks, 'redberylitAdminSection'),
                'page' => 'redberylit_plugin'
            )
        );
        $this->settings->setSections($args);
    }

    public function setFields()
    {
        $args = array(
            array(
                'id' => 'text_example',
                'title' => 'Text Example',
                'callback' => array($this->callbacks, 'redberylitTextExample'),
                'page' => 'redberylit_plugin',
                'section' => 'redberylit_admin_index',
                'args' => array(
                    'label_for' => 'text_example',
                    'class' => 'example-class'
                )
            ),
            array(
                'id' => 'first_name',
                'title' => 'First Name',
                'callback' => array($this->callbacks, 'redberylitFirstName'),
                'page' => 'redberylit_plugin',
                'section' => 'redberylit_admin_index',
                'args' => array(
                    'label_for' => 'first_name',
                    'class' => 'example-class'
                )
            )
        );
        $this->settings->setFields($args);
    }


}
