<?php

dirname(__FILE__);


require_once(dirname(__FILE__) . '../../../../../wp-load.php');

class save_rate_range
{

    public $table_prefix;
    public $wpdb;
    public $table_rate_range;

    public function __construct()
    {
        global $table_prefix, $wpdb;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;

        /** Tables */
        $this->table_rate_range = $this->wpdb->prefix . 'rate_range';
    }


    public function updateRateRange($id, $value, $columnName = 'description')
    {
        $q = "UPDATE $this->table_rate_range SET $columnName='$value' WHERE id='$id'";
        return $this->wpdb->query($q);
    }


}

$obj = new save_rate_range();
$output = $obj->updateRateRange($_POST['id'], $_POST['value']);
echo json_encode(array('response' => '200', $output));