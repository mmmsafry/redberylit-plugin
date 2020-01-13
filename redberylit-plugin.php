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


function base_url_redberylit()
{
    return get_site_url() ;

}






/*
use Inc\Activate;
use Inc\Deactivate;
use Inc\Admin\Admin;

if (!class_exists('redberylit')) {
    class redberylit
    {
        public $plugin;

        public function __construct()
        {
            $this->plugin = plugin_basename(__FILE__);
            //add_action('init', [$this, 'custom_post_type']);
        }


        function register()
        {
            add_action('wp_enqueue_scripts', [$this, 'enqueue']);
            add_action('admin_menu', [$this, 'add_rate_pages']);
            add_filter("plugin_action_links_$this->plugin", [$this, 'settings_link']);
        }

        function settings_link($link)
        {
            $settings_link = '<a href="admin.php?page=redberylit_plugin"> Manage Rate </a>';
            array_push($link, $settings_link);
            return $link;

        }

        public function add_rate_pages()
        {
            add_menu_page('Manage Rates', 'Manage Vehicle Rate ', 'manage_options', 'redberylit_plugin', [$this, 'admin_manage_rate'], 'dashicons-media-spreadsheet', 30);
        }

        public function admin_manage_rate()
        {
            // require template
            require_once plugin_dir_path(__FILE__) . 'template/rate_page.php';
        }

        function enqueue()
        {
            wp_enqueue_style('myPluginStyle', plugins_url('/asset/mystyle.css', __FILE__));
            wp_enqueue_script('myPluginScript', plugins_url('/asset/myscript.js', __FILE__));
        }

        function activate()
        {
            Activate::activate();
        }


        function deactivate()
        {
            Deactivate::deactivate();
        }

        function uninstall()
        {

        }

        function custom_post_type()
        {
            $labels = array(
                'name' => _x('Books', 'Post type general name', 'textdomain'),
                'singular_name' => _x('Book', 'Post type singular name', 'textdomain'),
                'menu_name' => _x('Books', 'Admin Menu text', 'textdomain'),
                'name_admin_bar' => _x('Book', 'Add New on Toolbar', 'textdomain'),
                'add_new' => __('Add New', 'textdomain'),
                'add_new_item' => __('Add New Book', 'textdomain'),
                'new_item' => __('New Book', 'textdomain'),
                'edit_item' => __('Edit Book', 'textdomain'),
                'view_item' => __('View Book', 'textdomain'),
                'all_items' => __('All Books', 'textdomain'),
                'search_items' => __('Search Books', 'textdomain'),
                'parent_item_colon' => __('Parent Books:', 'textdomain'),
                'not_found' => __('No books found.', 'textdomain'),
                'not_found_in_trash' => __('No books found in Trash.', 'textdomain'),
                'featured_image' => _x('Book Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
                'set_featured_image' => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
                'use_featured_image' => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
                'archives' => _x('Book archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
                'insert_into_item' => _x('Insert into book', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
                'uploaded_to_this_item' => _x('Uploaded to this book', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
                'filter_items_list' => _x('Filter books list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain'),
                'items_list_navigation' => _x('Books list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Admin list navigation”. Added in 4.4', 'textdomain'),
                'items_list' => _x('Books list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Admin list”. Added in 4.4', 'textdomain'),
            );

            $args = array(
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => 'book'),
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
            );
            register_post_type('book', $args);
        }

    }


    $redberylitPlugin = new redberylit();
    $redberylitPlugin->register();
}
*/
