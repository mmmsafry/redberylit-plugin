<?php
global $table_prefix, $wpdb;
$url = $_SERVER['REQUEST_URI'];


if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class ClassVehicleCategory extends WP_List_Table
{

    public $table_prefix;
    public $wpdb;

    private function getVehicleCategory()
    {
        global $table_prefix, $wpdb;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;
        $vehicle_category = $this->wpdb->prefix . 'vehicles_cat';
        // $vehicle = get_posts(['post_type'=>'vehicle','post_status'=>'publish']);

        return $date_range = $this->wpdb->get_results("SELECT * FROM $vehicle_category", ARRAY_A);


    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();
        usort($data, array(&$this, 'sort_data'));
        $perPage = 5;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => $perPage
        ));
        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }


    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
           // 'cb' => '<input type="checkbox" />',
            'id' => 'ID',
            'name' => 'Name',
            'action' => 'Action'
        );
        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return ['name' => ['name', true],'id' => ['id', true]];
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        return $this->getVehicleCategory();
    }


    /**
     * Define what data to show on each column of the table
     *
     * @param Array $item Data
     * @param String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'name':
                return $item[$column_name];

            case 'action':
                return '<a class="float-left" href="?page=redberylit_vehicle_category&eid=' . $item['id'] . '&edit=true&name=' . $item['name'] . '">
<span class="dashicons dashicons-edit"></span></a> &nbsp;
<form class="float-left" style="width: 25px" method="post"> <input type="hidden" value="' . $item['id'] . '" name="id"/>  
<button class="button-link" style="color: #da4d4d;" type="submit" onclick="return confirm(\'Are you sure you want to delete?\')"> <span class="dashicons dashicons-trash"></span></button>
</form>';
            default:
                return print_r($item, true);
        }
    }


    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data($a, $b)
    {
        // Set defaults
        $orderby = 'id';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }
        $result = strcmp($a[$orderby], $b[$orderby]);
        if ($order === 'asc') {
            return $result;
        }
        return -$result;
    }

    /*function column_date_submitted($item){
        $actions = array(
            'edit' => sprintf('<a href="?page=view_application&application=%s">View</a>', $item->id),
            'delete' => sprintf('<a href="?page=view_application&application=%s">Delete</a>','delete',$item->id)
        );

        return sprintf('%1$s %2$s', $item->date_submitted, $this->row_actions($actions) );
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="application[]" value="%s" />', $item['id']
        );
    }*/


}

?>
<div class="wrap">
    <h1 class="wp-heading-inline">Vehicle Category</h1>
    <div class="wp-clearfix">
        <div class="col-wrap">
            <div id="col-left">
                <!--<form name="addnew" method="post">
                    <input type="hidden" name="addnew">
                    <input class="button button-primary" type="submit" name="addnewbtn" value="Add New">
                </form>-->
                <?php
                /**
                 * Edit
                 */
                if (isset($_REQUEST['edit'])) {
                    $id = $_REQUEST['eid'];
                    ?>
                    <form action="?page=redberylit_vehicle_category" method="post">
                        <div class="form-field form-required term-name-wrap">
                            <label for="name">Name</label>
                            <input type="text" name="name" size="40" class="form-control" id="name"
                                   value="<?php echo $_REQUEST['name']; ?>">
                        </div>

                        <div class="form-group">
                            <input type="hidden" class="form-control" id="id" name="idv"
                                   value="<?php echo $_REQUEST['eid']; ?>">
                        </div>
                        <br>

                        <button type="submit" class="button btn-primary" name="editform">Edit Category</button>
                        <a href="? page=redberylit_vehicle_category" class="button btn-primary">Cancel </a>
                    </form>

                    <?php
                }

                if (isset($_REQUEST['editform'])) {
                    $name = $_REQUEST['name'];
                    $id = $_REQUEST['idv'];
                    global $table_prefix, $wpdb;

                    $wpdb->query(
                        'update   ' . $wpdb->prefix . 'vehicles_cat  set name="' . $name . '" WHERE id = "' . $id . '"'
                    );
                } else if (isset($_POST['delete'])) {
                    $id = $_POST['id'];
                    $wpdb->query('DELETE  FROM ' . $wpdb->prefix . 'vehicles_cat WHERE id = "' . $id . '"');
                }

                /**
                 * Add
                 */
                if (!isset($_REQUEST['edit'])) { ?>
                    <form action="?page=redberylit_vehicle_category" method="post">
                        <div class="form-field form-required term-name-wrap">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" size="40" value="">
                        </div>
                        <br>


                        <button type="submit" class="button btn-primary" name="addform">Add Category</button>
                    </form>
                    <?php
                }

                if (isset($_REQUEST['addform'])) {
                    $name = $_REQUEST['name'];
                    global $table_prefix, $wpdb;
                    $wpdb->query('insert into   ' . $wpdb->prefix . 'vehicles_cat  values("","' . $name . '")');
                }
                ?>
            </div>
        </div>
        <div class="col-wrap">


            <?php
            $result = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "vehicles_cat ");
            ?>
            <div id="col-right">
                <?php
                $categoryTable = new ClassVehicleCategory();
                $categoryTable->prepare_items();
                $categoryTable->display();
                ?>
            </div>

        </div>
    </div>
</div>

</div>


<?php
/*wp_register_script( 'DataTable', 'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js', null, null, true );
wp_enqueue_script('DataTable');

wp_register_style( 'DataTable', 'https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css' );
wp_enqueue_style('DataTable');*/
?>
<script type="text/javascript">
    /*jQuery(document).ready(function () {
        jQuery('#example').DataTable();
    });*/
</script>

