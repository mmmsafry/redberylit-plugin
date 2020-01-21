<?php
/**
 * @package           redberylit
 */

namespace Inc\Base;
class Activate
{

    public static $table_prefix;
    public static $wpdb;

    public static $table_rate;
    public static $table_rate_range;
    public static $table_rate_chart;
    public static $table_vehicles_cat;
    public static $table_driver;
    public static $table_vehicle_class;

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        flush_rewrite_rules();
        self::initTableName();
        self::create_plugin_database_table();
    }

    public static function initTableName()
    {
        global $table_prefix, $wpdb;

        self::$table_prefix = $table_prefix;
        self::$wpdb = $wpdb;

        /** Tables */
        self::$table_rate = $table_prefix . "rate";
        self::$table_rate_range = $table_prefix . "rate_range";
        self::$table_rate_chart = $table_prefix . "rate_chart";
        self::$table_vehicles_cat = $table_prefix . 'vehicles_cat';
        self::$table_driver = $table_prefix . 'driver';
        self::$table_vehicle_class = $table_prefix . 'licence_class';

    }

    public static function create_plugin_database_table()
    {

        require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
        #Check to see if the table exists already, if not, then create it
        if (self::$wpdb->get_var("show tables like '" . self::$table_vehicles_cat . "'") != self::$table_vehicles_cat) {

            $sql = "CREATE TABLE `" . self::$table_vehicles_cat . "`  (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `name` varchar(900) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                      `is_wedding` int(1) NULL DEFAULT 0 COMMENT 'allowed to wedding search',
                      `is_deleted` int(1) NULL DEFAULT 0,
                      PRIMARY KEY (`id`) USING BTREE
                    );";

            $sql .= "INSERT INTO `" . self::$table_vehicles_cat . "` VALUES (1, 'Cars', 1, 0);
                        INSERT INTO `" . self::$table_vehicles_cat . "` VALUES (2, 'SUV / Cabs', 1, 0);
                        INSERT INTO `" . self::$table_vehicles_cat . "` VALUES (3, 'Vans / Buses  ', 0, 0);
                        INSERT INTO `" . self::$table_vehicles_cat . "` VALUES (4, 'Trucks/Lorries', 0, 0);
                        INSERT INTO `" . self::$table_vehicles_cat . "` VALUES (5, ' Motorbikes', 0, 0);
                        INSERT INTO `" . self::$table_vehicles_cat . "` VALUES (6, 'Tuk Tuks', 0, 0);";
            dbDelta($sql);
        }


        if (self::$wpdb->get_var("show tables like '" . self::$table_rate . "'") != self::$table_rate) {
            $sql = "CREATE TABLE `" . self::$table_rate . "`  (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `rate_chart_id` int(11) NULL DEFAULT NULL,
              `rate_range_id` int(11) NULL DEFAULT NULL,
              `amount` double NULL DEFAULT NULL,
              `type` enum('WD','SD') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'WD' COMMENT 'With Drive, Self Drive',
              PRIMARY KEY (`id`) USING BTREE
            );";
            dbDelta($sql);
        }

        if (self::$wpdb->get_var("show tables like '" . self::$table_rate_chart . "'") != self::$table_rate_chart) {
            $sql = "CREATE TABLE `" . self::$table_rate_chart . "`  (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `wp_post_ID` int(11) NULL DEFAULT NULL,
                      `wp_vehicle_category_id` int(11) NULL DEFAULT NULL,
                      `deposit` double NULL DEFAULT NULL,
                      `extra_amount_per_km` double NULL DEFAULT NULL,
                      `extra_amount_per_hour` double NULL DEFAULT NULL,
                      `wedding_per_hour` double NULL DEFAULT NULL,
                      `wedding_extra_hour_km` double NULL DEFAULT NULL,
                      `drop_hire_per_km` double NULL DEFAULT NULL,
                      PRIMARY KEY (`id`) USING BTREE
                    );";
            dbDelta($sql);
        }

        if (self::$wpdb->get_var("show tables like '" . self::$table_rate_range . "'") != self::$table_rate_range) {
            $sql = "CREATE TABLE `" . self::$table_rate_range . "`  (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `description` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
                      `min_days` int(11) NULL DEFAULT NULL,
                      `max_days` int(11) NULL DEFAULT NULL,
                      `is_active` int(1) NULL DEFAULT 1,
                      PRIMARY KEY (`id`) USING BTREE
                    );";

            $sql .= "INSERT INTO `" . self::$table_rate_range . "` VALUES (1, '1-2 Days', 1, 2, 1);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (2, '3-4 Days', 3, 4, 1);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (3, '5-6 Days', 5, 6, 1);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (4, '7-8 Days', 7, 8, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (5, '9-10 Days', 9, 10, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (6, '11-12 Days', 11, 12, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (7, '13-14 Days', 13, 14, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (8, '15-16 Days', 15, 16, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (9, '17-18 Days', 17, 18, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (10, '19-20 Days', 19, 20, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (11, '21-22 Days', 21, 22, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (12, '23-24 Days', 23, 24, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (13, '25-26 Days', 25, 26, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (14, '27-28 Days', 27, 28, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (15, '29-30 Days', 29, 30, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (16, '31-365 Days', 31, 1000, 0);
                        INSERT INTO `" . self::$table_rate_range . "` VALUES (17, 'Excess', 0, 0, 0);";
            dbDelta($sql);
        }

        if (self::$wpdb->get_var("show tables like '" . self::$table_driver . "'") != self::$table_driver) {
            $sql = "CREATE TABLE `" . self::$table_driver . "`  (
                      `ID` int(11) NOT NULL AUTO_INCREMENT,
                      `first_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
                      `last_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
                      `nic` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
                      `mobile` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
                      `licence_no` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
                      PRIMARY KEY (`ID`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1;";

            dbDelta($sql);
        }

        if (self::$wpdb->get_var("show tables like '" . self::$table_vehicle_class . "'") != self::$table_vehicle_class) {
            $sql = "
                    CREATE TABLE `" . self::$table_vehicle_class . "`  (
                      `ID` int(11) NOT NULL AUTO_INCREMENT,
                      `licence_code` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
                      `description` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
                      PRIMARY KEY (`ID`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;";

            $sql .= "INSERT INTO `wp_licence_class` VALUES (1, 'A1', 'Light motor cycles of which Engine Capacity does not exceeds 100CC');
INSERT INTO `wp_licence_class` VALUES (2, 'A', 'Motorcycles of which Engine capacity exceeds 100CC');";

            dbDelta($sql);
        }


    }

}
