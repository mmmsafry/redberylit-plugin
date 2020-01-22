<?php

namespace Inc\Base;

class Enqueue extends BaseController
{
    function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
        add_action('wp_enqueue_scripts', [$this, 'custom_style']);

    }

    function enqueue()
    {
        wp_enqueue_style('myPluginStyle', $this->plugin_url . 'assets/mystyle.css');
        wp_enqueue_script('myPluginScript', $this->plugin_url . 'assets/myscript.js');
    }

    function custom_style()
    {
        wp_enqueue_style('myCustomStyle', $this->plugin_url . 'assets/custom-style-rb-plugin.css');

        /* Only in home page */
        if (is_front_page()) {
            wp_enqueue_style('myPluginStyle', $this->plugin_url . 'assets/mystyle-fontend.css');
            wp_enqueue_style('googleMapStyle', $this->plugin_url . 'assets/google-map.css');

            wp_enqueue_script('myPluginScript', $this->plugin_url . 'assets/myscript-fontend.js');
            wp_enqueue_script('googleMapScript', $this->plugin_url . 'assets/google-map.js');
        }

    }


}



