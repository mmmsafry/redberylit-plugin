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

        $this->settings->AddPages($this->pages)->withSubPage('Rate Chart')->addSubPages($this->subpages)->register();
    }

    public function setPages()
    {
        $this->pages = [
            [
                'page_title' => 'Manage Rates',
                'menu_title' => 'Manage Rate',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_plugin',
                'callback' => [$this->callbacks, 'rateChart'], //adminDashboard
                'icon_url' => 'dashicons-media-spreadsheet',
                'position' => 30
            ]
        ];
    }

    public function setSubPages()
    {
        $this->subpages = [
            /*[
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'Custom Post Type',
                'menu_title' => 'CPT',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_cpt',
                'callback' => [$this->callbacks, 'customPostType']
            ],
            [
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'Custom Taxonomies',
                'menu_title' => 'taxonomies',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_taxonomies',
                'callback' => [$this->callbacks, 'exampleTaxonomy']
            ],
            [
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'Custom Widgets',
                'menu_title' => 'Widgets',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_widgets',
                'callback' => [$this->callbacks, 'customWidget']
            ],*/
            [
                'parent_slug' => 'redberylit_plugin',
                'page_title' => 'Vehicle Category',
                'menu_title' => 'Vehicle Category',
                'capability' => 'manage_options',
                'menu_slug' => 'redberylit_vehicle_category',
                'callback' => [$this->callbacks, 'vehicleCategory']
            ]/*,[
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
                'callback' => array( $this->callbacks, 'redberylitAdminSection' ),
                'page' => 'redberylit_plugin'
            )
        );
        $this->settings->setSections( $args );
    }
    public function setFields()
    {
        $args = array(
            array(
                'id' => 'text_example',
                'title' => 'Text Example',
                'callback' => array( $this->callbacks, 'redberylitTextExample' ),
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
                'callback' => array( $this->callbacks, 'redberylitFirstName' ),
                'page' => 'redberylit_plugin',
                'section' => 'redberylit_admin_index',
                'args' => array(
                    'label_for' => 'first_name',
                    'class' => 'example-class'
                )
            )
        );
        $this->settings->setFields( $args );
    }


}
