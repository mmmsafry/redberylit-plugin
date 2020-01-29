<?php

class wedding
{

    public $meta_prefix = 'rc_wedding_';
    public $meta_keys = [];

    public function __construct()
    {
        global $table_prefix, $wpdb;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;

        /** Tables */
        $this->table_post = $this->wpdb->prefix . 'posts';
        $this->table_rate_chart = $this->wpdb->prefix . 'postmeta';

        $fields = [
            ['field_name' => 'hour_rate_4'],
            ['field_name' => 'hour_rate_8'],
            ['field_name' => 'hour_rate_12'],
            ['field_name' => 'extra_hour']
        ];

        foreach ($fields as $key => $field) {
            $this->meta_keys[$key]['field_name'] = $this->meta_prefix . $field['field_name'];
        }
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
        $q = "SELECT * FROM wp_postmeta pm WHERE pm.post_id = '" . $postID . "' and pm.meta_key LIKE '" . $this->meta_prefix . "%'";
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

    public function get_td_input($postID)
    {
        $data = $this->getData($postID);
        $td_inputs = '';
        if (!empty($this->meta_keys)) {
            foreach ($this->meta_keys as $meta_key) {
                $amount = $this->getAmount($data, $meta_key['field_name']);
                $td_inputs .= '<td><input onchange="update_rb_chart_rate(\'' . $postID . '\',\'' . $meta_key['field_name'] . '\',this)" type="number" value="' . $amount . '" class="rb_input"></td>';
            }
            return $td_inputs;
        }
    }

}

$rateObject = new wedding();
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

    <h2 class="wp-heading-inline">Wedding Rates </h2>
    <div class="page-table" style="overflow: auto;" id="table-div">
        <table class="widefat display" id="example">
            <thead>
            <tr>
                <th rowspan="2" class="headcol" style="height: 55px">#</th>
                <th rowspan="2" class="headcol space2" style="height: 55px"> Model</th>
                <th colspan="3" class="bg-lg" style="text-align: center;"><strong>Hourly Rate (LKR) </strong></th>
                <th rowspan="2"><strong>Extra Hour</strong></th>

            </tr>
            <tr>
                <td></td>
                <th class="bg-lg"><strong>4 Hour</strong></th>
                <th class="bg-lg"><strong>8 Hour </strong></th>
                <th class="bg-lg"><strong>12 Hour</strong></th>
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
                        <?php
                        echo $rateObject->get_td_input($vehicle->post_id);
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
