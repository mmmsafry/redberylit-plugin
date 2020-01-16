<?php
global $table_prefix, $wpdb;
$url = $_SERVER['REQUEST_URI'];

class rate_chart
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

    private function getVehicleList()
    {
        return $vehicle_list = $this->wpdb->get_results("SELECT p.ID AS post_id, p.post_name, p.post_title, p.post_status, p.post_name, p.post_type,
    rc.id, rc.wp_vehicle_category_id, rc.deposit, rc.extra_amount_per_km, rc.extra_amount_per_hour, rc.wedding_per_hour, rc.wedding_extra_hour_km, rc.drop_hire_per_km, vc.`name` AS category_description 
     FROM $this->table_post p LEFT JOIN $this->table_rate_chart rc ON rc.wp_post_ID = p.ID  LEFT JOIN wp_vehicles_cat vc ON vc.id = rc.wp_vehicle_category_id   WHERE p.post_type = 'vehicle' AND p.post_status='publish' ORDER BY  p.ID DESC");
    }

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
        return $this->wpdb->get_results("SELECT * FROM $this->table_rate_range WHERE is_active=1", ARRAY_A);
    }

    public function getRates($range_id, $chart_id)
    {
        return $this->wpdb->get_results("SELECT * FROM $this->table_rate WHERE rate_range_id=$range_id AND rate_chart_id=$chart_id ");
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

/** ------------------------------------  Save Edited Values for Rate Chart  ------------------------------------ */
if (isset($_POST['update_rate'])) {
    if ($_POST['rate_chart_id'] > 0 && $_POST['post_id']) {
        $rateObject = new rate_chart;
        $rateObject->updateRates($_POST);
    }
}

/**  ------------------------------------  End Save Edited Values for Rate Chart  ------------------------------------ */


/**
 * ------------------------------------  Edit Vehicle ------------------------------------
 */

if (isset($_GET['edit']) && isset($_GET['id'])) {
    $rateObject = new rate_chart;

    $rateObject->createData($_GET['id']);
    $date_range = $rateObject->getDateRange();
    $vehicle_info = $rateObject->getVehicleByPostID();
    $vehicle_category = $rateObject->geVehicleCategory();
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Edit Rates </h1>


        <?php
        // var_dump($vehicle_info);
        ?>
        <form name="edittag" id="edittag" method="post" action="?page=redberylit_plugin" class="validate">

            <input type="hidden" name="update_rate" value="true">
            <input type="hidden" name="rate_chart_id" value="<?php echo $vehicle_info->id ?>">
            <input type="hidden" name="post_id" value="<?php echo $vehicle_info->post_id ?>">

            <table class="form-table">
                <tbody>

                <!-- Name -->
                <tr class="form-field form-required term-name-wrap">
                    <th scope="row"><label for="name">Name</label></th>
                    <td><input name="name" id="name" type="text" value="<?php echo $vehicle_info->post_title ?>"
                               readonly size="40"></td>
                </tr>

                <!-- Deposit  -->
                <tr class=" form-required term-name-wrap">
                    <th scope="row"><label for="deposit">Deposit</label></th>
                    <td><input name="deposit" id="deposit" type="text" value="<?php echo $vehicle_info->deposit ?>">
                    </td>
                </tr>

                <!-- Extra Amount Per KM   -->
                <tr class=" form-required term-name-wrap">
                    <th scope="row"><label for="extra_amount_per_km">Extra Amount Per KM </label></th>
                    <td><input name="extra_amount_per_km" id="extra_amount_per_km" type="text"
                               value="<?php echo $vehicle_info->extra_amount_per_km ?>"></td>
                </tr>

                <!-- extra_amount_per_hour  -->
                <tr class=" form-required term-name-wrap">
                    <th scope="row"><label for="extra_amount_per_hour">Extra Amount Per Hour</label></th>
                    <td><input name="extra_amount_per_hour" id="extra_amount_per_hour" type="text"
                               value="<?php echo $vehicle_info->extra_amount_per_hour ?>"></td>
                </tr>

                <!-- wedding_per_hour  -->
                <tr class=" form-required term-name-wrap">
                    <th scope="row"><label for="wedding_per_hour">Wedding Per Hour</label></th>
                    <td><input name="wedding_per_hour" id="wedding_per_hour" type="text"
                               value="<?php echo $vehicle_info->wedding_per_hour ?>"></td>
                </tr>

                <!-- wedding_extra_hour_km  -->
                <tr class=" form-required term-name-wrap">
                    <th scope="row"><label for="wedding_extra_hour_km">Wedding Extra Hour KM</label></th>
                    <td><input name="wedding_extra_hour_km" id="wedding_extra_hour_km" type="text"
                               value="<?php echo $vehicle_info->wedding_extra_hour_km ?>"></td>
                </tr>

                <!-- drop_hire_per_km  -->
                <tr class=" form-required term-name-wrap">
                    <th scope="row"><label for="drop_hire_per_km">Drop Hire Per KM</label></th>
                    <td><input name="drop_hire_per_km" id="drop_hire_per_km" type="text"
                               value="<?php echo $vehicle_info->drop_hire_per_km ?>"></td>
                </tr>


                <tr class="form-field term-parent-wrap">
                    <th scope="row"><label for="wp_vehicle_category_id">Vehicle Category </label></th>
                    <td>
                        <select name="wp_vehicle_category_id" id="wp_vehicle_category_id" class="postform">
                            <option value="">Select Vehicle Category</option>
                            <?php
                            if (!empty($vehicle_category)) {
                                foreach ($vehicle_category as $category) {

                                    $selected = isset($vehicle_info->wp_vehicle_category_id) && $category->id == $vehicle_info->wp_vehicle_category_id ? 'selected="selected"' : '';
                                    echo '<option ' . $selected . ' value="' . $category->id . '">' . $category->name . '</option>';
                                }
                            }
                            ?>

                        </select>

                    </td>
                </tr>


                <?php
                if (!empty($date_range)) {
                    foreach ($date_range as $dates) {
                        $rates = $rateObject->getRates($dates['id'], $vehicle_info->id);

                        //$wd_amount = !empty($rate) ? $rate['amount'] : 0;
                        ?>
                        <tr class="form-required term-name-wrap">

                            <th scope="row">
                                <label><?php echo $dates['description'] ?></label>
                            </th>
                            <td>
                                <?php

                                $SD_amount = 0;
                                $WD_amount = 0;

                                foreach ($rates as $rate) {
                                    if ($rate->type == 'SD') $SD_amount = $rate->amount;
                                    if ($rate->type == 'WD') $WD_amount = $rate->amount;
                                }
                                ?>

                                <input title="Self Drive" name="date_SD[<?php echo $dates['id'] ?>]"
                                       id="date_<?php echo $dates['id'] ?>"
                                       type="text" value="<?php echo $SD_amount ?>" size="20">

                                <input title="With Driver" name="date_WD[<?php echo $dates['id'] ?>]"
                                       id="date_<?php echo $dates['id'] ?>"
                                       type="text" value="<?php echo $WD_amount ?>" size="20">
                            </td>
                        </tr>
                        <?php
                    }

                }
                ?>

                </tbody>
            </table>

            <div class="edit-tag-actions">

                <input type="submit" class="button button-primary" value="Update">
                <span id="delete-link">
			<a class="cancel-button" href="?page=redberylit_plugin">Cancel</a>
		</span>

            </div>

        </form>

    </div>
    <?php
    exit;
}
/**
 * ------------------------------------  Edit Vehicle End ------------------------------------
 */

?>

<style>
    #table-div {
        width: 80%;
        overflow-x: scroll;
        margin-left: 20em;
        overflow-y: visible;
        padding: 0;
    }

    .headcol {
        position: absolute;
        width: 3em;
        left: 0;
        top: auto;
        /*border-top-width: 1px;*/
        /*only relevant for first row*/
        margin-top: -1px;
        /*compensate for top border*/


        text-overflow: ellipsis; /* IE, Safari (WebKit) */
        overflow: hidden; /* don't show excess chars */
        white-space: nowrap; /* force single line */

        border: 1px solid #5273aa6b;
        background: #ffecec;
        /* border: 1px solid #5273aa6b; */

    }

    .space {
        left: 35px;
    }

    .space2 {
        left: 76px;
        width: 170px;
    }

    tr:hover {
        background-color: #cedff9;
    }

    .rb_input {
        width: 80px;
        min-height: 19px !important;
        line-height: 0px !important;
        padding: 0px 3px !important;
        text-align: right;
    }

