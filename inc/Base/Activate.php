<?php
/*
 * @package           redberylit
 */
namespace Inc\Base;
class Activate
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        //redberylit_Install::install();
        flush_rewrite_rules();
    }

}
