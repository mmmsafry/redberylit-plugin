<?php

namespace Inc\Base;

class ShortCode extends BaseController
{
    public function register()
    {
        add_shortcode('base_url', 'base_url_redberylit');
    }


}

