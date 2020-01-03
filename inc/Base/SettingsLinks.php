<?php

namespace Inc\Base;

use  Inc\Base\BaseController;

class SettingsLinks extends BaseController
{

    public function register()
    {
        add_filter("plugin_action_links_$this->plugin", [$this, 'settings_link']);
    }

    public function settings_link($link)
    {
        $settings_link = '<a href="admin.php?page=redberylit_plugin"> Manage Rate </a>';
        array_push($link, $settings_link);
        return $link;
    }
}