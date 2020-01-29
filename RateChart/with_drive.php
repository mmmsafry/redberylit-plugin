<?php

class with_drive
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
        $this->table_vehicles_category = $this->wpdb->prefix . 'vehicles_cat';


        $q = "UPDATE wp_rate_range SET is_active=1 ";
        $this->wpdb->query($q);

    }

    public function geVehicleCategory()
    {
        return $vehicle_list = $this->wpdb->get_results("SELECT * FROM $this->table_vehicles_category");
    }

    /** Public Functions */

    public function getVehicleByPostID()
    {
        return $vehicle_list = $this->wpdb->get_row("SELECT p.ID AS post_id, p.post_name, p.post_title, p.post_status, p.post_name, p.post_type,
    rc.id, rc.wp_vehicle_category_id, rc.deposit, rc.extra_amount_per_km, rc.extra_amount_per_hour, rc.wedding_per_hour, rc.wedding_extra_hour_km, rc.drop_hire_per_km
     FROM $this->table_post p LEFT JOIN $this->table_rate_chart rc ON rc.wp_post_ID = p.ID WHERE p.post_type = 'vehicle' AND p.post_status='publish' AND p.ID=" . $_GET['id']);
    }

    public function createData($post_id)
    {
        $post = $this->wpdb->get_row("SELECT * FROM $this->table_rate_chart WHERE wp_post_ID=$post_id");
        if (!$post) {
            $query = "INSERT INTO $this->table_rate_chart (wp_post_ID) VALUES ($post_id)";
            $this->wpdb->query($query);
            $last_id = $this->wpdb->insert_id;

            $data_range = $this->getDateRange();
            if (!empty($data_range)) {
                $q = "INSERT INTO $this->table_rate (`rate_chart_id`,`rate_range_id`,`amount`,`type`) VALUES ";
                $i = 0;
                foreach ($data_range as $dates) {
                    $id = $dates['id'];
                    $q .= "($last_id,$id,0,'WD'),";
                    $q .= "($last_id,$id,0,'SD'),";
                }
                $q = rtrim($q, ',');
                $this->wpdb->query($q);
            }
        }
    }

    public function getDateRange()
    {
        return $this->wpdb->get_results("SELECT * FROM $this->table_rate_range WHERE is_active=1 AND `type`='WD'", ARRAY_A);
    }

    public function getRates($range_id, $chart_id)
    {
        $q = "SELECT * FROM $this->table_rate WHERE rate_range_id=$range_id AND rate_chart_id=$chart_id AND (`type` = 'WD_BR' OR `type`='WD_EX' )  ";
        return $this->wpdb->get_results($q);
    }

    public function getRateChartData()
    {
        $vehicles = $this->getVehicleList();
        $master_date_range = $this->getDateRange();

        if (!empty($vehicles)) {
            $i = 0;
            /** First Loop */
            foreach ($vehicles as $key_v => $vehicle) {
                if (!empty($master_date_range)) {
                    $vehicles[$i]->date_range = $master_date_range;
                    if (!empty(trim($vehicles[$i]->id))) {
                        foreach ($vehicles[$i]->date_range as $key => $tmp_date_range) {
                            if (!empty($vehicles[$key_v]->id)) {
                                $vehicles[$i]->date_range[$key]['rate_range_id'] = $vehicles[$i]->date_range[$key]['id'];
                                $vehicles[$i]->date_range[$key]['rate_chart_id'] = $i;

                                $rate_data = $this->getRates($vehicles[$i]->date_range[$key]['id'], $vehicles[$i]->id);
                                $vehicles[$i]->date_range[$key]['data'] = !empty($rate_data) ? $rate_data : null;
                            }
                        }
                    }
                    $i++;
                }
            }
        }
        return $vehicles;
    }

    public function updateRates($post)
    {
        $sql = "UPDATE $this->table_rate_chart 
                    SET 
                    wp_vehicle_category_id =  '" . $post['wp_vehicle_category_id'] . "' , 
                    deposit =  '" . $post['deposit'] . "' , 
                    extra_amount_per_km =  '" . $post['extra_amount_per_km'] . "' , 
                    extra_amount_per_hour =  '" . $post['extra_amount_per_hour'] . "' , 
                    wedding_per_hour =  '" . $post['wedding_per_hour'] . "' , 
                    wedding_extra_hour_km =  '" . $post['wedding_extra_hour_km'] . "' , 
                    drop_hire_per_km =  '" . $post['drop_hire_per_km'] . "' 
                    WHERE id = '" . $post['rate_chart_id'] . "'";
        $this->wpdb->query($sql);

        if (isset($post['date_SD'])) $this->updateSingleRate('SD', $post['date_SD'], $post['rate_chart_id']);
        if (isset($post['date_WD'])) $this->updateSingleRate('WD', $post['date_WD'], $post['rate_chart_id']);

    }

    public function get_pickup_locations()
    {
        return $this->wpdb->get_results("SELECT term_taxonomy_id as id, wp_terms.`name` as description   FROM wp_term_taxonomy INNER JOIN wp_terms ON wp_terms.term_id = wp_term_taxonomy.term_id WHERE taxonomy = 'vehicle_rent_pickup' AND parent =0 ");
    }

    /** Private Functions */
    private function getVehicleList()
    {
        return $vehicle_list = $this->wpdb->get_results("SELECT p.ID AS post_id, p.post_name, p.post_title, p.post_status, p.post_name, p.post_type,
    rc.id, rc.wp_vehicle_category_id, rc.deposit, rc.extra_amount_per_km, rc.extra_amount_per_hour, rc.wedding_per_hour, rc.wedding_extra_hour_km, rc.drop_hire_per_km, vc.`name` AS category_description, rc.driver_charges  
    FROM $this->table_post p LEFT JOIN $this->table_rate_chart rc ON rc.wp_post_ID = p.ID  LEFT JOIN wp_vehicles_cat vc ON vc.id = rc.wp_vehicle_category_id   WHERE p.post_type = 'vehicle' AND p.post_status='publish' ORDER BY  p.ID DESC");
    }

    private function updateSingleRate($preFix, $data, $rate_chart_id)
    {
        if (!empty($data)) {
            foreach ($data as $key => $amount) {
                $q_t = "SELECT * FROM $this->table_rate  WHERE `type`='$preFix' AND rate_range_id=$key AND rate_chart_id=$rate_chart_id ";
                $r = $this->wpdb->get_row($q_t);
                if (!empty($r)) {
                    $q = "UPDATE $this->table_rate SET amount=$amount WHERE `type`='$preFix' AND rate_range_id=$key AND rate_chart_id=$rate_chart_id  ";
                } else {
                    $q = "INSERT INTO $this->table_rate (`rate_chart_id`, `rate_range_id`,  `amount`,  `type` )  VALUES ($rate_chart_id , $key ,$amount,  '$preFix' )";
                }

                $this->wpdb->query($q);
            }
        }

    }


}

new with_drive();
?>
<script>
    function update_rb_chart_rate(id, post_id, tmpThis, column_name) {
        var data = {
            id: id,
            post_id: post_id,
            amount: tmpThis.value,
            source: 'rate_chart',
            column_name: column_name
        };
        var postURL = '<?php echo plugins_url('redberylit-plugin/ajax/save_rates.php'); ?>';
        $.post(postURL, data, function (response) {
            var obj = $.parseJSON(response);
        });
        return false;
    }

    function update_rb_chart_rate_detail(post_id, rate_range_id, type, tmpThis) {
        var postURL = "<?php echo plugins_url('redberylit-plugin/ajax/save_rates_detail.php'); ?>";
        var data = {
            type: type,
            post_id: post_id,
            source: 'with_drive',
            amount: tmpThis.value,
            rate_range_id: rate_range_id
        };
        $.post(postURL, data, function (response) {
            $.parseJSON(response);
        });
    }
</script>
<div class="wrap">

    <h2 class="wp-heading-inline">With Drive Rates </h2>
    <?PHP
    $rateObject = new with_drive();
    $date_range = $rateObject->getDateRange();
    $vehicle_list = $rateObject->getRateChartData();
    ?>
    <div class="page-table" style="overflow: auto;" id="table-div">
        <table class="widefat display" id="example">
            <thead>
            <tr>
                <th class="headcol" style="height: 55px">#</th>
                <th class="headcol space2" style="height: 55px"> Model</th>
                <th rowspan="2"><strong>Driver Charges</strong></th>

                <?php
                $i = 0;
                if (!empty($date_range)) {
                    foreach ($date_range as $range) {
                        $cls = $i % 2 == 0 ? 'bg-lg' : '';
                        echo "<th style='text-align: center' colspan='2' class='" . $cls . "'><strong>" . $range['description'] . " KM Package </strong></th>";
                        $i++;
                    }
                }
                ?>
            </tr>
            <tr>
                <!--<th colspan="3">&nbsp;</th>-->
                <th colspan="2">&nbsp;</th>
                <?php
                $i = 0;
                if (!empty($date_range)) {
                    foreach ($date_range as $range) {
                        $cls = $i % 2 == 0 ? 'bg-lg' : '';
                        ?>
                        <th class="<?php echo $cls ?>">Base Rate</th>
                        <th class="<?php echo $cls ?>">Extra KM</th>
                        <?php
                        $i++;
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($vehicle_list)) {
                $i = 0;
                foreach ($vehicle_list as $vehicle) {
                    ?>
                    <tr title="<?php echo $vehicle->post_title ?>">
                        <td class="headcol"><?php echo $i + 1 ?></td>
                        <td class="headcol space2">
                            <?php
                            $link = get_site_url() . "/" . $vehicle->post_type . "/" . $vehicle->post_name . "/";
                            echo "<strong><a target='_blank' href=\"$link\" class=\"row-title\">" . $vehicle->post_title . "</a></strong>";
                            ?>
                        </td>

                        <td>
                            <input class="rb_input" type="number"
                                   onchange="update_rb_chart_rate('<?php echo $vehicle->id ?>','<?php echo $vehicle->post_id ?>',this,'driver_charges')"
                                   value="<?php echo isset($vehicle->driver_charges) ? $vehicle->driver_charges : '0' ?>"/>
                        </td>

                        <?php
                        /** ----------------------  Populate date range ---------------------- */
                        $i = 0;
                        if (!empty($vehicle->date_range)) {
                            foreach ($vehicle->date_range as $range) {
                                $cls = $i % 2 == 0 ? 'bg-lg' : '';
                                /** With Drive cell */
                                ?>
                                <td class="<?php echo $cls ?>">
                                    <?php
                                    $amount = 0;
                                    if (!empty($range['data'])) {
                                        foreach ($range['data'] as $data_values) {
                                            if ($data_values->type == 'WD_BR') {
                                                $amount = $data_values->amount;
                                            }
                                        }
                                    }
                                    ?>
                                    <input class="rb_input" type="number"
                                           onchange="update_rb_chart_rate_detail('<?php echo $vehicle->post_id ?>', '<?php echo $range['id'] ?>' ,'WD_BR',this)"
                                           value="<?php echo $amount ?>"/>
                                </td>
                                <td class="<?php echo $cls ?>">
                                    <?php
                                    $amount = 0;
                                    if (!empty($range['data'])) {
                                        foreach ($range['data'] as $data_values) {
                                            if ($data_values->type == 'WD_EX') {
                                                $amount = $data_values->amount;
                                            }
                                        }
                                    }
                                    ?>
                                    <input class="rb_input" type="number"
                                           onchange="update_rb_chart_rate_detail('<?php echo $vehicle->post_id ?>', '<?php echo $range['id'] ?>' ,'WD_EX',this)"
                                           value="<?php echo $amount ?>"/>
                                </td>
                                <?php
                                $i++;

                            }
                        }
                        /** ----------------------  Populate date range End  ---------------------- */
                        ?>

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
