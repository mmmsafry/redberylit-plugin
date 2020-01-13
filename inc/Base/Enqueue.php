<?php

namespace Inc\Base;

class Enqueue extends BaseController
{
    function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
        //add_action('wp_enqueue_scripts', [$this, 'enqueue_front_end']);


    }

    function enqueue()
    {
        wp_enqueue_style('myPluginStyle', $this->plugin_url . 'assets/mystyle.css');
        wp_enqueue_script('myPluginScript', $this->plugin_url . 'assets/myscript.js');
    }

    function globalPost(){
        global $wp_query;
        echo $wp_query->post->ID;
    }

    function enqueue_front_end()
    {
        wp_enqueue_style('myPluginStyle', $this->plugin_url . 'assets/mystyle-fontend.css');
        wp_enqueue_style('googleMapStyle', $this->plugin_url . 'assets/google-map.css');

        wp_enqueue_script('myPluginScript', $this->plugin_url . 'assets/myscript-fontend.js');
        wp_enqueue_script('googleMapScript', $this->plugin_url . 'assets/google-map.js');
    }


}



