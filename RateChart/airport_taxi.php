<?php

class airport_taxi
{

    public $meta_prefix = 'rc_airport_taxi_';
    public $meta_key_min_km;
    public $meta_key_max_km;
    public $meta_key_rate_taxi;
    public $meta_key_rate_airport;
    public $meta_key_rate_extra = "rc_airport_taxi_extra";

    public function __construct()
    {
        global $table_prefix, $wpdb;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;

        /** Tables */
        $this->table_post = $this->wpdb->prefix . 'posts';
        $this->table_rate_chart = $this->wpdb->prefix . 'postmeta';


        $this->meta_key_min_km = $this->meta_prefix . $this->meta_key_min_km . 'min_km';
        $this->meta_key_max_km = $this->meta_prefix . $this->meta_key_max_km . 'max_km';
        $this->meta_key_rate_taxi = $this->meta_prefix . $this->meta_key_rate_taxi . 'per_km_taxi';
        $this->meta_key_rate_airport = $this->meta_prefix . $this->meta_key_rate_airport . 'per_km_airport';
        $this->meta_key_rate_extra = $this->meta_prefix . $this->meta_key_rate_extra . 'extra';

    }

    public function getVehicleList()
    {
        $q = "SELECT
                    p.ID AS post_id, p.post_name, p.post_title, p.post_status, p.post_name, p.post_type, rc.id, rc.wp_vehicle_category_id, 
                    rc.deposit, rc.extra_amount_per_km, rc.extra_amount_per_hour, rc.wedding_per_hour, rc.wedding_extra_hour_km, rc.drop_hire_per_km, 
                    vc.`name` AS category_description, rc.driver_charges 
                FROM
                    wp_posts p
                    LEFT JOIN wp_rate_chart rc ON rc.wp_post_ID = p.ID
                    LEFT JOIN wp_vehicles_cat vc ON vc.id = rc.wp_vehicle_category_id 
                WHERE
                    p.post_type = 'vehicle' 
                    AND p.post_status = 'publish' 
                ORDER BY
                    p.ID DESC";
        return $vehicle_list = $this->wpdb->get_results($q);
    }

    public function getData($postID)
    {
        $q = "SELECT * FROM wp_postmeta pm WHERE pm.post_id = '" . $postID . "' and pm.meta_key LIKE 'rc_airport_taxi_%'";
        return $vehicle_list = $this->wpdb->get_results($q);
    }

    public function getPostMeta($postID)
    {
        get_post_meta($postID);
    }

    public function get_post_data($postID)
    {
        $q = "SELECT * FROM $this->table_rate WHERE rate_range_id=$postID AND  (`type` = 'SD' OR `type` = 'WD' )";
        return $this->wpdb->get_results($q);

    }

    public function getAmount($data, $metaKey)
    {
        $amount = 0;
        if (!empty($data)) {
            foreach ($data as $val) {
                if ($val->meta_key == $metaKey) {
                    $amount = $val->meta_value;
                    break;
                }
            }
        }
        return $amount;
    }

}

new airport_taxi();
?>
<script>

    function update_rb_chart_rate(post_id, keyValue, tmpThis) {
        var data = {
            post_id: post_id,
            key: keyValue,
            value: tmpThis.value,
            source: 'rate_chart'
        };
        var postURL = '<?php echo plugins_url('redberylit-plugin/ajax/save_postmeta.php'); ?>';
        $.post(postURL, data, function (response) {
            var obj = $.parseJSON(response);
        });
        return false;
    }
