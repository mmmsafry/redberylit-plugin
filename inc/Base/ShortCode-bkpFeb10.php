<?php

namespace Inc\Base;


class ShortCode extends BaseController
{
    public function register()
    {
        add_shortcode('rb_front_end_form', [$this, 'getFormShortCode']);
        add_shortcode('base_url', [$this, 'base_url_redberylit']);
        add_shortcode('country_list_casons', 'rb_get_country_list');
    }

    function base_url_redberylit()
    {
        return get_site_url();
    }


    function getFormShortCode()
    {
        $html = "";
        $vehicles_search_url = get_option("autoroyal_search_rent_vehicles_page_id");
        global $table_prefix, $wpdb;
        ?>
        <style>
            .autoroyal-rent-booking-dates-update {
                padding: 10px 15px;
            }

            .nav-tabs .nav-link {
                min-height: 45px !important;
                line-height: 1.5em !important;
                font-size: 0.9em !important;
                padding: .75rem 1rem .25rem 1rem;
            }

            .chosen-container .chosen-results li, .chosen-container-single .chosen-single span {
                font-size: 1.0em !important;
            }

            .chosen-container-single .chosen-single span {
                font-size: 0.8em !important;
            }

            #myTabContent .search-form-wrapper {
                padding: 3px !important;
            }

