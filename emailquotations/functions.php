<?php
/**
 * Plugin Name: Casons Email Quotations
 * Plugin URI:
 * Description: Casons Email Quotations
 * Version: 1.0.1
 *
 */

global $casons_emailquote_db_version;
$casons_emailquote_db_version = '1.1';

function casons_emailquote_install()
{
    global $wpdb;
    global $casons_emailquote_db_version;

    $table_name = $wpdb->prefix . 'emailquote'; // do not forget about tables prefix


    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) NOT NULL AUTO_INCREMENT,
          sender_email VARCHAR(100) NOT NULL,
                   recepient_email VARCHAR(100) NOT NULL,
                    recepient_name VARCHAR(500) NOT NULL,
                       recepient_mobile VARCHAR(10) ,
                          special_note VARCHAR(900) ,
      PRIMARY KEY  (id)
    );";

    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // save current database version for later use (on upgrade)
    add_option('casons_emailquote_db_version', $casons_emailquote_db_version);


}


register_activation_hook(__FILE__, 'casons_emailquote_install');

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


//==
class CasonsEmailQuotations_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'quotation',
            'plural' => 'quotations',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * [OPTIONAL] this is example, how to render specific column
     *
     * method name must be like this: "column_[column_name]"
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_sender_email($item)
    {
        return '<em>' . $item['sender_email'] . '</em>';
    }

    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_recepient_email($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'edit' => sprintf('<a href="?page=persons_form&id=%s">%s</a>', $item['id'], __('Edit', 'casons_emailquote')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'casons_emailquote')),
        );

        return sprintf('%s %s',
            $item['recepient_email'],
            $this->row_actions($actions)
        );
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'sender_email' => __('Sender E-Mail', 'casons_emailquote'),
            'recepient_email' => __('Recepient E-Mail', 'casons_emailquote'),
            'recepient_name' => __('Recepient Name', 'casons_emailquote'),
            'recepient_mobile' => __('Mobile', 'casons_emailquote'),
            'special_note' => __('Note', 'casons_emailquote'),

        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'sender_email' => array('recepient_email', true),
            'recepient_email' => array('recepient_email', false),
            'recepient_name' => array('recepient_name', false),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'emailquote'; // do not forget about tables prefix

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'emailquote'; // do not forget about tables prefix

        $per_page = 5; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'sender_email';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}


//==============admin menu

function casons_emailquote_admin_menu()
{
    add_menu_page(__('Email Quotations', 'casons_emailquote'), __('Email Quotations', 'casons_emailquote'), 'activate_plugins', 'emailquote', 'casons_emailquote_page_handler');
    add_submenu_page('emailquote', __('Email Quotations', 'casons_emailquote'), __('Email Quotations', 'casons_emailquote'), 'activate_plugins', 'emailquote', 'casons_emailquotes_page_handler');
    // add new will be described in next part
    add_submenu_page('emailquote', __('Add new', 'casons_emailquote'), __('Add new', 'casons_emailquote'), 'activate_plugins', 'emailquote_form', 'casons_emailquotes_form_page_handler');
}

add_action('admin_menu', 'casons_emailquote_admin_menu');

/**
 * List page handler
 *
 * This function renders our custom table
 * Notice how we display message about successfull deletion
 * Actualy this is very easy, and you can add as many features
 * as you want.
 *
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples
 */
//+++++++++++++
function casons_emailquote_page_handler()
{
    global $wpdb;

    $table = new CasonsEmailQuotations_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'casons_emailquote'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
    <div class="wrap">

        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Email Quotations', 'casons_emailquote') ?> <a class="add-new-h2"
                                                                    href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=emailquote_form'); ?>"><?php _e('Add new', 'casons_emailquote') ?></a>
        </h2>
        <?php echo $message; ?>

        <form id="emailquote-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $table->display() ?>
        </form>

    </div>
    <?php
}


function casons_emailquotes_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'emailquote'; // do not forget about tables prefix

    $message = '';
    $notice = '';
