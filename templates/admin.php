<?php
global $table_prefix, $wpdb;
$result = $wpdb->get_results("SELECT ID, post_title FROM " . $wpdb->prefix . "posts WHERE post_type='vehicle'");

?>
<div class="wrap">
    <h1>Redberyl Plugin </h1>
    <?php settings_errors(); ?>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab-1">Manage Settings </a></li>
        <li><a href="#tab-2">Update </a></li>
        <li><a href="#tab-3"> About </a></li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">

            <form action="options.php" method="post">
                <?php
                settings_fields('readberylit_option_group');
                do_settings_sections('redberylit_plugin');

                ?>
                <table class="form-table" role="presentation">
                    <tbody>
                    <tr class="example-class">
                        <th scope="row"><label for="text_example">Vehicle </label></th>
                        <td>
                            <select name="vehicle_Id" >
                                <option selected="selected">Select a vehicle </option>
                                <?php
                                foreach ($result as $value) { ?>
                                    <option value="<?php echo $value->ID; ?>"><?php echo $value->post_title; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </td>
                    </tr>

                    </tbody>
                </table>


                <?php
                submit_button();
                ?>
            </form>
        </div>
    </div>


</div>
