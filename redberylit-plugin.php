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


/** need to be changed to proper  standard */
function base_url_redberylit()
{
    return get_site_url();
}


/*Show some script only in Home page */
add_action('wp_enqueue_scripts', 'enqueue_rb_scripts');
function enqueue_rb_scripts()
{
    if (is_front_page()) {
        wp_enqueue_style('myPluginStyle', plugins_url('/redberylit-plugin/assets/mystyle-fontend.css'));
        wp_enqueue_style('googleMapStyle', plugins_url('/redberylit-plugin/assets/google-map.css'));
        wp_enqueue_script('myPluginScript', plugins_url('/redberylit-plugin/assets/myscript-fontend.js'));
        wp_enqueue_script('googleMapScript', plugins_url('/redberylit-plugin/assets/google-map.js') . '');
    }
}


/** ---------------------------------- New Table */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Customers_List extends WP_List_Table
{

    /** Class constructor */
    public function __construct()
    {

        parent::__construct([
            'singular' => __('Customer', 'sp'), //singular name of the listed records
            'plural' => __('Customers', 'sp'), //plural name of the listed records
            'ajax' => false //should this table support ajax?

        ]);

    }

    /**
     * Retrieve customer’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_customers($per_page = 5, $page_number = 1)
    {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}customers";

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;


        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_customer($id)
    {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}customers",
            ['ID' => $id],
            ['%d']
        );
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count()
    {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}customers";

        return $wpdb->get_var($sql);
    }

    /** Text displayed when no customer data is available */
    public function no_items()
    {
        _e('No customers avaliable.', 'sp');
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name($item)
    {

        // create a nonce
        $delete_nonce = wp_create_nonce('sp_delete_customer');

        $title = '<strong>' . $item['name'] . '</strong>';

        $actions = [
            'delete' => sprintf('<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['ID']), $delete_nonce)
        ];

        return $title . $this->row_actions($actions);
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'address':
            case 'city':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns()
    {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'name' => __('Name', 'sp'),
            'address' => __('Address', 'sp'),
            'city' => __('City', 'sp')
        ];

        return $columns;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'name' => array('name', true),
            'city' => array('city', false)
        );

        return $sortable_columns;
    }


    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = [
            'bulk-delete' => 'Delete'
        ];

        return $actions;
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items()
    {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('customers_per_page', 5);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ]);


        $this->items = self::get_customers($per_page, $current_page);
    }


    public function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, 'sp_delete_customer')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_customer(absint($_GET['customer']));

                wp_redirect(esc_url(add_query_arg()));
                exit;
            }

        }

        // If the delete bulk action is triggered
        if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
            || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')
        ) {

            $delete_ids = esc_sql($_POST['bulk-delete']);

            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_customer($id);

            }

            wp_redirect(esc_url(add_query_arg()));
            exit;
        }
    }

}


class SP_Plugin
{

    // class instance
    static $instance;

    // customer WP_List_Table object
    public $customers_obj;

    // class constructor
    public function __construct()
    {
        add_filter('set-screen-option', [__CLASS__, 'set_screen'], 10, 3);
        add_action('admin_menu', [$this, 'plugin_menu']);
    }

    public static function set_screen($status, $option, $value)
    {
        return $value;
    }

    public function plugin_menu()
    {

        $hook = add_menu_page(
            'Sitepoint WP_List_Table Example',
            'SP WP_List_Table',
            'manage_options',
            'wp_list_table_class',
            [$this, 'plugin_settings_page']
        );

        add_action("load-$hook", [$this, 'screen_option']);

    }

    /**
     * Screen options
     */
    public function screen_option()
    {

        $option = 'per_page';
        $args = [
            'label' => 'Customers',
            'default' => 5,
            'option' => 'customers_per_page'
        ];

        add_screen_option($option, $args);

        $this->customers_obj = new Customers_List();
    }

    /**
     * Plugin settings page
     */
    public function plugin_settings_page()
    {
        ?>

        <?php
    }


    /** Singleton instance */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


}

/*$customer = new Customers_List();*/


add_action( 'plugins_loaded', function () {
    SP_Plugin::get_instance();
} );

/** ---------------------------------- End New Table */

/*
add_action( 'wp', 'your_function' );
*/


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
