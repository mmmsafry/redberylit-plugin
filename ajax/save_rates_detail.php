<?php
require_once(dirname(__FILE__) . '../../../../../wp-load.php');

class rate_chart_post
{

    public $debug;
    public $table_prefix;
    public $wpdb;
    public $table_rate;
    public $table_rate_range;
    public $table_post;
    public $table_rate_chart;

    public $rate_chart_id;
    public $source;

    public function __construct()
    {
        global $table_prefix, $wpdb;
        $this->debug = WP_DEBUG;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;
        $this->post = $_POST;

        /** Tables */
        $this->table_post = $this->wpdb->prefix . 'posts';
        $this->table_rate_chart = $this->wpdb->prefix . 'rate_chart';
        $this->table_rate_range = $this->wpdb->prefix . 'rate_range';
        $this->table_rate = $this->wpdb->prefix . 'rate';

        $this->source = isset($_POST['source']) ? $_POST['source'] : 'rate_chart';
    }

    private function create_rate_chart()
    {
        if ($this->source == 'location') {
            $this->rate_chart_id = $this->post['post_id'];
        } else {
            $post_id = $this->post['post_id'];
            $q = "SELECT * FROM  $this->table_rate_chart  WHERE wp_post_ID='$post_id'";
            $r = $this->wpdb->get_row($q);
            if (!empty($r)) {
                $this->rate_chart_id = $r->id;
            } else {
                $q = "INSERT INTO $this->table_rate_chart (`wp_post_ID`) VALUES ($post_id);";
                $this->wpdb->query($q);

                $this->rate_chart_id = $this->wpdb->insert_id;
            }
        }
    }


    public function updateRatesSpecificColumn($rate_range_id, $amount, $type)
    {

        $this->create_rate_chart();

        $x = "SELECT * FROM $this->table_rate  WHERE rate_chart_id='" . $this->rate_chart_id . "' AND rate_range_id='" . $rate_range_id . "'  AND `type`='" . $type . "'";
        $result = $this->wpdb->get_row($x);
        $output = [];
        if ($this->debug) $output[] = ['operation' => 'select', 'q' => $x];

        if (!empty($result)) {
            $q = "UPDATE $this->table_rate SET amount='$amount' WHERE rate_chart_id='" . $this->rate_chart_id . "' AND rate_range_id='" . $rate_range_id . "'  AND `type`='" . $type . "'  ";
            if ($this->debug) $output[] = ['operation' => 'update', 'q' => $q];
        } else {
            $q = "INSERT INTO $this->table_rate (`rate_chart_id`,`rate_range_id`, `amount`,`type`) VALUES ($this->rate_chart_id,$rate_range_id,$amount,'$type');";
            if ($this->debug) $output[] = ['operation' => 'insert', 'q' => $q];
        }

        $output['result'] = $this->wpdb->query($q);

        return $output;
    }


}

$obj = new rate_chart_post();
$output = $obj->updateRatesSpecificColumn($_POST['rate_range_id'], $_POST['amount'], $_POST['type']);
echo json_encode(array('response' => '200', $output));