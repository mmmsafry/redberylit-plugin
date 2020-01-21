<?php
global $table_prefix, $wpdb;
$url = $_SERVER['REQUEST_URI'];
$pageName = isset($_GET['page']) ? $_GET['page'] : '';
define('PAGE_NAME', $pageName);
define('PAGE_SINGULAR', "Licence Class");
define('PAGE_PLURAL', "Licence Classes");

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class LicenceClass extends WP_List_Table
{
    public $table_prefix;
    public $wpdb;
    public $table_name;

    public function __construct($args = array())
    {
        parent::__construct($args);
        global $table_prefix, $wpdb;
        $this->table_prefix = $table_prefix;
        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . 'licence_class';
    }

    private function getTableOutput()
    {
        return $this->wpdb->get_results("SELECT * FROM $this->table_name", ARRAY_A);
    }


    public function setTableName()
    {
        $this->table_name = $this->table_prefix . 'licence_class';
    }

    public function getTableName()
    {
        $this->setTableName();
        return $this->table_name;
    }

    public function get_single_record($id)
    {
        return $this->wpdb->get_row("SELECT * FROM $this->table_name WHERE ID=" . $id);
    }

    private function trimArray($array)
    {
        if (!empty($array)) {
            foreach ($array as $key => $item) {
                $array[$key] = htmlspecialchars(trim($item));
            }
            return $array;
        }

    }

    public function insert_table(array $request)
    {
        $request = $this->trimArray($request);
        $sql = "INSERT INTO " . $this->table_name . " (licence_code,description) VALUES ('" . $request['licence_code'] . "','" . $request['licence_code'] . "' ) ";
        return $this->wpdb->query($sql);
    }

    public function update_table(array $request)
    {
        $request = $this->trimArray($request);
        $sql = "UPDATE " . $this->table_name . " SET licence_code= '" . $request['licence_code'] . "', description = '" . $request['description'] . "' WHERE id = " . $request['ID'];
        return $this->wpdb->query($sql);
    }

    public function delete_record($id)
    {
        if ($id > 0) {
            $sql = "DELETE FROM  " . $this->table_name . " WHERE ID= '" . $id . "'";
            return $this->wpdb->query($sql);
        }
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
            'ID' => 'ID',
            'licence_code' => 'licence_code',
            'description' => 'description',
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
        return ['name' => ['name', true], 'ID' => ['ID', true]];
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        return $this->getTableOutput();
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
            case 'ID':
            case 'licence_code':
            case 'description':
                return $item[$column_name];

            case 'action':
                return '<a class="float-left" href="?page=' . PAGE_NAME . '&eid=' . $item['ID'] . '&edit=true">
<span class="dashicons dashicons-edit"></span></a> &nbsp;
<form class="float-left" style="width: 25px" method="post"> <input type="hidden" value="' . $item['ID'] . '" name="ID"/>  
<input type="hidden" name="delete" value="1">
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
        $orderby = 'ID';
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
        return $result;
    }


}

?>
<div class="wrap">
    <h1 class="wp-heading-inline">Licence Class </h1>
    <div class="wp-clearfix">
        <div class="col-wrap">
            <div id="col-left">

                <?php
                /**
                 * Edit
                 */
                if (isset($_REQUEST['edit'])) {
                    $id = $_REQUEST['eid'];
                    $table_edit = new LicenceClass();
                    $record = $table_edit->get_single_record($id);
                    if (!empty($record)) {
                        ?>
                        <form action="?page=<?php echo PAGE_NAME ?>" method="post">
                            <input type="hidden" class="form-control" id="ID" name="ID"
                                   value="<?php echo $id; ?>">

                            <!--licence_code -->
                            <div class="form-field form-required term-name-wrap">
                                <label for="licence_code">Licence Code</label>
                                <input type="text" name="licence_code" size="40" class="form-control" id="licence_code"
                                       value="<?php echo $record->licence_code; ?>">
                            </div>

                            <!--description -->
                            <div class="form-field form-required term-name-wrap">
                                <label for="description">Description</label>
                                <input type="text" name="description" size="40" class="form-control" id="description"
                                       value="<?php echo $record->description; ?>">
                            </div>

                            <br>

                            <button type="submit" class="button btn-primary" name="editform">
                                Edit <?php echo PAGE_SINGULAR ?></button>
                            <a href="?page=<?php echo PAGE_NAME ?>" class="button btn-primary">Cancel </a>
                        </form>

                        <?php
                    }
                }

                if (isset($_REQUEST['editform'])) {
                    $instant_LicenceClass = new LicenceClass();
                    $instant_LicenceClass->update_table($_REQUEST);
                } else if (isset($_POST['delete'])) {
                    $id = $_POST['ID'];
                    $instant_LicenceClass = new LicenceClass();
                    $instant_LicenceClass->delete_record($id);
                }

                /**
                 * Add
                 */
                if (!isset($_REQUEST['edit'])) { ?>
                    <form action="?page=<?php echo PAGE_NAME ?>" method="post">

                        <!--licence_code -->
                        <div class="form-field form-required term-name-wrap">
                            <label for="licence_code">Licence Code</label>
                            <input type="text" name="licence_code" size="40" class="form-control" id="licence_code"
                                   value="">
                        </div>

                        <!--description -->
                        <div class="form-field form-required term-name-wrap">
                            <label for="description">Description</label>
                            <input type="text" name="description" size="40" class="form-control" id="description"
                                   value="">
                        </div>
                        <br>


                        <button type="submit" class="button btn-primary" name="addform">
                            Add <?php echo PAGE_SINGULAR ?>
                        </button>
                    </form>
                    <?php
                }

                if (isset($_REQUEST['addform'])) {
                    $instant_LicenceClass = new LicenceClass();
                    $instant_LicenceClass->insert_table($_REQUEST);
                }
                ?>
            </div>
        </div>
        <div class="col-wrap">
            <?php
            $table = new LicenceClass();
            $tableName = $table->getTableName();
            $result = $wpdb->get_results("SELECT * FROM " . $tableName);
            ?>
            <div id="col-right">
                <?php
                $table->prepare_items();
                $table->display();
                ?>
            </div>
        </div>
    </div>
</div>

</div>



