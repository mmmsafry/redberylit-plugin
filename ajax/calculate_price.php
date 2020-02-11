<?php

dirname(__FILE__);


require_once(dirname(__FILE__) . '../../../../../wp-load.php');

class calculate_price
{

    public $table_prefix;
    public $wpdb;
 

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




 public function calculate_total($total,$vehicle_id,$service_type){

  $discount=$this->_getDiscount($vehicle_id);
  $totaldiscount=$total * $discount;
  return $total-$totaldiscount;
 }




private function _getDiscount($vehicle_id){
 return .05;
}




}

$obj = new calculate_price();
// $output = $obj->updateRatesSpecificColumn($_POST['id'], $_POST['post_id'], $_POST['amount'], $_POST['column_name']);
// echo json_encode(array('response' => '200', $output));


if(isset($_POST)){
$total=$_POST['reserv-car-price-total'];
$vehicle_id=$_POST['reserv-car-id'];
$service_type=$_POST['rezerve-rent-option'];

echo  $obj->calculate_total($total,$vehicle_id,$service_type);    
}

