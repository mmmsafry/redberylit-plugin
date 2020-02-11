<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../../../wp-load.php");
global $wpdb;

if(isset($_POST['carid'])){
	$ids=$_POST['carid'];




 $result = $wpdb->get_results("SELECT * FROM `wp_posts` WHERE `ID` ='$ids'");
$html='';
 foreach ( $result as  $value) {

 	$html.= $value->post_title.'<br>';
 



}

echo $html;

}



if(isset($_POST['quotation'])){


	$sender_email=$_POST['sender_email'];
	$recepient_email=$_POST['recepient_email'];
$recepient_name=$_POST['recepient_name'];
$recepient_mobile=$_POST['recepient_mobile'];
 $specialnote=$_POST['specialnote'];


$table=$wpdb->prefix . 'emailquote';
$result=$wpdb->insert($table, array(
    'sender_email' => $sender_email,
    'recepient_email' => $recepient_email,
    'recepient_name' => $recepient_name,


      'recepient_mobile' => $recepient_mobile,
    'special_note' => $specialnote
 
));

echo $result;



}







