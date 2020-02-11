<?php

dirname(__FILE__);


require_once(dirname(__FILE__) . '../../../../../wp-load.php');

//var_dump($_POST);

global $wpdb;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (empty($_POST["c_name"]) || empty($_POST["c_email"]) ) {
    echo "2"; // This field is required
  } else {

    $query = $wpdb->insert('wp_fleet', array(
        'post_id' => $_POST['c_post_id'],
        'name' => $_POST['c_name'],
        'country' => $_POST['country_list'],
        'email' => $_POST['c_email'],
        'address' => $_POST['c_address'],
        'contact_number' => $_POST['c_contact_number'],
        'type_of_service' => $_POST['c_type_of_service'],
        'selected_model' => $_POST['c_selected_model'],
        'start_date' => $_POST['starting_date'],
        'end_date' => $_POST['ending_date'],
        'message' => $_POST['c_message']
    ));

    if($query){
        echo "1"; // success
    }else {
        echo "0"; // failed
    }

  }

}



