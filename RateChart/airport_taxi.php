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
        $this->table_rate_chart = $this->wpdb->prefix . 'rate_chart';


        $this->meta_key_min_km = $this->meta_prefix . $this->meta_key_min_km . 'min_km';
        $this->meta_key_max_km = $this->meta_prefix . $this->meta_key_max_km . 'max_km';
        $this->meta_key_rate_taxi = $this->meta_prefix . $this->meta_key_rate_taxi . 'per_km_taxi';
        $this->meta_key_rate_airport = $this->meta_prefix . $this->meta_key_rate_airport . 'per_km_airport';
        $this->meta_key_rate_extra = $this->meta_prefix . $this->meta_key_rate_extra . 'extra';

    }

    public function getVehicleList()
    {
        $sql = "SELECT
                    p.ID AS post_id, p.post_name, p.post_title, p.post_status, p.post_name, p.post_type, rc.id, rc.wp_vehicle_category_id, rc.deposit, rc.extra_amount_per_km, 
                    rc.extra_amount_per_hour, rc.wedding_per_hour, rc.wedding_extra_hour_km, rc.drop_hire_per_km, vc.`name` AS category_description 
                FROM
                    $this->table_post p
                    LEFT JOIN $this->table_rate_chart rc ON rc.wp_post_ID = p.ID
                    LEFT JOIN wp_vehicles_cat vc ON vc.id = rc.wp_vehicle_category_id
                    INNER JOIN wp_postmeta ON p.ID = wp_postmeta.post_id 
                WHERE
                    p.post_type = 'vehicle' 
                    AND p.post_status = 'publish' 
                    AND wp_postmeta.meta_key = 'service_type'  
                    AND (wp_postmeta.meta_value LIKE '%\"3\"%' or wp_postmeta.meta_value LIKE '%\"5\"%' )
                GROUP BY
                    p.ID DESC";

        return $vehicle_list = $this->wpdb->get_results($sql);
    }

    public function getData($postID)
    {
        $q = "SELECT * FROM wp_postmeta pm WHERE pm.post_id = '" . $postID . "' and pm.meta_key LIKE 'rc_airport_taxi_%'";
        return $vehicle_list = $this->wpdb->get_results($q);
    }

    public function get_km_range()
    {
        $q = "SELECT * FROM wp_rate_range WHERE `type`='TAXI_KM' ORDER BY id ASC";
        return $this->wpdb->get_results($q);
    }

    public function get_km_range_last()
    {
        $q = "SELECT * FROM wp_rate_range WHERE `type`='TAXI_KM' ORDER BY id DESC";
        return $this->wpdb->get_row($q);
    }

    public function getPostMeta($postID)
    {
        get_post_meta($postID);
    }

    public function insert()
    {
        if (isset($_POST['insert']) & !empty($_POST)) {
            $description = sanitize_text_field($_POST['description']);
            $min_days = sanitize_text_field($_POST['min_days']);
            $max_days = sanitize_text_field($_POST['max_days']);
            $q = "INSERT INTO `wp_rate_range` (`description`, `min_days`, `max_days`, `is_active`, `type`)
            VALUES ('" . $description . "', " . $min_days . ", " . $max_days . ", 1, 'TAXI_KM');";
            return $this->wpdb->get_results($q);
        }

    }

    public function delete()
    {
        if (isset($_POST['delete']) & !empty($_POST)) {
            $id = $_POST['id'];
            $q = "DELETE FROM wp_rate_range WHERE id=" . $id;
            return $this->wpdb->query($q);
        }
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

    public function getRates($range_id = 0, $chart_id = 0, $type = 'EXTRA_KM')
    {
        $q = "SELECT * FROM wp_rate WHERE rate_range_id=$range_id AND rate_chart_id=$chart_id AND `type` = '$type'";
        return $this->wpdb->get_row($q);
    }
}

$rateObject = new airport_taxi();
?>


