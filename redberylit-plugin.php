<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also inc all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           redberylit
 *
 * @wordpress-plugin
 * Plugin Name:       redberylit-plugin
 * Plugin URI:        https://redberylit.com/plugin
 * Description:       To Setup Price chart for rent a car service
 * Version:           1.0.0
 * Author:            Safry
 * Author URI:        https://mmmsafry.wordpress.com/about/
 * License:
 * License URI:
 * Text Domain:       redberylit
 * Domain Path:
 * The code that runs during plugin activation.
 * This action is documented in inc/Activate.php
 */
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

if (class_exists('Inc\\Init')) {
    Inc\Init::register_services();
}

function activate_redberylit_plugin()
{
    Inc\Base\Activate::activate();
}

register_activation_hook(__FILE__, 'activate_redberylit_plugin');


function deactivate_redberylit_plugin()
{
    Inc\Base\Deactivate::deactivate();
}

register_deactivation_hook(__FILE__, 'deactivate_redberylit_plugin');