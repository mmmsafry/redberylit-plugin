<?php
dirname(__FILE__);
require_once(dirname(__FILE__) . '../../../../../wp-load.php');
if (file_exists(dirname(__FILE__) . '../../../../../wp-content/plugins/redberylit-plugin/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '../../../../../wp-content/plugins/redberylit-plugin/vendor/autoload.php';
}

//var_dump($_POST);

/**
 * reserv-car-id: 1847
 * rezerve-pickup-place: Casons Head Office
 * rezerve-pickup-date: 02/06/2020
 * rezerve-pickup-time: 12:00 PM
 * rezerve-dropoff-place: Casons Head Office
 * rezerve-dropoff-date: 12/06/2019
 * rezerve-dropoff-time: 12:00 PM
 * rezerve-rent-option: self_drive
 * reserv-car-days: 62
 * reserv-car-price-day: 2000
 * discounts: 51
 * reserv-car-price-total: 124000
 * reserv-car-price-extras: 0
 * discounts: 51
 * extra_options[252][multiselect]: 0
 * extra_options[253][multiselect]: 0
 * reserve-name:
 * reserve-phone:
 * reserve-age:
 * reserve-email:
 * rent-notes:
 * only_reservation:
 * action: autoroyal_ajax_place_reservation
 */


$baseRate = Inc\Base\CasonsRateCalculation::get_base_price($_POST['reserv-car-id'], $_POST['rezerve-rent-option']);
$totalAmount = $baseRate * $_POST['reserv-car-days'];
$extras = Inc\Base\CasonsRateCalculation::get_extra($_POST);

if (!empty($extras)) {
    foreach ($extras as $key => $extra) {
        $totalAmount += $extra;
        $extras[$key] = (float)$extra;
    }
}

/** VAT */
$vat = Inc\Base\CasonsRateCalculation::get_vat();
if ($vat > 0) {
    $vat_amount = ($baseRate * ($vat / 100)) * $_REQUEST['reserv-car-days'];
    $totalAmount += $vat_amount;
    $extras['VAT (' . $vat . '%)'] = $vat_amount;
}
$type = isset($_POST['rezerve-rent-option']) ? $_POST['rezerve-rent-option'] : 'self_drive';

$extra_data = Inc\Base\CasonsRateCalculation::get_extra_data($_POST['reserv-car-id'], $_POST['rezerve-rent-option']);

$output = array_merge(array("amount" => (float)$totalAmount, "base_rate" => (float)$baseRate, 'extras' => array_reverse($extras), 'type' => $type), $extra_data);
echo json_encode($output);

/** Note
 * Example
 *      Case :  "self_drive":
 *              extra_amount_km: 50
 *              mileage: "100 KM"
 *
 *
 */