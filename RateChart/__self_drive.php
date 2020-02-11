<?php

class self_drive
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
        return $this->wpdb->get_results("SELECT * FROM $this->table_rate_range WHERE is_active=1 AND `type`='RATE'", ARRAY_A);
    }

    public function getRates($range_id, $chart_id, $type = 'LOCATION')
    {
        $q = "SELECT * FROM $this->table_rate WHERE rate_range_id=$range_id AND rate_chart_id=$chart_id AND `type` = '$type'";
        return $this->wpdb->get_row($q);
    }

    public function getRateChartData($source = 'LOCATION')
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

                                $rate_data = $this->getRates($vehicles[$i]->date_range[$key]['id'], $vehicles[$i]->id, $source);
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
    rc.id, rc.wp_vehicle_category_id, rc.deposit, rc.extra_amount_per_km, rc.extra_amount_per_hour, rc.wedding_per_hour, rc.wedding_extra_hour_km, rc.drop_hire_per_km, vc.`name` AS category_description 
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

new self_drive();
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
            source: 'location',
            amount: tmpThis.value,
            rate_range_id: rate_range_id
        };
        $.post(postURL, data, function (response) {
            $.parseJSON(response);
        });
    }
</script>
<div class="wrap">

    <h2 class="wp-heading-inline">Self Drive Setup</h2>

    <?php
    function ilc_admin_tabs($current = 'rate_chart')
    {
        $tabs = [
            'rate_chart' => 'Rates <span style="color: darkred">(LKR)</span>',
            'rate_location' => 'Pick-up & Return Location Setup <span style="color: darkred">(LKR)</span>',
            'extra_km_charges' => 'Extra KM Charges & Mileage  <span style="color: darkred">(LKR)</span>'
        ];
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=redberylit_plugin&tab=$tab'>$name</a>";
        }
        echo '</h2>';
    }

    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'rate_chart';
    ilc_admin_tabs($tab);

    if (isset($_GET['tab']) && $_GET['tab'] == 'rate_location') {
        echo '<h2 class="wp-heading-inline">Location based Extra Rates </h2>';
        $rateObject = new self_drive;
        $date_range = $rateObject->getDateRange();
        $vehicle_list = $rateObject->getRateChartData();
        $locations = $rateObject->get_pickup_locations();
        ?>
        <div class="page-table" style="overflow: auto;" id="table-div">
            <table class="widefat display" id="example">
                <thead>
                <tr>
                    <th class="headcol">#</th>
                    <th class="headcol space2"> Vehicle Model</th>
                    <?php
                    if (!empty($locations)) {
                        foreach ($locations as $location) {
                            echo "<th class='lw' ><strong>" . $location->description . "</strong></th>";
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

                            <?php
                            /** ----------------------  Location List  ---------------------- */
                            if (!empty($locations)) {
                                $i = 0;
                                foreach ($locations as $location) {
                                    $rates = $rateObject->getRates($location->id, $vehicle->post_id);
                                    $amount = isset($rates->amount) ? $rates->amount : 0;
                                    $cls = $i % 2 == 0 ? 'bg-lg' : '';
                                    ?>
                                    <td class="<?php echo $cls ?>">
                                        <input class="rb_input" type="number"
                                               id="<?php echo $vehicle->post_id . '_' . $location->id ?>"
                                               onchange="update_rb_chart_rate_detail('<?php echo $vehicle->post_id ?>', '<?php echo $location->id ?>' ,'LOCATION',this)"
                                               value="<?php echo $amount ?>"/>
                                    </td>
                                    <?php
                                    $i++;
                                }
                            }
                            /** ----------------------  Location List   End  ---------------------- */
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


        <?php
    } else if (isset($_GET['tab']) && $_GET['tab'] == 'extra_km_charges') {
        $rateObject = new self_drive;
        $rates = $rateObject->getRates(0, 0, 'MILEAGE');
        $amount = isset($rates->amount) ? $rates->amount : 0;
        ?>
        <h2>Mileage </h2>

        <table class="form-table">
            <tbody>
            <tr valign="top" class="">
                <th scope="row">
                    <label for=""> Mileage per Day </label>
                </th>
                <td><input onchange="update_rb_chart_rate_detail(0, 0 ,'MILEAGE',this)"
                           id="setting-autoroyal_posts_per_page" class="regular-text" type="text"
                           name="autoroyal_posts_per_page" value="<?php echo $amount; ?>"></td>
            </tr>
            </tbody>
        </table>


        <!-------------------------- KM Charges ----------------------------- -->
        <h2>Extra KM Charges </h2>

        <?php


        $date_range = $rateObject->getDateRange();
        $vehicle_list = $rateObject->getRateChartData();
        $locations = $rateObject->get_pickup_locations();
        ?>
        <div class="page-table" style="overflow: auto; width: 500px" id="table-div">
            <table class="widefat display">
                <thead>
                <tr>
                    <th class="headcol">#</th>
                    <th class="headcol space2"> Vehicle Model</th>
                    <th class='lw'><strong>Extra Amount per KM </strong></th>

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
                                <?php
                                $rates = $rateObject->getRates(0, $vehicle->post_id, 'EXTRA');
                                $amount = isset($rates->amount) ? $rates->amount : 0;
                                ?>
                                <input class="rb_input"
                                       onchange="update_rb_chart_rate_detail('<?php echo $vehicle->post_id ?>', 0 ,'EXTRA',this)"
                                       type="number" value="<?php echo $amount ?>"/>
                            </td>


                        </tr>
                        <?php
                        $i++;
                    }
                }

                ?>
                </tbody>
            </table>
        </div>

        <?php

    } else {
        echo '<h2 class="wp-heading-inline">Self Drive Rates </h2>';
        $rateObject = new self_drive;
        $date_range = $rateObject->getDateRange();
        $vehicle_list = $rateObject->getRateChartData('SD');
        ?>
        <div class="page-table" style="overflow: auto;" id="table-div">
            <table class="widefat display" id="example" style="width:150%">
                <thead>
                <tr>
                    <th class="headcol">#</th>
                    <th class="headcol space2"> Model</th>
                    <th>Deposit</th>
                    <?php
                    if (!empty($date_range)) {
                        $x = 1;
                        foreach ($date_range as $range) {
                            $cls = $x % 2 == 0 ? '' : 'bg-lg';
                            echo "<th class='" . $cls . "'><strong>" . $range['description'] . "</strong></th>";
                            $x++;
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
                                <input onchange="update_rb_chart_rate('<?php echo $vehicle->id ?>','<?php echo $vehicle->post_id ?>',this,'deposit')"
                                       type="number" class="rb_input"
                                       value="<?php echo isset($vehicle->deposit) ? $vehicle->deposit : '0' ?>">
                            </td>

                            <?php
                            /** ----------------------  Populate date range ---------------------- */
                            if (!empty($vehicle->date_range)) {
                                $i = 1;
                                foreach ($vehicle->date_range as $range) {
                                    $cls = $i % 2 == 0 ? '' : 'bg-lg';
                                    /** Self Drive cell */
                                    ?>
                                    <td class="<?php echo $cls ?>">

                                        <?php

                                        $rates = $rateObject->getRates($range['id'], $vehicle->post_id, 'SD');
                                        $amount_rate = isset($rates->amount) ? $rates->amount : 0;

                                        /*$amount = 0;
                                        if (!empty($range['data'])) {
                                            var_dump($range['data']);
                                            foreach ($range['data'] as $data_values) {
                                                if ($data_values->type == 'SD') {
                                                    $amount = $data_values->amount;
                                                }
                                            }
                                        }*/
                                        //echo $vehicle->post_id . ' - ' . $range['id']
                                        ?>
                                        <input class="rb_input" type="number"
                                               onchange="update_rb_chart_rate_detail('<?php echo $vehicle->post_id ?>', '<?php echo $range['id'] ?>' ,'SD',this)"
                                               value="<?php echo $amount_rate ?>"/>
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
        <?php
    }
    ?>
</div>