//emailquote_form
    // this is default $item which will be used for new records
    $default = array(
        'id' => 0,
        'sender_email' => '',
        'recepient_email' => '',
        'recepient_name' => null,
        'recepient_mobile' => '',
        'special_note' => '',
    );

    // here we are verifying does this request is post back and have correct nonce
    if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = casons_emailquote_validate_person($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'casons_emailquote');
                } else {
                    $notice = __('There was an error while saving item', 'casons_emailquote');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Item was successfully updated', 'casons_emailquote');
                } else {
                    $notice = __('There was an error while updating item', 'casons_emailquote');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    } else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'casons_emailquote');
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('casons_emailquote_meta_box', 'Email Quotation data', 'casons_emailquote_form_meta_box_handler', 'person', 'normal', 'default');

    ?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e('Quotations', 'casons_emailquote') ?> <a class="add-new-h2"
                                                              href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=emailquote'); ?>"><?php _e('back to list', 'casons_emailquote') ?></a>
        </h2>

        <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
        <?php endif; ?>
        <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
        <?php endif; ?>

        <form id="form" method="POST">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ?>"/>
            <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
            <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

            <div class="metabox-holder" id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">
                        <?php /* And here we call our custom meta box */ ?>
                        <?php do_meta_boxes('quotations', 'normal', $item); ?>
                        <input type="submit" value="<?php _e('Save', 'casons_emailquote') ?>" id="submit"
                               class="button-primary" name="submit">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function casons_emailquote_form_meta_box_handler($item)
{
    ?>

    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="name"><?php _e('Name', 'casons_emailquote') ?></label>
            </th>
            <td>
                <input id="recepient_name" name="recepient_name" type="text" style="width: 95%"
                       value="<?php echo esc_attr($item['recepient_name']) ?>"
                       size="50" class="code" placeholder="<?php _e('Your name', 'casons_emailquote') ?>" required>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="sender_email"><?php _e('Sender E-Mail', 'casons_emailquote') ?></label>
            </th>
            <td>
                <input id="email" name="sender_email" type="email" style="width: 95%"
                       value="<?php echo esc_attr($item['sender_email']) ?>"
                       size="50" class="code" placeholder="<?php _e('Sender E-Mail', 'casons_emailquote') ?>" required>
            </td>
        </tr>


        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="recepient_email"><?php _e('Sender E-Mail', 'casons_emailquote') ?></label>
            </th>
            <td>
                <input id="email" name="recepient_email" type="email" style="width: 95%"
                       value="<?php echo esc_attr($item['recepient_email']) ?>"
                       size="50" class="code" placeholder="<?php _e('Recepient E-Mail', 'casons_emailquote') ?>"
                       required>
            </td>
        </tr>


        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="recepient_mobile"><?php _e('Recepient Mobile', 'casons_emailquote') ?></label>
            </th>
            <td>
                <input id="email" name="recepient_mobile" type="text" style="width: 95%"
                       value="<?php echo esc_attr($item['recepient_mobile']) ?>"
                       size="50" class="code" placeholder="<?php _e('Recepient Mobile', 'casons_emailquote') ?>"
                       required>
            </td>
        </tr>


        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="special_note"><?php _e('Special Note', 'casons_emailquote') ?></label>
            </th>
            <td>
                <textarea id="special_note"
                          name="special_note"><?php echo esc_attr($item['recepient_mobile']) ?></textarea>
            </td>
        </tr>


