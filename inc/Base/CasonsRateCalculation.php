<?php
/**
 * @package           redberylit
 */

namespace Inc\Base;
class CasonsRateCalculation
{

    public static function get_vat()
    {
        global $wpdb;
        $q = "SELECT *  FROM wp_rate  WHERE rate_chart_id = 0  AND rate_range_id = 0  AND `type` = 'VAT'";
        $vat = $wpdb->get_row($q);
        return $vat ? $vat->amount : 0;

    }

    /**
     * rezerve-pickup-place: Casons Head Office
     * rezerve-dropoff-place: Kandy
     * reserv-car-id: 1847
     * rezerve-pickup-date: 02/07/2020
     * rezerve-pickup-time: 12:00 PM
     * rezerve-dropoff-place: 1
     * rezerve-dropoff-date: 02/10/2020
     * rezerve-dropoff-time: 12:00 PM
     * rezerve-rent-option: self_drive
     * reserv-car-days: 3
     * reserv-car-price-day: 2000
     * discounts: 0
     * reserv-car-price-total: 6000
     * reserv-car-price-extras: 0
     * discounts: 0
     * extra_options[252][multiselect]: 0
     * extra_options[253][multiselect]: 0
     */

    public static function get_extra($post)
    {
        $type = isset($post['rezerve-rent-option']) ? $post['rezerve-rent-option'] : 'self_drive';
        global $wpdb;

        $extras = [];
        switch ($type) {

            case "self_drive":
                if (isset($post['rezerve-pickup-place'])) {
                    $tmpRate = self::get_extra_rate($post['reserv-car-id'], $post['rezerve-pickup-place']);
                    if (!empty($tmpRate)) {
                        $extras[$post['rezerve-pickup-place'] . " (Pickup charge)"] = $tmpRate;
                    }
                }

                if (isset($post['rezerve-dropoff-place'])) {
                    $tmpRate = self::get_extra_rate($post['reserv-car-id'], $post['rezerve-dropoff-place']);
                    if (!empty($tmpRate)) {
                        $extras[$post['rezerve-dropoff-place'] . " (Return charge)"] = $tmpRate;
                    }
                }
        }
        return $extras;
    }


