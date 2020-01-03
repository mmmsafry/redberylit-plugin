<?php

namespace Inc\Base;
class Enqueue extends BaseController
{
    function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);

    }

    function enqueue()
    {
        wp_enqueue_style('myPluginStyle', $this->plugin_url . 'assets/mystyle.css');
        wp_enqueue_script('myPluginScript', $this->plugin_url . 'assets/myscript.js');
    }


}