<script>

    function update_postmeta(post_id, keyValue, tmpThis) {
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


    function update_rateRange(id, tmpThis) {
        var postURL = "<?php echo plugins_url('redberylit-plugin/ajax/save_rate_range.php'); ?>";
        var data = {
            id: id,
            value: tmpThis.value
        };
        $.post(postURL, data, function (response) {
            $.parseJSON(response);
        });
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
    <h2 class="wp-heading-inline">Airport & Taxi</h2>
    <?php
    function ilc_admin_tabs_airport_taxi($current = 'airport_taxi')
    {
        $tabs = [
            'airport_taxi' => 'Rates <span style="color: darkred">(LKR)</span>',
            'rate_km_range' => 'KMs Range & Extra KM Setup'
        ];
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=redberylit_rate_airport_taxi&tab=$tab'>$name</a>";
        }
        echo '</h2>';
    }

    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'airport_taxi';
    ilc_admin_tabs_airport_taxi($tab);

    if (isset($_GET['tab']) && $_GET['tab'] == 'rate_km_range') {

        /** INSERT - Date Range */
        $validate = false;
        if (isset($_POST['insert']) & !empty($_POST)) {
            $validate = false;

            $min_days = sanitize_text_field($_POST['min_days']);
            $max_days = sanitize_text_field($_POST['max_days']);
            $multiplier = sanitize_text_field($_POST['description']);
            if ($min_days < 0) {
                $validate = true;
                $message[] = 'Minimum Day is required';
            }
            if ($max_days <= 0) {
                $validate = true;
                $message[] = 'Maximum day is required';
            } else if ($min_days > $max_days) {
                $validate = true;
                $message[] = 'Maximum day must be grater than Maximum day';
            }
        }

        /** DELETE  - Date Range  */
        if (isset($_POST['delete']) & !empty($_POST)) {
            $rateObject->delete();
        }

        /**  --------------------------- KMs Range & Extra KM Setup ---------------------------   */
        ?>

        <h2>Extra KM </h2>
        <table class="form-table">
            <tbody>
            <tr valign="top" class="">
                <th scope="row">
                    <label for=""> Extra Amount Per KM </label>
                </th>
                <td>
                    <?php
                    $rates = $rateObject->getRates();
                    $amount = isset($rates->amount) ? $rates->amount : 0;
                    ?>
                    <input onchange="update_rb_chart_rate_detail(0,0,'EXTRA_KM',this)" class="regular-text" type="text"
                           value="<?php echo $amount ?>">
                </td>
            </tr>
            </tbody>
        </table>

        <hr>
        <h2>KMs Range Setup </h2>

        <div class="wp-clearfix">

            <div id="col-left">
                <div class="col-wrap">
                    <form action="" method="post">
                        <input type="hidden" name="insert" value="true">

                        <div class="form-wrap">
                            <h2>Add New Range </h2>
                        </div>
                        <?php
                        $showDefault = true;
                        if (isset($_POST['insert']) & !empty($_POST)) {
                            if ($validate) {
                                if (!empty($message)) {
                                    echo '<p><strong>Errors</strong></p>';
                                    foreach ($message as $item) {
                                        echo "<p class='text-danger'>$item</p>";
                                    }
                                    $showDefault = false;
                                }
                            } else {
                                $showDefault = true;
                                $rateObject->insert();
                            }
                        }

                        $lastRecord = $rateObject->get_km_range_last();
                        $value = !empty($lastRecord) ? $lastRecord->max_days + 1 : 0;
                        $readonly = !empty($lastRecord) ? 'readonly' : '';
                        ?>
                        <div class="form-field form-required term-name-wrap">
                            <label for="min_days">Min KMs</label>
                            <input required step="any" name="min_days" <?php echo $readonly ?> type="number"
                                   value="<?php echo $value ?>">
                        </div>

                        <div class="form-field form-required term-name-wrap">
                            <label for="max_days">Max KMs</label>
                            <input name="max_days" step="any" required type="number"
                                   value="<?php echo isset($_POST['max_days']) && (!$showDefault) ? $_POST['max_days'] : '' ?>">

                        </div>

                        <div class="form-field form-required term-name-wrap">
                            <label for="description">Multiplier</label>
                            <input name="description" step="any" required type="number"
                                   value="<?php echo isset($_POST['description']) && (!$showDefault) ? $_POST['description'] : 1 ?>">
                        </div>

                        <br>
                        <p class="submit">
                            <input type="submit" name="submit" id="submit" class="button button-primary"
                                   value="Add New KM Range">
                            <span class="spinner"></span>
                        </p>

                    </form>
                </div>
            </div>
            <div id="col-right">
                <div class="page-table">
                    <table class="widefat display">
                        <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th colspan="2">KMs Range</th>
                            <th rowspan="2">Multiplier</th>
                            <th rowspan="2" style="width: 10%">Action</th>
                        </tr>
                        <tr>
                            <th>Min KMs</th>
                            <th>Max KMs</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $ranges = $rateObject->get_km_range();
                        if (!empty($ranges)) {
                            $i = 1;
                            foreach ($ranges as $range) {
                                ?>
                                <tr>
                                    <td>
                                        <?php echo $i; ?>
                                    </td>
                                    <td><?php echo $range->min_days ?></td>
                                    <td><?php echo $range->max_days ?></td>
                                    <td>
                                        <input onchange="update_rateRange('<?php echo $range->id ?>',this)"
                                               class="rb_input" type="number"
                                               value="<?php echo $range->description ?>"/>
                                    </td>
                                    <td>
                                        <?php
                                        if (count($ranges) == $i) {
                                            ?>
                                            <form method="post">
                                                <input type="hidden" value="<?php echo $range->id ?>" name="id"/>
                                                <input type="hidden" value="true" name="delete"/>
                                                <button class="button-link" style="color: #da4d4d;" type="submit"
                                                        onclick="return confirm('Are you sure you want to delete?')">
                                                    <span class="dashicons dashicons-trash"></span></button>
                                            </form>
                                            <?php
                                        } ?>
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
            </div>

        </div>
        <?php
        /** --------------------------- END KMs Range & Extra KM Setup --------------------------- */
    } else {

        /**  --------------------------- Taxi or Airport Rates  ---------------------------   */
        ?>
        <h2 class="wp-heading-inline">Airport & Taxi Transfer Rates </h2>
        <div class="page-table" style="overflow: auto;" id="table-div">
            <table class="widefat display">
                <thead>
                <tr>
                    <th rowspan="2" class="headcol" style="height: 55px">#</th>
                    <th rowspan="2" class="headcol space2" style="height: 55px"> Model</th>
                    <th colspan="2" style="text-align: center;"><strong>Rate (Per KM) - LKR </strong></th>
                </tr>
                <tr>
                    <td style="width: 0px"></td>
                    <th><strong>Taxi</strong></th>
                    <th><strong>Airport</strong></th>
                </tr>

                </thead>
                <tbody>
                <?php
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
                            <td>
                                <input type="number"
                                       onchange="update_postmeta('<?php echo $vehicle->post_id ?>','<?php echo $rateObject->meta_key_rate_taxi; ?>',this)"
                                       value="<?php echo $rateObject->getAmount($data, $rateObject->meta_key_rate_taxi); ?>"
                                       class="rb_input">
                            </td>
                            <td>
                                <input type="number"
                                       onchange="update_postmeta('<?php echo $vehicle->post_id ?>','<?php echo $rateObject->meta_key_rate_airport; ?>',this)"
                                       value="<?php echo $rateObject->getAmount($data, $rateObject->meta_key_rate_airport); ?>"
                                       class="rb_input">
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
        /** -------------------------------- END of Taxi or Airport Rates -------------------------------------*/
    }
    ?>


</div>
