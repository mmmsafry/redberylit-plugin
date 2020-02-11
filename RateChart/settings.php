<?php

class settings
{


    public $table_post;
    public $table_rate_chart;


    public function __construct()
    {
        global $table_prefix, $wpdb;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;

        /** Tables */
        $this->table_post = $this->wpdb->prefix . 'posts';
        $this->table_rate_chart = $this->wpdb->prefix . 'postmeta';
    }

    public function getRates($range_id = 0, $chart_id = 0, $type = 'VAT')
    {
        $q = "SELECT * FROM wp_rate WHERE rate_range_id=$range_id AND rate_chart_id=$chart_id AND `type` = '$type'";
        return $this->wpdb->get_row($q);
    }
}

$settings = new settings();
?>
<script>

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

    <h2 class="wp-heading-inline">Setting</h2>
    <hr>
    <table class="form-table">
        <tbody>
        <tr valign="top" class="">
            <th scope="row">
                <label for=""> Value Added Tax (VAT) </label>
            </th>
            <td>
                <?php
                $rates = $settings->getRates();
                $amount = isset($rates->amount) ? $rates->amount : 0;
                ?>
                <input onchange="update_rb_chart_rate_detail(0,0,'VAT',this)" class="regular-text ar" type="text"
                       value="<?php echo $amount ?>"> %
            </td>
        </tr>
        </tbody>
    </table>
</div>