</script>
<div class="wrap">

    <?php

    function ilc_admin_tabs_airport_taxi($current = 'airport_taxi')
    {
        $tabs = [
            'airport_taxi' => 'Rates <span style="color: darkred">(LKR)</span>',
            'rate_settings' => 'Rate KM Ranges & Extra KM Setup  <span style="color: darkred">(LKR)</span>'
        ];
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=redberylit_plugin&tab=$tab'>$name</a>";
        }
        echo '</h2>';
    }

    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'airport_taxi';
    ilc_admin_tabs_airport_taxi($tab);
    ?>

    <h2 class="wp-heading-inline">Airport & Taxi Transfer Rates </h2>
    <div class="page-table" style="overflow: auto;" id="table-div">
        <table class="widefat display" id="example">
            <thead>
            <tr>
                <th rowspan="2" class="headcol" style="height: 55px">#</th>
                <th rowspan="2" class="headcol space2" style="height: 55px"> Model</th>
                <!--<th rowspan="2"><strong>Min KMs</strong></th>
                <th rowspan="2"><strong>Max KMs</strong></th>-->
                <th colspan="2" style="text-align: center;"><strong>Rate (Per KM) - LKR </strong></th>
                <!--<th rowspan="2"><strong>Extra KM</strong></th>-->

            </tr>
            <tr>
                <td style="width: 0px" ></td>
                <th ><strong>Taxi</strong></th>
                <th ><strong>Airport</strong></th>
            </tr>

            </thead>
            <tbody>
            <?php
            $rateObject = new airport_taxi();
            $vehicle_list = $rateObject->getVehicleList();
            if (!empty($vehicle_list)) {
                $i = 0;
                foreach ($vehicle_list as $vehicle) {
                    $data = $rateObject->getData($vehicle->post_id);
                    ?>
                    <tr title="<?php echo $vehicle->post_title ?>">
                        <td class="headcol"><?php echo $i + 1 ?></td>
                        <td class="headcol space2">
                            <?php
                            $link = get_site_url() . "/" . $vehicle->post_type . "/" . $vehicle->post_name . "/";
                            echo "<strong><a target='_blank' href=\"$link\" class=\"row-title\">" . $vehicle->post_title . "</a></strong>";
                            ?>
                        </td>

                        <!--<td>

                            <input onchange="update_rb_chart_rate('<?php /*echo $vehicle->post_id */?>','<?php /*echo $rateObject->meta_key_min_km; */?>',this)"
                                   type="number"
                                   value="<?php /*echo $rateObject->getAmount($data, $rateObject->meta_key_min_km); */?>"
                                   class="rb_input">
                        </td>
                        <td>
                            <input type="number"
                                   onchange="update_rb_chart_rate('<?php /*echo $vehicle->post_id */?>','<?php /*echo $rateObject->meta_key_max_km; */?>',this)"
                                   value="<?php /*echo $rateObject->getAmount($data, $rateObject->meta_key_max_km); */?>"
                                   class="rb_input">
                        </td>-->
                        <td>
                            <input type="number"
                                   onchange="update_rb_chart_rate('<?php echo $vehicle->post_id ?>','<?php echo $rateObject->meta_key_rate_taxi; ?>',this)"
                                   value="<?php echo $rateObject->getAmount($data, $rateObject->meta_key_rate_taxi); ?>"
                                   class="rb_input">
                        </td>
                        <td>
                            <input type="number"
                                   onchange="update_rb_chart_rate('<?php echo $vehicle->post_id ?>','<?php echo $rateObject->meta_key_rate_airport; ?>',this)"
                                   value="<?php echo $rateObject->getAmount($data, $rateObject->meta_key_rate_airport); ?>"
                                   class="rb_input">
                        </td>
                        <!--<td>
                            <input type="number"
                                   onchange="update_rb_chart_rate('<?php /*echo $vehicle->post_id */?>','<?php /*echo $rateObject->meta_key_rate_extra; */?>',this)"
                                   value="<?php /*echo $rateObject->getAmount($data, $rateObject->meta_key_rate_extra); */?>"
                                   class="rb_input">
                        </td>-->


                    </tr>
                    <?php
                    $i++;
                }
            }

            ?>
            </tbody>
        </table>
    </div>


</div>
