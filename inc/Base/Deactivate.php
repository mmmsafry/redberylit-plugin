<?php
/*
 * @package           redberylit
 */

namespace Inc\Base;
class Deactivate
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     */
    public static function deactivate()
    {
        flush_rewrite_rules();
    }

}