        </tbody>
    </table>
    <?php
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function casons_emailquote_validate_quotation($item)
{
    $messages = array();

    if (empty($item['sender_email'])) $messages[] = __('Sender E-Mail is required', 'casons_emailquote');
    if (!empty($item['recepient_email']) && !is_email($item['recepient_email'])) $messages[] = __('E-Mail is in wrong format', 'cltd_example');

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}


function casons_emailquote_languages()
{
    load_plugin_textdomain('casons_emailquote', false, dirname(plugin_basename(__FILE__)));
}

add_action('init', 'casons_emailquote_languages');


//quote form list front end via shortcode


function ShowEmailQuotations()
{
    if (is_user_logged_in()) {


        $user = wp_get_current_user();
        // echo var_dump( $user );

        $ID = $user->ID;
        // $user_login=$user->user_login;
        $user_roles = $user->roles;

// Check if the role you're interested in, is present in the array.
        if (in_array('administrator', $user_roles, true)) {
            $html = '';

            $html .= '<div class="col-lg-3 col-md-4 col-sm-6 mainform">
                <div class="form-group">
                    <label for="sender_email">Sender Email </label>
                    <input type="email" class="form-control" id="sender_email">
                    <span class="error" style="color:red;display:none;" id="sender_email_error">Please enter a valid Email Address </span>

                </div>


                <div class="form-group">
                    <label for="recepient_email">Recepient Email</label>
                    <input type="email" class="form-control" id="recepient_email" aria-describedby="emailHelp">

                    <span class="error" style="color:red;display:none;" id="recepient_email_error">Please enter a valid Email Address </span>

                </div>


                <div class="form-group">
                    <label for="recepient_name">Recepient Name</label>
                    <input type="text" class="form-control" id="recepient_name">

                    <span class="error" style="color:red;display:none;"
                          id="recepient_name_error">Please enter a Name </span>

                </div


                <div class="form-group">
                    <label for="recepient_mobile">Recepient Mobile</label>
                    <input type="text" class="form-control" id="recepient_mobile">
                    <span class="error" style="color:red;display:none;" id="recepient_mobile_error">Please enter a Mobile </span>

                </div>


                <div class="form-group">
                    <label for="specialnote">Special Note</label>
                    <textarea name="specialnote" id="specialnote"></textarea>

                </div>

                <div style="clear:both;"></div>
                <button type="submit" class="btn btn-primary" id="mail_quotation">Submit</button>


                <div id="display_selected"></div>
            ';

            $html .= "<script>

                function isEmail(email) {
                  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                  return regex.test(email);
                }
                
                
                
                function validatePhone(txtPhone) {
                    var a = document.getElementById(txtPhone).value;
                    var filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
                    if (filter.test(a)) {
                        return true;
                    }
                    else {
                        return false;
                    }
                }



jQuery('document').ready(function(){

// var carid=jQuery('.autoroyal-vehicle-block').attr('data-car-id');


jQuery('<input type=\"checkbox\"  class=\"quote\">' ).insertAfter( '.autoroyal-vehicle-block');

jQuery('.quote').click(function(){
	console.log('click');
var carid=jQuery(this).parent().attr('data-car-id');
// var name=jQuery(this).closest('.autoroyal-vehicle-block-meta .heading').text();
var name= jQuery(this).siblings('.autoroyal-vehicle-block').children('.autoroyal-vehicle-block-meta').text();
console.log(carid);
console.log(name);


//get name

 $.post( 
                  '" . get_bloginfo('url') . "/wp-content/plugins/emailquotations/loadcar.php',
                  { carid: carid },
                  function(data) {
                      $('#display_selected').append(data);
                  	//console.log(data);
                  }
               );




	});


jQuery('#mail_quotation').click(function(e){
// e.preventdefault();




var sender_email=jQuery('#sender_email').val();
var recepient_email=jQuery('#recepient_email').val();
var recepient_name=jQuery('#recepient_name').val();
var recepient_mobile=jQuery('#recepient_mobile').val();
var specialnote=jQuery('#specialnote').val();





if(isEmail(sender_email)==false){
 jQuery('#sender_email_error').attr('style','color:red;');

}
if(isEmail(recepient_email)==false){
 jQuery('#recepient_email_error').attr('style','color:red;');
}
if(recepient_name ==''){

	 jQuery('#recepient_name_error').attr('style','color:red;');
}

if(validatePhone==false){

 jQuery('#recepient_mobile_error').attr('style','color:red;');
}


if((isEmail(sender_email)==true) && (isEmail(recepient_email)==true)  ){

	 $.post( 
                  '" . get_bloginfo('url') . "/wp-content/plugins/emailquotations/loadcar.php',
                  { quotation: '1' ,sender_email:sender_email,recepient_email:recepient_email,
recepient_name:recepient_name,recepient_mobile:recepient_mobile,specialnote:specialnote
                  },
                  function(data) {

                  	if(data==1){
                     
                  	alert('success');
                  }
                  else{
	           alert('error');
                  }


                  	}
               );



}













	});







	});


</script></div>";

        } else {
            $html = '';
        }


    } else {
        $html = '';
    }


    return $html;
}

add_shortcode('casons_emailquote', 'ShowEmailQuotations');


//==test change woo currency


// add_filter('woocommerce_currency_symbol', 'add_cw_currency_symbol', 10, 2);
// function add_cw_currency_symbol( $custom_currency_symbol, $custom_currency ) {
//      // switch( $custom_currency ) {
//      //     case 'CLOUDWAYS': $custom_currency_symbol = 'CW$'; break;
//      // }
//      return '$$$$$';
// }


add_filter('woocommerce_currency_symbol', 'my_custom_currency_symbol', 10, 2);
function my_custom_currency_symbol($symbol, $currency)
{
    return 'Rs';
}


