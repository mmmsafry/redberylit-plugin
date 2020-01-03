<?php
/**
 * #@package redberylit_plugin
 */

namespace Inc\Api\Callbacks;

use Inc\Base\BaseController;

class AdminCallbacks extends BaseController
{
    public function adminDashboard()
    {
        return require_once("$this->plugin_path./templates/admin.php");
    }

    public function customPostType()
    {
        return require_once("$this->plugin_path./templates/CustomPostType.php");
    }

    public function exampleTaxonomy()
    {
        return require_once("$this->plugin_path./templates/ExampleTaxonomy.php");
    }

    public function customWidget()
    {
        return require_once("$this->plugin_path./templates/widget_manager.php");
    }

    public function redberylitOptionGroup($input)
    {
        return $input;
    }

    public function redberylitAdminSection()
    {
        echo 'example Section this is!<br/>';
    }


    public function redberylitTextExample()
    {
        $value = esc_attr(get_option('text_example'));
        echo '<input type="text" class="regular-text" name="text_example" value="' . $value . '" placeholder="Write Something Here!">';
    }

    public function redberylitFirstName()
    {
        $value = esc_attr(get_option('first_name'));
        echo '<input type="text" class="regular-text" name="first_name" value="' . $value . '" placeholder="Write your First Name">';
    }

    public function vehicleCategory()
    {
        return require_once("$this->plugin_path./templates/vehicle_category.php");
    }

    public function rateChart()
    {
        return require_once("$this->plugin_path./templates/rate_chart.php");
    }

    public function wpListTable(){
        return require_once("$this->plugin_path./templates/wp_list_table_example2.php");

    }
}