    public static function get_base_price($postID, $type = 'self_drive')
    {
        global $wpdb;
        $dayCount = self::number_of_days();
        if (isset($_POST['reserv-car-days'])) {
            $dayCount = $_POST['reserv-car-days'];
        }

        switch ($type) {

            case "self_drive":
                $where_set[] = "wp_rate.`type` = 'SD' ";
                $where_set[] = "rate_chart_id = $postID";
                $where_set[] = "wp_rate_range.is_active =1";

                $where = CasonsRateCalculation::generate_and_query($where_set);
                $sql = "SELECT
                            wp_rate.*,
                            wp_rate_range.description,
                            wp_rate_range.min_days,
                            wp_rate_range.max_days
                        FROM
                            wp_rate
                            LEFT JOIN wp_rate_range ON wp_rate_range.id = wp_rate.rate_range_id
                        WHERE
                            " . $where . "
                            AND (wp_rate_range.min_days='" . $dayCount . "' OR wp_rate_range.min_days<'" . $dayCount . "')
                            AND (wp_rate_range.max_days='" . $dayCount . "' OR wp_rate_range.max_days>'" . $dayCount . "')";
                $result = $wpdb->get_row($sql);
                $baseRate = !empty($result) ? $result->amount : 0;
                break;

            case "with_driver" :
                $packages = isset($_REQUEST['packages']) ? $_REQUEST['packages'] : 19; // 19 => 100 KM packages by default
                $rate_chart_id = self::get_rate_chart_id($postID); // Get Rate chart ID

                $q = "SELECT amount FROM wp_rate WHERE rate_chart_id = '" . $rate_chart_id . "' AND rate_range_id = '" . $packages . "'  AND `type`='WD_BR' ";
                $rate = $wpdb->get_row($q);
                $baseRate = $rate ? $rate->amount : 0;

                /* Driver Charges */
                $q = "SELECT * FROM  wp_rate_chart  WHERE wp_post_ID='$postID'";
                $rate_chart = $wpdb->get_row($q);
                if (!empty($rate_chart)) {
                    if ($rate_chart->driver_charges > 0) {
                        $baseRate += $rate_chart->driver_charges;
                    }
                }
                break;

            case "airport_transfers":
            case "transfers":
                $distance_input = isset($_REQUEST['km_distance']) ? (double)$_REQUEST['km_distance'] : 0;
                $q = "SELECT MIN(min_days) as min_km FROM wp_rate_range  WHERE `type` = 'TAXI_KM'";
                $rate_range = $wpdb->get_row($q);

                $distance = isset($rate_range->min_km) && $rate_range->min_km > $distance_input ? $rate_range->min_km : $distance_input;


                $q = "SELECT MAX(max_days) as max_km FROM wp_rate_range  WHERE `type` = 'TAXI_KM'";
                $max_distance = $wpdb->get_row($q);
                if ($max_distance) {
                    $max_distance = $max_distance->max_km;
                    if ($distance > $max_distance) {
                        $extra_km = $distance - $max_distance;
                        /*Extra KM Charge */
                        $q = "SELECT amount  FROM wp_rate  WHERE rate_chart_id =0 AND  rate_range_id=0 AND `type` = 'EXTRA_KM'";
                        $extra_charge = $wpdb->get_row($q);
                        $extra_charge = $extra_charge && $extra_charge->amount > 0 ? $extra_charge->amount * $extra_km : 0;
                        $distance = $max_distance;
                    }

                }

                $multiplier = self::get_multiplier($distance);


                /*Get per KM Rate from Meta Data  */
                if ((isset($_GET['form_source']) && $_GET['form_source'] == 'airport_transfers') || (isset($_POST['rezerve-rent-option']) && $_POST['rezerve-rent-option'] == 'airport_transfers')) {
                    $metaKey = 'rc_airport_taxi_per_km_airport';
                    $taxi = false;
                } else {
                    $metaKey = 'rc_airport_taxi_per_km_taxi';
                    $taxi = true;
                }
                $metaData = get_post_meta($postID, $metaKey);
                $km_charge = !empty($metaData) ? $metaData[0] : 0;
                $baseRate = isset($extra_charge) ? $extra_charge + ($distance * $km_charge * $multiplier) : $distance * $km_charge * $multiplier;
                if ($taxi) {
                    //echo $baseRate.$_REQUEST['differentReturnLocation'];
                    $baseRate = isset($_REQUEST['differentReturnLocation']) && $_REQUEST['differentReturnLocation'] == 'on' ? $baseRate * 2 : $baseRate;
                    //echo $baseRate;
                }

                break;


            case "wedding":
                /**
                 *  From wp_rate_range table
                 *  44    (4 Hours)    -   rc_wedding_hour_rate_4
                 *  45    (8 Hours)    -   rc_wedding_hour_rate_8
                 *  46    (12 Hours)  -   rc_wedding_hour_rate_12
                 */

                $packages = $_REQUEST['packages'];
                switch ($packages) {
                    case 44:
                        $metaKey = "rc_wedding_hour_rate_4";
                        break;
                    case 45:
                        $metaKey = "rc_wedding_hour_rate_8";
                        break;
                    case 46:
                        $metaKey = "rc_wedding_hour_rate_12";
                        break;
                    default:
                        $metaKey = "rc_wedding_hour_rate_4";
                }

                $metaData = get_post_meta($postID, $metaKey);
                $baseRate = !empty($metaData) ? (double)$metaData[0] : 0;
                break;

            default: // self_drive
                $baseRate = 55555.555;


        }
        return $baseRate;
    }

    public static function get_extra_data($postID, $type = 'self_drive')
    {
        global $wpdb;
        $output = [];
        switch ($type) {

            case "self_drive":
                /** EXTRA KM (CURRENCY) */
                $q = "SELECT * FROM wp_rate WHERE `type`='EXTRA' AND rate_chart_id=" . $postID;
                $extraKM = $wpdb->get_row($q);
                $output['extra_amount_km'] = isset($extraKM) ? number_format($extraKM->amount, 2) : 0;

                /** MILEAGE (KM) */
                $q = "SELECT * FROM wp_rate WHERE `type`='MILEAGE' AND rate_chart_id=" . $postID;
                $Mileage = $wpdb->get_row($q);
                $output['mileage'] = isset($Mileage) ? $Mileage->amount . ' KM' : 0;

                /** DEPOSIT (CURRENCY) */
                $q = "SELECT deposit   FROM wp_rate_chart WHERE wp_post_ID =" . $postID;
                $deposit = $wpdb->get_row($q);
                $output['refundable_deposit'] = isset($deposit) ? number_format($deposit->deposit, 2) . ' ' : 0;
                break;
            case "with_driver":
                $rate_chart_id = self::get_rate_chart_id($postID);

                /** EXTRA KM (CURRENCY) */
                $q = "SELECT * FROM wp_rate WHERE `type`='WD_EX' AND rate_range_id = 21 AND rate_chart_id = '" . $rate_chart_id . "'";
                $extraKM = $wpdb->get_row($q);
                $output['extra_amount_km'] = isset($extraKM) ? number_format($extraKM->amount, 2) : 0;

                /** MILEAGE (KM) */
                $q = "SELECT *  FROM wp_rate_range  WHERE id = '" . $_REQUEST['packages'] . "' and `type`='WD'";
                $Mileage = $wpdb->get_row($q);
                $output['mileage'] = isset($Mileage) ? $Mileage->description . ' KM' : 0;

                break;
        }
        return $output;

    }

