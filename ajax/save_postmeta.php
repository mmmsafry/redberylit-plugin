<?php

dirname(__FILE__);


require_once(dirname(__FILE__) . '../../../../../wp-load.php');

class save_postmeta
{

    public $table_prefix;
    public $wpdb;
    public $post_meta;


    public function __construct()
    {
        global $table_prefix, $wpdb;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;

        /** Tables */
        $this->table_post = $this->wpdb->prefix . 'posts';
        $this->post_meta = $this->wpdb->prefix . 'postmeta';

    }

    public function updatePostMeta($post_id, $key, $value)
    {
        return update_post_meta($post_id, $key, $value);
    }

}

$obj = new save_postmeta();
$output = $obj->updatePostMeta($_POST['post_id'], $_POST['key'], $_POST['value']);
echo json_encode(array('response' => '200', $output));