            #myTabContent .right-separator {
                padding-top: 15px;
            }

            #myTabContent .chosen-container {
                border: 1px solid #e4e7e8;
                padding: 10px;
                border-radius: 5px;
            }

            #airport_transfers .btn-secondary, #airport_transfers .btn-secondary:hover {
                background-color: #594f4f;
                color: #fff;
            }

            #airport_transfers .active, #airport_transfers .active:hover {
                background-color: #50a70d;
                color: #fff;
            }

            /** google map css */
            #map_output, #map_output_transfer {
                padding: 3px;
                font-weight: 800;
                color: #488ef6;
            }


            /*******tab css home page***********************/
            @media only screen and (min-width: 320px) {
                .home-search-tab .mobile-v {
                    width: 100%;
                }
            }

            @media only screen and (min-width: 768px) {
                .home-search-tab .mobile-v {
                    width: 25%;
                    font-size: 0.8rem;
                }
            }

            /* ----------- Google Map Drop Down --------------*/
            .pac-container {
                z-index: 99999999999999 !important;
            }

            /* ----------- end Google Map Drop Down --------------*/

        </style>
        <ul class="nav nav-tabs p-0" id="myTab" role="tablist">
            <li class="nav-item col">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#self_drive" role="tab"
                   aria-controls="selfDrive" aria-selected="true">
                    <?php esc_html_e('Self Drive', 'autoroyal'); ?>
                </a>
            </li>
            <li class="nav-item col">
                <a class="nav-link" id="with-driver-tab" data-toggle="tab" href="#with_driver" role="tab"
                   aria-controls="withDriver" aria-selected="false">
                    <?php esc_html_e('With Driver', 'autoroyal'); ?>
                </a>
            </li>
            <li class="nav-item col">
                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#airport_transfers" role="tab"
                   aria-controls="contact" aria-selected="false">
                    <?php esc_html_e('Airport Hire', 'autoroyal'); ?>
                </a>
            </li>
            <li class="nav-item col">
                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#transfers" role="tab"
                   aria-controls="contact" aria-selected="false">
                    <?php esc_html_e('Taxi Transfers', 'autoroyal'); ?>
                </a>
            </li>
            <li class="nav-item col">
                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#wedding" role="tab"
                   aria-controls="contact" aria-selected="false">
                    <?php esc_html_e('Weddings', 'autoroyal'); ?>
                </a>
            </li>
            <li class="nav-item col">
                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#tuk_motorbike" role="tab"
                   aria-controls="contact" aria-selected="false">
                    Tuk Motorbike
                </a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <input type="hidden" id="currentTabName" value="">
            <!-- ------------------------------------------- self_drive ---------------------------------------------- -->
            <div class="tab-pane fade show active" id="self_drive" role="tabpanel"
                 aria-labelledby="self-drive-tab">
                <form id="autoroyal-advance-search-form"
                      action="<?php echo get_permalink($vehicles_search_url); ?>"
                      method="GET">

                    <input type="hidden" value="self_drive" name="form_source">


                    <div class="search-form-wrapper">

                        <div class="row">

                            <div class="col-md-12 mb-2 ">

                                <div class="row">

                                    <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">

                                        <div class="title"><?php esc_html_e('Pick-up location', 'autoroyal'); ?></div>
                                        <select onchange="filterPickupAndReturnLocationValues()"
                                                id="rezerve-pickup-place-filter"
                                                name="rezerve-pickup-place-filter"
                                                class="chosen-select" autocomplete="off">
                                            <option value="0"><?php esc_html_e('Select location', 'autoroyal'); ?></option>
                                            <?php
                                            $categories = get_categories(array('taxonomy' => 'vehicle_rent_pickup', 'hide_empty' => false, 'parent' => 0));
                                            foreach ($categories as $category) {
                                                ?>
                                                <option value="<?php echo esc_attr($category->cat_name); ?>"><?php echo esc_attr($category->cat_name); ?></option>
                                            <?php } ?>
                                        </select>

                                        <br>
                                        <input onchange="clickReturnLocation(this)" id="differentReturnLocation"
                                               name="differentReturnLocation" type="checkbox"> Different Return
                                        Location
                                    </div>


                                </div>

                            </div>

                            <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30"
                                 id="returnLocationInput" style="display: none;">

                                <div class="title"><?php esc_html_e('Return location', 'autoroyal'); ?></div>
                                <select id="rezerve-return-place-filter"
                                        name="rezerve-return-place-filter"
                                        class="chosen-select" autocomplete="off">
                                    <option value="0"><?php esc_html_e('Return location', 'autoroyal'); ?></option>
                                    <?php

                                    $categories = get_categories(array('taxonomy' => 'vehicle_rent_pickup', 'hide_empty' => false, 'parent' => 0));
                                    foreach ($categories as $category) {

                                        ?>
                                        <option value="<?php echo esc_attr($category->cat_name); ?>"><?php echo esc_attr($category->cat_name); ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-12 ">

                                <div class="row">

                                    <div class="col-lg-12 col-md-12">

                                        <div class="row">

                                            <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">

                                                <div class="autoroyal-rent-filter-date">

                                                    <div class="title"><?php esc_html_e('Pick-up date', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text" name="rezerve-pickup-date"
                                                               placeholder="<?php esc_attr_e('Choose a date', 'autoroyal'); ?>"
                                                               class="daySelect" id="curentDay-filter"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title"><?php esc_html_e('Pick-up time', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text" name="rezerve-pickup-time"
                                                               placeholder="12:00 AM" id="curentHour-filter"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-lg-12 col-md-12 right-separator">

                                                <div class="autoroyal-rent-filter-date">

                                                    <div class="title"><?php esc_html_e('Return date', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text" name="rezerve-drop-date"
                                                               placeholder="<?php esc_attr_e('Choose a date', 'autoroyal'); ?>"
                                                               class="daySelect" id="dropDay-filter"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title"><?php esc_html_e('Return time', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text" name="rezerve-drop-time"
                                                               placeholder="12:00 AM" id="dropHour-filter"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-lg-12 col-md-12 right-separator">

                                                <div class="title"><?php esc_html_e('Vehicle Type', 'autoroyal'); ?></div>
                                                <select id="category"
                                                        name="category"
                                                        class="chosen-select" autocomplete="off">
                                                    <option value="0"><?php esc_html_e('Vehicle Type', 'autoroyal'); ?></option>
                                                    <option value="1,2" selected>Cars & SUVs</option>

                                                    <?php

                                                    $q = "SELECT * FROM $table_prefix" . "vehicles_cat WHERE is_deleted=0";
                                                    $vehicle_list = $wpdb->get_results($q);
                                                    foreach ($vehicle_list as $type) {

                                                        ?>
                                                        <option value="<?php echo esc_attr($type->id); ?>"><?php echo esc_attr($type->name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-3 col-md-4 mt-2">

                                        <button type="submit"
                                                class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn"><?php esc_html_e('Search', 'autoroyal'); ?></button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </form>
            </div>

            <!-- ------------------------------------------- with_driver ---------------------------------------------- -->
            <div class="tab-pane fade" id="with_driver" role="tabpanel" aria-labelledby="with-driver-tab">
                <form id="autoroyal-advance-search-form_with_driver"
                      action="<?php echo get_permalink($vehicles_search_url); ?>"
                      method="GET">

                    <input type="hidden" value="with_driver" name="form_source">
                    <div class="search-form-wrapper">

                        <div class="row">


                            <div class="col-md-12 ">
                                <div class="row">

                                    <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                        <div class="btn-block btn-group btn-group-toggle home-search-tab"
                                             data-toggle="buttons">
                                            <?php
                                            $rateRanges = $wpdb->get_results("SELECT *  FROM wp_rate_range  WHERE TYPE = 'WD' AND is_active=1  ORDER BY id ASC ");
                                            $i = 0;
                                            foreach ($rateRanges as $rateRange) {
                                                $active = $i = 0 ? 'active' : '';
                                                ?>
                                                <div class="mobile-v col btn btn-secondary <?php echo $active ?>">
                                                    <input type="radio" name="packages"
                                                           value="<?php echo $rateRange->id ?>"
                                                           autocomplete="off"
                                                           checked> <?php echo $rateRange->description ?> KMs
                                                    <br>Package
                                                </div>
                                                <?php
                                                $i++;

                                            }
                                            ?>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-12 mb-2 ">

                                <div class="row">

                                    <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">

                                        <div class="title"><?php esc_html_e('Pick-up location', 'autoroyal'); ?></div>
                                        <select id="rezerve-pickup-place-filter-with_driver"
                                                name="rezerve-pickup-place-filter"
                                                class="chosen-select" autocomplete="off">
                                            <option value="0"><?php esc_html_e('Select location', 'autoroyal'); ?></option>
                                            <?php

                                            $categories = get_categories(array('taxonomy' => 'vehicle_rent_pickup', 'hide_empty' => false, 'parent' => 0));
                                            foreach ($categories as $category) {

                                                ?>
                                                <option value="<?php echo esc_attr($category->cat_name); ?>"><?php echo esc_attr($category->cat_name); ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>


                                </div>

                            </div>

                            <div class="col-md-12 ">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                                <div class="autoroyal-rent-filter-date">

                                                    <div class="title"><?php esc_html_e('Pick-up date', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text" name="rezerve-pickup-date"
                                                               placeholder="<?php esc_attr_e('Choose a date', 'autoroyal'); ?>"
                                                               class="daySelect"
                                                               id="curentDay-filter-with-driver"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title"><?php esc_html_e('Pick-up time', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text" name="rezerve-pickup-time"
                                                               placeholder="12:00 AM"
                                                               id="curentHour-filter-with-driver"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-lg-12 col-md-12 right-separator">

                                                <div class="autoroyal-rent-filter-date">

                                                    <div class="title"><?php esc_html_e('Return date', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text" name="rezerve-drop-date"
                                                               placeholder="<?php esc_attr_e('Choose a date', 'autoroyal'); ?>"
                                                               class="daySelect" id="dropDay-filter-with-driver"
                                                               value="01/07/20120"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title"><?php esc_html_e('Return time', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text" name="rezerve-drop-time"
                                                               placeholder="12:00 AM"
                                                               id="dropHour-filter-with-driver"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-lg-12 col-md-12 right-separator">

                                                <div class="title"><?php esc_html_e('Vehicle Type', 'autoroyal'); ?></div>
                                                <select id="category-with-drive"
                                                        name="category"
                                                        class="chosen-select" autocomplete="off">
                                                    <option value="0"><?php esc_html_e('Vehicle Type', 'autoroyal'); ?></option>
                                                    <option value="1,2" selected>Cars & SUVs</option>

                                                    <?php
                                                    foreach ($vehicle_list as $type) {
                                                        ?>
                                                        <option value="<?php echo esc_attr($type->id); ?>"><?php echo esc_attr($type->name); ?></option>
                                                    <?php } ?>
                                                </select>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-3 col-md-4 mt-2">

                                        <button type="submit"
                                                class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn"><?php esc_html_e('Search', 'autoroyal'); ?></button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </form>
            </div>

            <!-- ------------------------------------------- airport_transfers ---------------------------------------------- -->
            <div class="tab-pane fade" id="airport_transfers" role="tabpanel" aria-labelledby="contact-tab">
                <form id="autoroyal-advance-search-form_airport_transfers"
                      action="<?php echo get_permalink($vehicles_search_url); ?>"
                      method="GET">
                    <input type="hidden" value="airport_transfers" name="form_source">
                    <input type="hidden" value="0" name="km_distance_airport" id="km_distance_airport">

                    <div class="search-form-wrapper">
                        <div class="row">
                            <div class="col-md-12 ">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                        <div class="btn-block btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-secondary active">
                                                <input type="radio" name="destination" id="destination_from"
                                                       onchange="switch_airport_location('from_airport')"
                                                       autocomplete="off" checked> From Airport
                                            </label>

                                            <label class="btn btn-secondary">
                                                <input type="radio" name="destination" id="destination_to"
                                                       onchange="switch_airport_location('to_airport')"
                                                       autocomplete="off"> To Airport
                                            </label>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-12 mb-2 ">

                                <div class="row">
                                    <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">

                                        <div class="title"><?php esc_html_e('Pick-up location', 'autoroyal'); ?></div>
                                        <input type="text" name="pick-up-location" value=""
                                               id="start">


                                    </div>

                                    <div class="col-lg-12 col-md-12 right-separator">
                                        <div class="title"><?php esc_html_e('Drop Off location', 'autoroyal'); ?></div>
                                        <input type="text" name="drop-off-location" value=""
                                               id="end">

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-12 ">

                                <div class="row">

                                    <div class="col-lg-12 col-md-12">

                                        <div class="row">

                                            <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">

                                                <div class="autoroyal-rent-filter-date">

                                                    <div class="title"><?php esc_html_e('Pick-up date', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text" name="rezerve-pickup-date"
                                                               placeholder="<?php esc_attr_e('Choose a date', 'autoroyal'); ?>"
                                                               class="daySelect"
                                                               id="curentDay-filter-airport-transfers"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title"><?php esc_html_e('Pick-up time', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text" name="rezerve-pickup-time"
                                                               placeholder="12:00 AM"
                                                               id="curentHour-filter-airport-transfers"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                            </div>


                                        </div>

                                    </div>

                                    <div class="col-lg-3 col-md-4 mt-2">
                                        <button type="submit"
                                                class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn"><?php esc_html_e('Search', 'autoroyal'); ?></button>
                                    </div>
                                    <div class="col-lg-8 col-md-4 mt-3">
                                        <div id="map_output"></div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </form>
            </div>

            <!-- ------------------------------------------- transfers ---------------------------------------------- -->
            <div class="tab-pane fade" id="transfers" role="tabpanel" aria-labelledby="contact-tab">
                <form id="autoroyal-advance-search-form_transfers"
                      action="<?php echo get_permalink($vehicles_search_url); ?>"
                      method="GET">
                    <input type="hidden" value="transfers" name="form_source">

                    <div class="search-form-wrapper">
                        <div class="row">
                            <div class="col-md-12 mb-2 ">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                        <div class="title"><?php esc_html_e('Pick-up location', 'autoroyal'); ?></div>
                                        <input type="text" name="pick-up-location" value="" id="transfer_start">
                                    </div>

                                    <div class="col-lg-12 col-md-12 right-separator">
                                        <div class="title"><?php esc_html_e('Return location', 'autoroyal'); ?></div>
                                        <input type="text" name="pick-up-location" value="" id="transfer_end">

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 ">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                                <div class="autoroyal-rent-filter-date">
                                                    <div class="title"><?php esc_html_e('Pick-up date', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text" name="rezerve-pickup-date"
                                                               placeholder="<?php esc_attr_e('Choose a date', 'autoroyal'); ?>"
                                                               class="daySelect" id="curentDay-filter-transfers"
                                                               autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title"><?php esc_html_e('Pick-up time', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text" name="rezerve-pickup-time"
                                                               placeholder="12:00 AM"
                                                               id="curentHour-filter-transfers"
                                                               autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- -------------------------------------- Transfer Return Location --------------------------------------  -->
                                    <div class="col-md-12 mb-2 ">
                                        <p class="cus-p">
                                            <input onchange="transfer_return_location(this)"
                                                   id="transfer_differentReturnLocation"
                                                   name="differentReturnLocation" type="checkbox"> Return
                                            Transfer</p>
                                    </div>

                                    <div id="transfer_return_div" style="display: none;" class="col-md-12 mb-2">

                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                                <div class="title"><?php esc_html_e('Pickup location (Return)', 'autoroyal'); ?></div>
                                                <input type="text" name="pick-up-location" value=""
                                                       id="return-transfer_start" readonly>
                                            </div>

                                            <div class="col-lg-12 col-md-12 right-separator">
                                                <div class="title"><?php esc_html_e('Drop-offn location (Return)', 'autoroyal'); ?></div>
                                                <input type="text" name="pick-up-location" value=""
                                                       id="transfer_transfer_end" readonly>

                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                                <div class="autoroyal-rent-filter-date">
                                                    <div class="title"><?php esc_html_e('Return date', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text" name="return-rezerve-pickup-date"
                                                               placeholder="<?php esc_attr_e('Choose a date', 'autoroyal'); ?>"
                                                               class="daySelect"
                                                               id="return-curentDay-filter-transfers"
                                                               autocomplete="off" readonly>
                                                    </div>
                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title"><?php esc_html_e('Return time', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text" name="return-rezerve-pickup-time"
                                                               placeholder="12:00 AM"
                                                               id="return-curentHour-filter-transfers"
                                                               autocomplete="off" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- -------------------------------------- end Transfer Return Location --------------------------------------  -->
                                    <div class="col-lg-3 col-md-4 mt-2">
                                        <button type="submit"
                                                class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn"><?php esc_html_e('Search', 'autoroyal'); ?></button>

                                    </div>
                                    <div class="col-lg-8 col-md-4 mt-3">
                                        <div id="map_output_transfer"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ------------------------------------------- wedding ---------------------------------------------- -->
            <div class="tab-pane fade" id="wedding" role="tabpanel" aria-labelledby="contact-tab">
                <form id="autoroyal-advance-search-form_wedding"
                      action="<?php echo get_permalink($vehicles_search_url); ?>"
                      method="GET">

                    <input type="hidden" value="wedding" name="form_source">
                    <div class="search-form-wrapper">
                        <div class="row">
                            <div class="col-md-12 mb-2 ">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                        <div class="title"><?php esc_html_e('Pick-up location', 'autoroyal'); ?></div>
                                        <select id="rezerve-pickup-place-filter-wedding"
                                                name="rezerve-pickup-place-filter"
                                                class="chosen-select" autocomplete="off">
                                            <!--<option value="0"><?php /*esc_html_e('Select location', 'autoroyal'); */ ?></option>-->
                                            <option value="Colombo" selected>Colombo</option>
                                            <?php

                                            /*$categories = get_categories(array('taxonomy' => 'vehicle_rent_pickup', 'hide_empty' => false, 'parent' => 0));
                                            foreach ($categories as $category) {

                                                */ ?><!--
                                                        <option value="<?php /*echo esc_attr($category->cat_name); */ ?>"><?php /*echo esc_attr($category->cat_name); */ ?></option>
                                                    --><?php /*}*/ ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 ">
                                <div class="row">


                                    <div class="col-lg-12 col-md-12">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                                <div class="autoroyal-rent-filter-date">
                                                    <div class="title"><?php esc_html_e('Pick-up date', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text" name="rezerve-pickup-date"
                                                               placeholder="<?php esc_attr_e('Choose a date', 'autoroyal'); ?>"
                                                               class="daySelect" id="curentDay-filter-wedding"
                                                               autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="autoroyal-rent-filter-time">
                                                    <div class="title"><?php esc_html_e('Pick-up time', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text" name="rezerve-pickup-time"
                                                               placeholder="12:00 AM"
                                                               id="curentHour-filter-wedding"
                                                               autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 right-separator">

                                        <div class="title"><?php esc_html_e('Vehicle Type', 'autoroyal'); ?></div>
                                        <select id="category-wedding"
                                                name="category"
                                                class="chosen-select" autocomplete="off">
                                            <option value="0"><?php esc_html_e('Vehicle Type', 'autoroyal'); ?></option>
                                            <?php
                                            $vehicle_list = $wpdb->get_results("SELECT * FROM $table_prefix" . "vehicles_cat WHERE is_deleted=0 AND is_wedding=1");
                                            foreach ($vehicle_list as $type) {

                                                ?>
                                                <option value="<?php echo esc_attr($type->id); ?>"><?php echo esc_attr($type->name); ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>


                                    <div class="col-lg-3 col-md-4 mt-2">

                                        <button type="submit"
                                                class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn"><?php esc_html_e('Search', 'autoroyal'); ?></button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </form>
            </div>

            <!-- ------------------------------------------- Tuk & Motorbike ---------------------------------------------- -->
            <div class="tab-pane fade" id="tuk_motorbike" role="tabpanel" aria-labelledby="contact-tab">
                <form id="autoroyal-advance-search-form_tuk_motorbike"
                      action="<?php echo get_permalink($vehicles_search_url); ?>"
                      method="GET">

                    <input type="hidden" value="with_driver" name="form_source">
                    <div class="search-form-wrapper">

                        <div class="row">


                            <div class="col-md-12 ">
                                <div class="row">

                                    <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                        <div class="btn-block btn-group btn-group-toggle home-search-tab"
                                             data-toggle="buttons">
                                            <div class="mobile-v col btn btn-secondary active">
                                                <input type="radio" name="category" value="6"
                                                       autocomplete="off" checked> Tuk Tuk
                                            </div>

                                            <div class="mobile-v col btn btn-secondary">
                                                <input type="radio" value="5" name="category"
                                                       autocomplete="off"> Motorbike
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-md-12 mb-2 ">

                                <div class="row">

                                    <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">

                                        <div class="title"><?php esc_html_e('Pick-up location', 'autoroyal'); ?></div>
                                        <select id="rezerve-pickup-place-filter-tuk_motorbike"
                                                name="rezerve-pickup-place-filter"
                                                class="chosen-select" autocomplete="off">
                                            <option value="0"><?php esc_html_e('Select location', 'autoroyal'); ?></option>
                                            <?php

                                            $categories = get_categories(array('taxonomy' => 'vehicle_rent_pickup', 'hide_empty' => false, 'parent' => 0));
                                            foreach ($categories as $category) {

                                                ?>
                                                <option value="<?php echo esc_attr($category->cat_name); ?>"><?php echo esc_attr($category->cat_name); ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>


                                </div>

                            </div>

                            <div class="col-md-12 ">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                                <div class="autoroyal-rent-filter-date">

                                                    <div class="title"><?php esc_html_e('Pick-up date', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text" name="rezerve-pickup-date"
                                                               placeholder="<?php esc_attr_e('Choose a date', 'autoroyal'); ?>"
                                                               class="daySelect"
                                                               id="curentDay-filter-tuk-motorbike"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title"><?php esc_html_e('Pick-up time', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text" name="rezerve-pickup-time"
                                                               placeholder="12:00 AM"
                                                               id="curentHour-filter-tuk-motorbike"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-lg-12 col-md-12 right-separator">

                                                <div class="autoroyal-rent-filter-date">

                                                    <div class="title"><?php esc_html_e('Return date', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text" name="rezerve-drop-date"
                                                               placeholder="<?php esc_attr_e('Choose a date', 'autoroyal'); ?>"
                                                               class="daySelect"
                                                               id="dropDay-filter-tuk-motorbike"
                                                               value="01/07/20120"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title"><?php esc_html_e('Return time', 'autoroyal'); ?></div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text" name="rezerve-drop-time"
                                                               placeholder="12:00 AM"
                                                               id="dropHour-filter-tuk-motorbike"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                            </div>


                                        </div>

                                    </div>

                                    <div class="col-lg-3 col-md-4 mt-2">

                                        <button type="submit"
                                                class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn"><?php esc_html_e('Search', 'autoroyal'); ?></button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </form>
            </div>

        </div>
        <script>
            function init_form(tabName) {
                if (tabName == 'self-drive') {
                    tabName = '';
                } else {
                    tabName = '-' + tabName;
                }

                var curentHour = '#curentHour' + tabName;
                if (jQuery("#rezerve-pickup-place-filter" + tabName).exists() || jQuery('#rezerve-pickup-place-filter' + tabName).val()) {
                    jQuery("#rezerve-pickup-place" + tabName).val(jQuery('#rezerve-pickup-place-filter' + tabName).val());
                    jQuery('.chosen-select').trigger("chosen:updated");
                }

                if (jQuery("#rezerve-drop-place-filter" + tabName).exists() || jQuery('#rezerve-drop-place-filter' + tabName).val()) {
                    jQuery("#rezerve-dropoff-place" + tabName).val(jQuery('#rezerve-drop-place-filter' + tabName).val());
                    jQuery('.chosen-select').trigger("chosen:updated");
                }


                if (jQuery(curentHour).exists() || jQuery("#curentHour-filter" + tabName).exists()) {

                    jQuery('#curentHour' + tabName + '  , #curentHour-filter' + tabName + '').datetimepicker({
                        datepicker: false,
                        ampm: true,
                        format: 'g:i A',
                        hours24: true, //time format 24h/12h
                        scrollInput: false,
                        onSelectTime: function (current_time, $input) {
                            jQuery("#pickT" + tabName + ", .pickT").text(jQuery(curentHour).val());
                            jQuery('#dropHour' + tabName + '').val(jQuery('#curentHour' + tabName + '').val());
                            jQuery('#dropHour-filter' + tabName).val(jQuery('#curentHour-filter' + tabName).val());
                            jQuery(".autoroyal-rent-booking-dates-pickup-date .time").text(jQuery('#curentHour-filter' + tabName).val());
                            jQuery(".autoroyal-rent-booking-dates-return-date .time").text(jQuery('#curentHour-filter' + tabName).val());
                        }
                    });

                }

                if (jQuery("#dropHour" + tabName + "").exists() || jQuery("#dropHour-filter" + tabName + "").exists()) {

                    jQuery('#dropHour' + tabName + ', #dropHour-filter' + tabName + '').datetimepicker({
                        datepicker: false,
                        ampm: true,
                        format: 'g:i A',
                        hours24: true, //time format 24h/12h
                        scrollInput: false,
                        onSelectTime: function (current_time, $input) {
                            jQuery("#dropT" + tabName + ", .dropT" + tabName + "").text(jQuery('#dropHour' + tabName + '').val());
                            jQuery(".autoroyal-rent-booking-dates-return-date .time").text(jQuery('#dropHour-filter' + tabName + '').val());
                        }
                    });

                }


                var fmt = 'm/d/Y';

                if (jQuery("#curentDay" + tabName).exists()) {

                    // set days rent range
                    jQuery('#curentDay' + tabName).datetimepicker({
                        defaultDate: new Date(),
                        format: fmt,
                        formatDate: fmt,
                        timepicker: false,
                        onShow: function (ct) {
                            this.setOptions({
                                minDate: 0,
                            })
                        },
                        onSelectDate: function (ct, $i) {

                            var date = new Date(jQuery('#curentDay' + tabName).val());
                            var newdate = new Date(date);
                            newdate.setDate(newdate.getDate() + 3);
                            var dd = newdate.getDate();
                            var mm = newdate.getMonth() + 1;
                            var y = newdate.getFullYear();
                            var someFormattedDate = (mm < 10 ? '0' : '') + mm + '/' + (dd < 10 ? '0' : '') + dd + '/' + y;

                            var start = new Date(jQuery('#curentDay' + tabName).val());
                            var end = new Date(jQuery('#dropDay' + tabName + '').val());
                            if (!jQuery('#dropDay' + tabName).val() || start.getTime() >= end.getTime()) {
                                jQuery('#dropDay' + tabName).val(someFormattedDate);
                            }

                            days();
                            //diffDaysFilter();
                        }
                    });

                }
                /*
                    ' + tabName + '
                    " + tabName + "
                    * */
                if (jQuery("#curentDay-filter" + tabName + "").exists()) {

                    jQuery('#curentDay-filter' + tabName + '').datetimepicker({
                        defaultDate: new Date(),
                        format: fmt,
                        formatDate: fmt,
                        timepicker: false,
                        onShow: function (ct) {
                            this.setOptions({
                                minDate: 0,
                            })
                        },
                        onSelectDate: function (ct, $i) {
                            jQuery('.autoroyal-rent-booking-dates-pickup-date .date').text(jQuery('#curentDay-filter' + tabName + '').val());

                            var date = new Date(jQuery('#curentDay-filter' + tabName + '').val());
                            var newdate = new Date(date);
                            newdate.setDate(newdate.getDate() + 3);
                            var dd = newdate.getDate();
                            var mm = newdate.getMonth() + 1;
                            var y = newdate.getFullYear();
                            var someFormattedDate = (mm < 10 ? '0' : '') + mm + '/' + (dd < 10 ? '0' : '') + dd + '/' + y;

                            var start = new Date(jQuery('#curentDay-filter' + tabName + '').val());
                            var end = new Date(jQuery('#dropDay-filter' + tabName + '').val());
                            if (!jQuery('#dropDay-filter' + tabName + '').val() || start.getTime() >= end.getTime()) {
                                jQuery('#dropDay-filter' + tabName + '').val(someFormattedDate);
                                jQuery('.autoroyal-rent-booking-dates-return-date .date').text(someFormattedDate);
                                jQuery('#dropDay' + tabName + '').val(someFormattedDate);
                            }

                            jQuery('#curentDay' + tabName + '').datetimepicker({
                                setDate: jQuery('#curentDay-filter').val(),
                            });
                            jQuery('#curentDay' + tabName + '').val(jQuery('#curentDay-filter' + tabName + '').val());

                            days();
                        }
                    });

                }

                if (jQuery("#dropDay" + tabName + "").exists()) {
                    jQuery('#dropDay' + tabName + '').datetimepicker({
                        format: fmt,
                        formatDate: fmt,
                        timepicker: false,
                        startDate: '+1970/01/02',
                        onShow: function (ct) {
                            this.setOptions({
                                minDate: jQuery('#curentDay' + tabName + '').val() ? jQuery('#curentDay' + tabName + '').val() : false,
                            })
                        },
                        onSelectDate: function (ct, $i) {
                            days();
                        }
                    });
                }

                if (jQuery("#dropDay-filter" + tabName + "").exists()) {
                    jQuery('#dropDay-filter' + tabName + '').datetimepicker({
                        format: fmt,
                        formatDate: fmt,
                        timepicker: false,
                        startDate: '+1970/01/02',
                        onShow: function (ct) {
                            this.setOptions({
                                minDate: jQuery('#curentDay-filter' + tabName + '').val() ? jQuery('#curentDay-filter' + tabName + '').val() : false,
                            })
                        },
                        onSelectDate: function (ct, $i) {
                            jQuery('#dropDay' + tabName + '').val(jQuery('#dropDay-filter' + tabName + '').val());
                            jQuery('.autoroyal-rent-booking-dates-return-date .date').text(jQuery('#dropDay-filter' + tabName + '').val());
                            days();
                        }
                    });
                }

                function getAsDate(day, time) {

                    var hours = Number(time.match(/^(\d+)/)[1]);
                    var minutes = Number(time.match(/:(\d+)/)[1]);
                    var AMPM = time.match(/\s(.*)$/)[1];
                    if (AMPM == "pm" && hours < 12) hours = hours + 12;
                    if (AMPM == "am" && hours == 12) hours = hours - 12;
                    var sHours = hours.toString();
                    var sMinutes = minutes.toString();
                    if (hours < 10) sHours = "0" + sHours;
                    if (minutes < 10) sMinutes = "0" + sMinutes;
                    time = sHours + ":" + sMinutes + ":00";
                    var d = new Date(day);
                    var n = d.toISOString().substring(0, 10);
                    var newDate = new Date(n + "T" + time);
                    return newDate;

                }

                function days() {
                    var pickup_date = $("#curentDay").val(), //+ tabName Current day they have maintain in one place
                        pickup_time = "12:00 PM",
                        drop_date = $("#dropDay").val(), //+ tabName Current day they have maintain in one place
                        drop_time = "12:00 PM",
                        c = 24 * 60 * 60 * 1000;

                    var pickup = getAsDate(pickup_date, pickup_time),
                        drop = getAsDate(drop_date, drop_time),
                        diffDays = Math.round(Math.abs((drop - pickup) / (c)));

                    //console.log(diffDays);

                    jQuery('#reserv-car-days' + tabName + '').val(diffDays);
                    jQuery('.autoroyal-rent-booking-dates-rental-period .date').text(diffDays);

                    jQuery("#daysN" + tabName + ", .daysN").text(diffDays + " days");
                    jQuery("#startR" + tabName + ", .startR").text(jQuery('#curentDay' + tabName + '').val());
                    jQuery("#endR" + tabName + ", .endR").text(jQuery('#dropDay' + tabName + '').val());

                    var new_price = 0;
                    var total_sets = jQuery('#reservation-modal' + tabName + ' #car-price-sets' + tabName + ' .total-sets').val();

                    for (var n = 0; n <= total_sets; ++n) {
                        if (jQuery('#reservation-modal' + tabName + ' #car-price-sets' + tabName + ' #price-set-period-' + n + tabName).val() <= diffDays) {
                            new_price = jQuery('#reservation-modal' + tabName + ' #car-price-sets' + tabName + ' #price-set-price-' + n + tabName).val();
                        }
                    }

                    if (new_price == 0) {
                        new_price = jQuery('#dayP' + tabName + ' span').html();
                    }

                    jQuery('.reserv-car-price-day').val(new_price);

                    var days_num = diffDays;
                    var total_p = new_price * diffDays;
                    var reduced_p = new_price * diffDays;

                    jQuery("#dayP" + tabName + " span, .dayP span").text(new_price);
                    jQuery("#totDayP" + tabName + " span, .totDayP span").text(total_p);
                    jQuery("#reserv-car-price-total" + tabName + ", #reserv-car-price-without-extras" + tabName + "").val(total_p);
                    jQuery('#reserv-car-price-total' + tabName + '').val(total_p);

                }

                function diffDaysFilter() {
                    var pickup_date = $("#curentDay-filter" + tabName + "").val(),
                        pickup_time = "12:00 PM",
                        drop_date = $("#dropDay-filter" + tabName + "").val(),
                        drop_time = "12:00 PM",
                        c = 24 * 60 * 60 * 1000;
                    var pickup = getAsDate(pickup_date, pickup_time),
                        drop = getAsDate(drop_date, drop_time),
                        diffDays = Math.round(Math.abs((drop - pickup) / (c)));
                    jQuery('.autoroyal-rent-booking-dates-rental-period .date').text(diffDays);
                }

                if (jQuery(".autoroyal-rent-booking-dates").exists() || jQuery(".autoroyal-homepage-search-box-rent").exists() || jQuery("#reservation-modal" + tabName + "").exists()) {

                    var d = new Date();
                    var month = d.getMonth() + 1;
                    var day = d.getDate();
                    var output = (month < 10 ? '0' : '') + month + '/' + (day < 10 ? '0' : '') + day + '/' + d.getFullYear();

                    if (jQuery("#rent_pickup_date" + tabName + "").exists()) {
                        output = jQuery("#rent_pickup_date" + tabName + "").val();
                    }

                    jQuery("#curentDay" + tabName + "").val(output);
                    jQuery("#curentDay-filter" + tabName + "").val(output);

                    var newdate = new Date();
                    newdate.setDate(newdate.getDate() + 3);
                    var month = newdate.getMonth() + 1;
                    var day = newdate.getDate();
                    var output_new = (month < 10 ? '0' : '') + month + '/' + (day < 10 ? '0' : '') + day + '/' + newdate.getFullYear();

                    if (jQuery("#rent_drop_date" + tabName + "").exists()) {
                        output_new = jQuery("#rent_drop_date" + tabName).val();
                    }

                    jQuery("#dropDay" + tabName + "").val(output_new);
                    jQuery("#dropDay-filter" + tabName + "").val(output_new);

                    if (jQuery("#rent_pickup_time" + tabName + "").exists()) {
                        jQuery("#curentHour-filter" + tabName + "").val(jQuery("#rent_pickup_time" + tabName + "").val());
                        jQuery("#pickT" + tabName + ", .pickT").text(jQuery('#rent_pickup_time' + tabName + '').val());
                        jQuery('.autoroyal-rent-booking-dates-pickup-date .time').text(jQuery("#rent_pickup_time" + tabName + "").val());
                    } else {
                        jQuery("#curentHour-filter" + tabName + "").val("12:00 PM");
                        jQuery("#pickT" + tabName + ", .pickT").text("12:00 PM");
                        jQuery('.autoroyal-rent-booking-dates-pickup-date .time').text("12:00 PM");
                    }

                    if (jQuery("#rent_drop_time" + tabName + "").exists()) {
                        jQuery("#dropHour-filter" + tabName + "").val(jQuery("#rent_drop_time" + tabName + "").val());
                        jQuery("#dropT" + tabName + ", .dropT").text(jQuery("#rent_drop_time" + tabName + "").val());
                        jQuery('.autoroyal-rent-booking-dates-return-date .time').text(jQuery("#rent_drop_time" + tabName + "").val());
                    } else {
                        jQuery("#dropHour-filter" + tabName + "").val("12:00 PM");
                        jQuery("#dropT" + tabName + ", .dropT").text("12:00 PM");
                        jQuery('.autoroyal-rent-booking-dates-return-date .time').text("12:00 PM");
                    }

                    jQuery('.autoroyal-rent-booking-dates-pickup-date .date').text(jQuery('#curentDay-filter' + tabName + '').val());
                    jQuery('.autoroyal-rent-booking-dates-return-date .date').text(jQuery('#dropDay-filter' + tabName + '').val());

                    days();
                }

                if (jQuery(".autoroyal-progress-circle").exists()) {
                    jQuery(".autoroyal-progress-circle").on("inview", function (event, isInView) {
                        if (isInView) {
                            jQuery(this).addClass("animated");
                        }
                    });
                }
                ;


                if (jQuery(".autoroyal-progress-bar-progress").exists()) {
                    jQuery(".autoroyal-progress-bar-progress").on("inview", function (event, isInView) {
                        if (isInView) {
                            $(this).animate({
                                width: $(this).data("percent")
                            }, 700);
                        }
                    });
                }
                ;

                if (jQuery("#cd-item-slider" + tabName + "").exists() || jQuery("#cd-main-carousel" + tabName + "").exists() || jQuery("#rent-me" + tabName + "").exists()) {
                    jQuery("#cd-item-slider" + tabName + ", #cd-main-carousel" + tabName + ", #rent-me" + tabName + "").swipe({
                        swipe: function (event, direction, distance, duration, fingerCount, fingerData) {
                            if (direction == 'left') jQuery(this).carousel('next');
                            if (direction == 'right') jQuery(this).carousel('prev');
                        },
                        allowPageScroll: "vertical"
                    });

                }


            }


            /**
             * Google Map Custom Function by Redberyl IT
             * */

            var tmpLocation = '✈️Bandaranaike International Airport ';
            jQuery(function ($) {
                //initMap();
                initAutocomplete('start');
                initAutocomplete('end');
                init_form('self-drive');
                //initMap_transfers()
                initAutocompleteTransfers('transfer_start');
                initAutocompleteTransfers('transfer_end');


                $("#myTab a").click(function (e) {
                    e.preventDefault();
                    $(this).tab('show');

                    let target = (($(e.target).attr("href")).substr(1)).replace("_", "-") // activated tab
                    $("#currentTabName").val(target);
                    customSetup(target);
                    init_form(target);
                    if (target == 'airport-transfers') {
                        $("#map2").hide();
                        $("#map").show();
                        $("#map_output").html('');
                        $("#destination_from").click();
                        switch_airport_location();


                    } else if (target == 'transfers') {
                        $("#map2").show();
                        $("#map").hide();
                        $("#map_output").html('');
                        resetTransfer();
                    } else {
                        map_airport_transfer(false);
                    }

                });

                $(window).keydown(function (event) {
                    if (event.keyCode == 13) {
                        event.preventDefault();
                        focus_date(event.target.id);
                        return false;
                    }
                });


            });

            function focus_date(ElementID) {
                $("#" + ElementID).change();
                if ($("#currentTabName").val() == 'airport-transfers') {
                    $("#curentDay-filter-airport-transfers").focus();
                } else if ($("#currentTabName").val() == 'transfers') {
                    if ($("#transfer_start").val() != '' && $("#transfer_end").val() != '') {
                        $("#curentDay-filter-transfers").focus()
                    }
                }
            }


            function switch_airport_location(destination = 'from_airport') {
                if (destination === 'from_airport') {
                    $("#start").val(tmpLocation);
                    $("#start").prop('readonly', true);
                    $("#end").val('');
                    $("#end").prop('readonly', false);

                } else if ('to_airport') {
                    $("#end").val(tmpLocation);
                    $("#end").prop('readonly', true);
                    $("#start").val('');
                    $("#start").prop('readonly', false);
                }
                map_airport_transfer(false);
            }

            function map_airport_transfer(show = true) {
                if (show == true) {
                    $("#airport_transfers_map").show();
                    $("#content_description_home_page").hide();
                } else {
                    $("#airport_transfers_map").hide();
                    $("#content_description_home_page").show();
                }
            }

            function customSetup(tabName) {
                if (tabName == 'self-drive') {
                    tabName = '';
                } else {
                    tabName = '-' + tabName;
                }
                var d = new Date();
                var month = d.getMonth() + 1;
                var day = d.getDate();
                var output = (month < 10 ? '0' : '') + month + '/' + (day < 10 ? '0' : '') + day + '/' + d.getFullYear();

                if (jQuery("#rent_pickup_date" + tabName).exists()) {
                    output = jQuery("#rent_pickup_date" + tabName).val();
                }

                jQuery("#curentDay-filter" + tabName).val(output);
            }

            function resetTransfer() {
                $("#transfer_start").val('');
                $("#transfer_end").val('');
                $("#map_output_transfer").html('')
                map_airport_transfer(false);
            }

            /*Return Location Code */
            function clickReturnLocation(tmpThis) {
                console.log(tmpThis.checked);
                if (tmpThis.checked) {
                    $("#returnLocationInput").show();
                } else {
                    $("#returnLocationInput").hide();
                }
            }

            function filterPickupAndReturnLocationValues() {
                var tmpSelected = $("#rezerve-pickup-place-filter").val();
                if (tmpSelected != '') {
                    $("#rezerve-return-place-filter").html('');
                    var options = '<option value="0">Select location</option>';
                    $('#rezerve-pickup-place-filter > option').each(function () {
                        if (tmpSelected != $(this).val() || $(this).val() == 0) {
                            var tmpHTML = '<option value="' + $(this).val() + '">' + $(this).text() + '</option>'
                            options = options + tmpHTML;
                            console.log('Location Added: ' + options);
                        } else {
                            console.log('Location Remove: ' + $(this).text() + ' +++ ' + $(this).val());
                        }

                    });
                    $("#rezerve-return-place-filter").html(options);
                    $('#rezerve-return-place-filter').trigger("chosen:updated");


                }

            }


            /*----------------------------------------- Google MAP ----------------------------------- */


            function calculateAndDisplayRoute(directionsService, directionsRenderer) {
                setTimeout(function () {
                    directionsService.route(
                        {
                            origin: {query: document.getElementById('start').value},
                            destination: {query: document.getElementById('end').value},
                            travelMode: 'DRIVING'
                        },
                        function (response, status) {
                            map_airport_transfer(false);
                            if (status === 'OK') {
                                map_airport_transfer(); // show the map custom code
                                directionsRenderer.setDirections(response);
                                var time = response.routes[0].legs[0].duration.text;
                                var distance = response.routes[0].legs[0].distance.text;
                                $("#map_output").html(distance + '&nbsp;| &nbsp;' + time);
                                debugger;
                                $("#km_distance_airport").val(distance);
                            } else {
                                $("#map_output").html('');
                                debugger;
                                $("#km_distance_airport").val(0);
                            }
                        });
                }, 100);
            }


            /**
             * Auto Complete
             * */
            var autocomplete;

            function initAutocomplete(ElementID) {
                console.log(ElementID);
                autocomplete = new google.maps.places.Autocomplete(document.getElementById(ElementID), {
                    types: ['geocode'],
                    componentRestrictions: {country: "lk"}
                });
                /*autocomplete.addListener('place_changed', function () {
                    focus_date(ElementID)
                });*/
            }


            /**
             * Google Map for Transfers
             * */
            function initAutocompleteTransfers(ElementID) {
                autocomplete = new google.maps.places.Autocomplete(document.getElementById(ElementID), {
                    types: ['geocode'],
                    componentRestrictions: {country: "lk"}
                });
            }


            function calculateAndDisplayRoute_transfers(directionsService, directionsRenderer) {
                setTimeout(function () {
                    var startLocation = document.getElementById('transfer_start').value;
                    var endLocation = document.getElementById('transfer_end').value;
                    if (startLocation != '' && endLocation != '') {
                        directionsService.route(
                            {
                                origin: {query: startLocation},
                                destination: {query: endLocation},
                                travelMode: 'DRIVING'
                            },
                            function (response, status) {
                                if (status === 'OK') {
                                    map_airport_transfer(true);
                                    directionsRenderer.setDirections(response);
                                    var time = response.routes[0].legs[0].duration.text;
                                    var distance = response.routes[0].legs[0].distance.text;
                                    $("#map_output_transfer").html(distance + '&nbsp;| &nbsp;' + time);
                                } else {
                                    map_airport_transfer(false);
                                    $("#map_output_transfer").html('')
                                }
                            });
                    }
                }, 100);
                setTransferValues();


            }


            function transfer_return_location(tmpThis) {
                if ($(tmpThis).prop("checked") == true) {
                    $("#transfer_return_div").show();
                    setTransferValues();
                } else if ($(tmpThis).prop("checked") == false) {
                    $("#transfer_return_div").hide();
                }
            }

            function setTransferValues() {
                setTimeout(function () {
                    if ($("#transfer_differentReturnLocation").prop("checked") == true) {
                        $("#transfer_transfer_end").val($("#transfer_start").val());
                        $("#return-transfer_start").val($("#transfer_end").val());
                        $("#return-curentDay-filter-transfers").val($("#curentDay-filter-transfers").val());
                        $("#return-curentHour-filter-transfers").val($("#curentHour-filter-transfers").val());
                    }
                }, 100);
            }


            /*----------------------------------------- end  Google MAP ----------------------------------- */
        </script>

        <?php ;

        return $html;


    }


}






