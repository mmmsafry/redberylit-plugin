<?php

dirname(__FILE__);


require_once(dirname(__FILE__) . '../../../../../wp-load.php');

class rate_chart_post
{

    public $table_prefix;
    public $wpdb;
    public $table_rate;
    public $table_rate_range;
    public $table_post;
    public $table_rate_chart;

    public function __construct()
    {
        global $table_prefix, $wpdb;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;

        /** Tables */
        $this->table_post = $this->wpdb->prefix . 'posts';
        $this->table_rate_chart = $this->wpdb->prefix . 'rate_chart';
        $this->table_rate_range = $this->wpdb->prefix . 'rate_range';
        $this->table_rate = $this->wpdb->prefix . 'rate';
    }


    public function updateRatesSpecificColumn($id, $postID, $value, $columnName)
    {
        $output = [];
        $q = "SELECT * FROM  $this->table_rate_chart  WHERE wp_post_ID='$postID'";


        $r = $this->wpdb->get_row($q);

        if (!empty($r)) {
            $q = "UPDATE $this->table_rate_chart SET $columnName='$value' WHERE wp_post_ID='$postID'";
            $output['operation'] = 'update';
            $output['q'] = $q;
        } else {
            $q = "INSERT INTO $this->table_rate_chart (`wp_post_ID`, $columnName) VALUES ($postID, $value);";
            $output['operation'] = 'insert';
            $output['q'] = $q;
        }

        $output['result'] = $this->wpdb->query($q);

        return $output;
    }


}

$obj = new rate_chart_post();
$output = $obj->updateRatesSpecificColumn($_POST['id'], $_POST['post_id'], $_POST['amount'], $_POST['column_name']);
echo json_encode(array('response' => '200', $output));