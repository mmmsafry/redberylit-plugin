<?php

namespace Inc\Base;

class ShortCode extends BaseController
{
    public function register()
    {
        add_shortcode('base_url', 'get_base_url_rb_plugin');

    }

}