</style>
<div class="wrap">
    <h1 class="wp-heading-inline">Rate Chart </h1>
    <?php
    $rateObject = new rate_chart;
    $date_range = $rateObject->getDateRange();
    $vehicle_list = $rateObject->getRateChartData();
    /*echo '<pre>';
    print_r($vehicle_list[0]);
    echo '</pre>';*/
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

        function update_rb_chart_rate_detail(id, post_id, rate_range_id, type, tmpThis) {
            var data = {
                id: id,
                type: type,
                post_id: post_id,
                source: 'rate_chart',
                amount: tmpThis.value,
                rate_range_id: rate_range_id
            };
            var postURL = '<?php echo plugins_url('redberylit-plugin/ajax/save_rates_detail.php'); ?>';
            $.post(postURL, data, function (response) {
                var obj = $.parseJSON(response);
            });
            return false;
        }
    </script>
    <div style="overflow: auto;" id="table-div">
        <table class="widefat display" id="example" style="width:260%">
            <!--border="1" cellspacing="0" cellpadding="0"-->
            <thead>
            <tr>
                <th rowspan="2" class="headcol" style="height: 57px;">#</th>
                <th rowspan="2" class="headcol space" style="height: 57px;">Edit</th>
                <th rowspan="2" class="headcol space2" style="height: 57px;"> Model</th>
                <th rowspan="2">Category</th>
                <th rowspan="2">Deposit</th>
                <th rowspan="2">Extra KM</th>
                <th rowspan="2">Extra Hours</th>
                <th rowspan="2">Wedding Per Hour</th>
                <th rowspan="2">Wedding Extra Hours</th>
                <th rowspan="2">Drop Hire</th>

                <?php
                if (!empty($date_range)) {
                    foreach ($date_range as $range) {
                        echo "<th colspan='2'><strong>" . $range['description'] . "</strong></th>";
                    }
                }
                ?>
            </tr>
            <tr>
                <th colspan="7">&nbsp;</th>
                <?php
                if (!empty($date_range)) {
                    foreach ($date_range as $range) {
                        echo "<th title='With Drive'>WD </th><th title='Self Drive'>SD </th>";
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
                    //var_dump($vehicle);
                    ?>
                    <tr title="<?php echo $vehicle->post_title ?>">
                        <td class="headcol"><?php echo $i + 1 ?></td>
                        <td class="headcol space">
                            <a href="?page=redberylit_plugin&edit=true&id=<?php echo $vehicle->post_id ?>" class="">
                                <span class="dashicons dashicons-edit"></span> </a>
                        </td>

                        <td class="headcol space2">
                            <?php
                            $link = get_site_url() . "/" . $vehicle->post_type . "/" . $vehicle->post_name . "/";
                            echo "<strong><a target='_blank' href=\"$link\" class=\"row-title\">" . $vehicle->post_title . "</a></strong>";
                            ?>
                        </td>

                        <td><strong class="text-capitalize"><?php echo $vehicle->category_description ?></strong>
                        </td>
                        <td>
                            <input onchange="update_rb_chart_rate('<?php echo $vehicle->id ?>','<?php echo $vehicle->post_id ?>',this,'deposit')"
                                   type="number"
                                   class="rb_input"
                                   value="<?php echo isset($vehicle->deposit) ? $vehicle->deposit : '0' ?>"></td>
                        <td>
                            <input class="rb_input"
                                   onchange="update_rb_chart_rate('<?php echo $vehicle->id ?>','<?php echo $vehicle->post_id ?>',this,'extra_amount_per_km')"
                                   type="number"
                                   value="<?php echo isset($vehicle->extra_amount_per_km) ? $vehicle->extra_amount_per_km : '0' ?>">
                        </td>
                        <td>
                            <input class="rb_input" type="number"
                                   onchange="update_rb_chart_rate('<?php echo $vehicle->id ?>','<?php echo $vehicle->post_id ?>',this,'extra_amount_per_hour')"
                                   value="<?php echo isset($vehicle->extra_amount_per_hour) ? $vehicle->extra_amount_per_hour : '0' ?>"/>
                        </td>
                        <td>
                            <input class="rb_input" type="number"
                                   onchange="update_rb_chart_rate('<?php echo $vehicle->id ?>','<?php echo $vehicle->post_id ?>',this,'wedding_per_hour')"
                                   value="<?php echo isset($vehicle->wedding_per_hour) ? $vehicle->wedding_per_hour : '0' ?>"/>
                        </td>
                        <td>
                            <input class="rb_input" type="number"
                                   onchange="update_rb_chart_rate('<?php echo $vehicle->id ?>','<?php echo $vehicle->post_id ?>',this,'wedding_extra_hour_km')"
                                   value="<?php echo isset($vehicle->wedding_extra_hour_km) ? $vehicle->wedding_extra_hour_km : '0' ?>"/>
                        </td>
                        <td>
                            <input class="rb_input" type="number"
                                   onchange="update_rb_chart_rate('<?php echo $vehicle->id ?>','<?php echo $vehicle->post_id ?>',this,'drop_hire_per_km')"
                                   value="<?php echo isset($vehicle->drop_hire_per_km) ? $vehicle->drop_hire_per_km : '0' ?>"/>
                        </td>
                        <?php
                        /** ----------------------  Populate date range ---------------------- */
                        if (!empty($vehicle->date_range)) {

                            foreach ($vehicle->date_range as $range) {

                                /** Width Drive cell */
                                echo "<td>";
                                $amount = 0;
                                if (!empty($range['data'])) {
                                    foreach ($range['data'] as $data_values) {
                                        if ($data_values->type == 'WD') {
                                            $amount = $data_values->amount;
                                        }
                                    }
                                }
                                ?>
                                <input class="rb_input" type="number"
                                       onchange="update_rb_chart_rate_detail('<?php echo $vehicle->id ?>','<?php echo $vehicle->post_id ?>', '<?php echo $range['id'] ?>' ,'WD',this)"
                                       value="<?php echo $amount ?>"/>
                                <?php
                                echo "</td>";

                                /** Self Drive cell */
                                echo "<td>";
                                $amount = 0;
                                if (!empty($range['data'])) {
                                    foreach ($range['data'] as $data_values) {
                                        if ($data_values->type == 'SD') {
                                            $amount = $data_values->amount;
                                        }
                                    }
                                }
                                ?>
                                <input class="rb_input" type="number"
                                       onchange="update_rb_chart_rate_detail('<?php echo $vehicle->id ?>','<?php echo $vehicle->post_id ?>', '<?php echo $range['id'] ?>' ,'SD',this)"
                                       value="<?php echo $amount ?>"/>
                                <?php
                                echo "</td>";


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


    if (isset($_POST['edit'])) {
        $id = $_POST['eid'];
    }
    ?>


</div>