    public static function test()
    {
        global $wpdb;
        echo '<pre>';
        print_r($_GET);

        $postID = 1847;

        /**
         *  * reserv-car-id: 1847
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
         */


        echo '</pre>';

    }

    /** PRIVATE STATIC FUNCTIONS */
    private static function get_extra_rate($postID, $location)
    {
        global $wpdb;
        $q = "SELECT wp_term_taxonomy.term_taxonomy_id as id FROM wp_terms JOIN wp_term_taxonomy ON wp_terms.term_id = wp_term_taxonomy.term_id WHERE `name` = '" . $location . "'";
        $location_id = $wpdb->get_row($q);
        $extra_amount = 0;
        if ($location_id) {
            $q = "SELECT amount FROM wp_rate WHERE rate_chart_id = '" . $postID . "' AND rate_range_id = '" . $location_id->id . "'  AND `type`='LOCATION' ";
            $rate = $wpdb->get_row($q);
            $extra_amount = isset($rate) ? $rate->amount : 0;
        }
        return $extra_amount;
    }

    private static function get_multiplier($km)
    {
        global $wpdb;
        $q = "SELECT description as multiplier FROM wp_rate_range  WHERE `type` = 'TAXI_KM'  AND min_days <= '" . $km . "' AND max_days >='" . $km . "' ;";
        $result = $wpdb->get_row($q);
        return $result ? $result->multiplier : 1;
    }

    private static function number_of_days()
    {
        if ((isset($_REQUEST['form_source']) && $_REQUEST['form_source'] == 'airport_transfers') || (isset($_REQUEST['rezerve-rent-option']) && $_REQUEST['rezerve-rent-option'] == 'airport_transfers')) {
            return 1;
        } else {
            if (isset($_REQUEST['rezerve-pickup-date']) && isset($_REQUEST['rezerve-pickup-time']) && isset($_REQUEST['rezerve-drop-date']) && isset($_REQUEST['rezerve-drop-time'])) {
                $pik_up = strtotime($_REQUEST['rezerve-pickup-date'] . ' ' . $_REQUEST['rezerve-pickup-time']);
                $return = strtotime($_REQUEST['rezerve-drop-date'] . ' ' . $_REQUEST['rezerve-drop-time']);
                $datediff = $return - $pik_up;
                return round($datediff / (60 * 60 * 24)) + 1;
            }
        }

    }

    private static function put_quotation($input, $is_array = false, $q = "`")
    {
        if (!$is_array) {
            if (count($string_to_array = explode('.', $input)) > 1) {
                // handling dot in the string
                $final_string = '';
                foreach ($string_to_array as $array_element) {
                    $final_string .= $q . $array_element . $q . ".";
                }
                return rtrim($final_string, '.');
            } else {
                return $q . $input . $q;
            }
        } else {
            if (!empty($input)) {
                foreach ($input as $key => $value) {
                    if (!empty($value)) {
                        if (count($string_to_array = explode('.', $value)) > 1) {
                            // handling dot in the string
                            $final_string = '';
                            foreach ($string_to_array as $array_element) {
                                $final_string .= $q . $array_element . $q . ".";
                            }
                            $input[$key] = rtrim($final_string, '.');
                        } else {
                            $input[$key] = $q . $value . $q;
                        }
                    }
                }
            }
            return $input;
        }
    }

    private static function generate_or_query($query_string, $column_name)
    {
        $column_name = self::put_quotation($column_name, false, "`");
        return '(' . ltrim(implode(" OR " . $column_name . "=", self::put_quotation(explode(",", "," . $query_string), true, "'")), " OR") . ')';
    }

    private static function generate_and_query($where_set)
    {
        if (!empty($where_set)) {
            return implode(" AND ", $where_set);
        }
    }

    private static function get_rate_chart_id($postID)
    {
        global $wpdb;
        $q = "SELECT * from wp_rate_chart where wp_post_ID = '" . $postID . "'";
        $rate_chart = $wpdb->get_row($q);
        if ($rate_chart) {
            return $rate_chart->id;
        } else {
            return 0;
        }
    }
    /** end of PRIVATE STATIC FUNCTIONS */

}