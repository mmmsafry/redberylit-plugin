<?php

namespace Inc\Base;


class ShortCode extends BaseController
{
    public function register()
    {
        add_shortcode('rb_front_end_form', [$this, 'getFormShortCode']);
        add_shortcode('base_url', [$this, 'base_url_redberylit']);
        add_shortcode('base_url', [$this, 'base_url_redberylit']);

    }

    function getFormShortCode()
    {
        $html = "" ?>
        <style>
            .search-form-wrapper {
                padding: 10px;
            }

            .chosen-container {
                border: 1px solid #eeeeee;
                padding: 6px;
                border-radius: 4px;
            }

            .autoroyal-homepage-search-box ul, ol, dl {
                line-height: 30px;
                font-size: 13px;
            }

            .autoroyal-homepage-search-box .nav-tabs .nav-link {
                min-height: 50px;
            }

            .chosen-container-single .chosen-single span {
                font-size: 14px;
            }

            .chosen-container .chosen-results li {
                font-size: 13px;
            }

            input {
                font-size: 14px;
            }

            #autoroyal-inventory #autoroyal-advance-search-form input[type="text"] {
                padding: 9px;
            }

            .autoroyal-rent-booking-dates-update {
                padding: 15px;
            }
        </style>
        <div class="autoroyal-homepage-search-box autoroyal-homepage-search-box-rent autoroyal_search_5e26c760247f2">

            <ul class="nav nav-tabs p-0" id="myTab" role="tablist">
                <li class="nav-item col">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#self_drive"
                       role="tab" aria-controls="selfDrive"
                       aria-selected="false">
                        Self Drive </a>
                </li>
                <li class="nav-item col">
                    <a class="nav-link" id="with-driver-tab" data-toggle="tab"
                       href="#with_driver" role="tab"
                       aria-controls="withDriver" aria-selected="false">
                        With Driver </a>
                </li>
                <li class="nav-item col">
                    <a class="nav-link" id="contact-tab" data-toggle="tab"
                       href="#airport_transfers" role="tab"
                       aria-controls="contact" aria-selected="true">
                        Airport Hire </a>
                </li>
                <li class="nav-item col">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#transfers"
                       role="tab" aria-controls="contact"
                       aria-selected="false">
                        Taxi Transfers </a>
                </li>
                <li class="nav-item col">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#wedding"
                       role="tab" aria-controls="contact"
                       aria-selected="false">
                        Weddings </a>
                </li>

            </ul>
            <div class="tab-content" id="myTabContent">
                <input type="hidden" id="currentTabName" value="airport-transfers">
                <!-- ------------------------------------------- self_drive ---------------------------------------------- -->
                <div class="tab-pane fade active show" id="self_drive" role="tabpanel"
                     aria-labelledby="self-drive-tab">
                    <form id="autoroyal-advance-search-form"
                          action="http://localhost/casons/rent/" method="GET">

                        <input type="hidden" value="self_drive" name="form_source">


                        <div class="search-form-wrapper">

                            <div class="row">

                                <div class="col-md-6 mb-2 ">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                            <div class="title">Pick-up location</div>
                                            <select id="rezerve-pickup-place-filter"
                                                    name="rezerve-pickup-place-filter"
                                                    class="chosen-select" autocomplete="off"
                                                    style="display: none;">
                                                <option value="0">Select location</option>
                                                <option value="Bandaranaike International Airport"> Bandaranaike
                                                    International Airport
                                                </option>
                                                <option value="Casons Head Office">Casons Head Office</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 right-separator">

                                    <div class="title">Vehicle Type</div>
                                    <select id="category" name="category"
                                            class="chosen-select"
                                            autocomplete="off"
                                            style="display: none;">
                                        <option value="0">Vehicle Type</option>
                                        <option value="1,2" selected="">Cars
                                            &amp; SUVs
                                        </option>

                                        <option value="1">Cars</option>
                                        <option value="2">SUV / Cabs</option>
                                        <option value="3">Vans / Buses</option>
                                        <option value="4">Trucks/Lorries
                                        </option>
                                        <option value="5"> Motorbikes</option>
                                        <option value="6">Tuk Tuks</option>
                                    </select>


                                </div>


                                <div class="row">

                                    <div class="col-lg-12 col-md-12">

                                        <div class="row">

                                            <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">

                                                <div class="autoroyal-rent-filter-date">

                                                    <div class="title">Pick-up date</div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text"
                                                               name="rezerve-pickup-date"
                                                               placeholder="Choose a date"
                                                               class="daySelect"
                                                               id="curentDay-filter"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title">Pick-up time</div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text"
                                                               name="rezerve-pickup-time"
                                                               placeholder="12:00 AM"
                                                               id="curentHour-filter"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-lg-12 col-md-12 right-separator">

                                                <div class="autoroyal-rent-filter-date">

                                                    <div class="title">Return date</div>
                                                    <div class="form-group input-append mt-2">
                                                        <input type="text"
                                                               name="rezerve-drop-date"
                                                               placeholder="Choose a date"
                                                               class="daySelect"
                                                               id="dropDay-filter"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                                <div class="autoroyal-rent-filter-time">

                                                    <div class="title">Return time</div>
                                                    <div class="form-group input-append timepick mt-2">
                                                        <input type="text"
                                                               name="rezerve-drop-time"
                                                               placeholder="12:00 AM"
                                                               id="dropHour-filter"
                                                               autocomplete="off">
                                                    </div>

                                                </div>

                                            </div>


                                        </div>

                                    </div>

                                    <div class="col-lg-2 col-md-2 mt-2 offset-10">

                                        <button type="submit"
                                                class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn ">
                                            Search
                                        </button>

                                    </div>

                                </div>


                            </div>

                        </div>

                    </form>
                </div>

                <!-- ------------------------------------------- with_driver ---------------------------------------------- -->
                <div class="tab-pane fade" id="with_driver" role="tabpanel"
                     aria-labelledby="with-driver-tab">
                    <form id="autoroyal-advance-search-form_with_driver"
                          action="http://localhost/casons/rent/" method="GET">

                        <input type="hidden" value="with_driver" name="form_source">
                        <div class="search-form-wrapper">

                            <div class="row">

                                <div class="col-md-12 mb-2 ">

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">

                                            <div class="title">Pick-up location</div>
                                            <select id="rezerve-pickup-place-filter-with_driver"
                                                    name="rezerve-pickup-place-filter"
                                                    class="chosen-select" autocomplete="off"
                                                    style="display: none;">
                                                <option value="0">Select location</option>
                                                <option value="Bandaranaike International Airport">
                                                    Bandaranaike International
                                                    Airport
                                                </option>
                                                <option value="Casons Head Office">Casons Head
                                                    Office
                                                </option>
                                            </select>
                                            <div class="chosen-container chosen-container-single chosen-container-single-nosearch"
                                                 title=""
                                                 id="rezerve_pickup_place_filter_with_driver_chosen"
                                                 style="width: 0px;"><a class="chosen-single">
                                                    <span>Select location</span>
                                                    <div>
                                                        <i class="material-icons">expand_more</i>
                                                    </div>
                                                </a>
                                                <div class="chosen-drop">
                                                    <div class="chosen-search">
                                                        <input class="chosen-search-input"
                                                               type="text" autocomplete="off"
                                                               readonly="">
                                                    </div>
                                                    <ul class="chosen-results"></ul>
                                                </div>
                                            </div>

                                        </div>


                                    </div>

                                </div>

                                <div class="col-md-12 ">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                                    <div class="autoroyal-rent-filter-date">

                                                        <div class="title">Pick-up date</div>
                                                        <div class="form-group input-append mt-2">
                                                            <input type="text"
                                                                   name="rezerve-pickup-date"
                                                                   placeholder="Choose a date"
                                                                   class="daySelect"
                                                                   id="curentDay-filter-with-driver"
                                                                   autocomplete="off">
                                                        </div>

                                                    </div>

                                                    <div class="autoroyal-rent-filter-time">

                                                        <div class="title">Pick-up time</div>
                                                        <div class="form-group input-append timepick mt-2">
                                                            <input type="text"
                                                                   name="rezerve-pickup-time"
                                                                   placeholder="12:00 AM"
                                                                   id="curentHour-filter-with-driver"
                                                                   autocomplete="off">
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="col-lg-12 col-md-12 right-separator">

                                                    <div class="autoroyal-rent-filter-date">

                                                        <div class="title">Return date</div>
                                                        <div class="form-group input-append mt-2">
                                                            <input type="text"
                                                                   name="rezerve-drop-date"
                                                                   placeholder="Choose a date"
                                                                   class="daySelect"
                                                                   id="dropDay-filter-with-driver"
                                                                   value="01/07/20120"
                                                                   autocomplete="off">
                                                        </div>

                                                    </div>

                                                    <div class="autoroyal-rent-filter-time">

                                                        <div class="title">Return time</div>
                                                        <div class="form-group input-append timepick mt-2">
                                                            <input type="text"
                                                                   name="rezerve-drop-time"
                                                                   placeholder="12:00 AM"
                                                                   id="dropHour-filter-with-driver"
                                                                   autocomplete="off">
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="col-lg-12 col-md-12 right-separator">

                                                    <div class="title">Vehicle Type</div>
                                                    <select id="category-with-drive"
                                                            name="category"
                                                            class="chosen-select"
                                                            autocomplete="off"
                                                            style="display: none;">
                                                        <option value="0">Vehicle Type</option>
                                                        <option value="1,2" selected="">Cars
                                                            &amp; SUVs
                                                        </option>

                                                        <option value="1">Cars</option>
                                                        <option value="2">SUV / Cabs</option>
                                                        <option value="3">Vans / Buses</option>
                                                        <option value="4">Trucks/Lorries
                                                        </option>
                                                        <option value="5"> Motorbikes</option>
                                                        <option value="6">Tuk Tuks</option>
                                                    </select>
                                                    <div class="chosen-container chosen-container-single chosen-container-single-nosearch"
                                                         title=""
                                                         id="category_with_drive_chosen"
                                                         style="width: 0px;"><a
                                                                class="chosen-single">
                                                            <span>Cars &amp; SUVs</span>
                                                            <div><i class="material-icons">expand_more</i>
                                                            </div>
                                                        </a>
                                                        <div class="chosen-drop">
                                                            <div class="chosen-search">
                                                                <input class="chosen-search-input"
                                                                       type="text"
                                                                       autocomplete="off"
                                                                       readonly="">
                                                            </div>
                                                            <ul class="chosen-results"></ul>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="col-lg-3 col-md-4 mt-2">

                                            <button type="submit"
                                                    class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn">
                                                Search
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </form>
                </div>

                <!-- ------------------------------------------- airport_transfers ---------------------------------------------- -->
                <div class="tab-pane fade" id="airport_transfers" role="tabpanel"
                     aria-labelledby="contact-tab">
                    <form id="autoroyal-advance-search-form_airport_transfers"
                          action="http://localhost/casons/rent/"
                          method="GET">
                        <input type="hidden" value="airport_transfers" name="form_source">

                        <div class="search-form-wrapper">
                            <div class="row">
                                <div class="col-md-12 ">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                            <div class="btn-block btn-group btn-group-toggle"
                                                 data-toggle="buttons">
                                                <label class="btn btn-secondary active">
                                                    <input type="radio" name="destination"
                                                           id="destination_from"
                                                           onchange="switch_airport_location('from_airport')"
                                                           autocomplete="off"
                                                           checked=""> From Airport
                                                </label>

                                                <label class="btn btn-secondary">
                                                    <input type="radio" name="destination"
                                                           id="destination_to"
                                                           onchange="switch_airport_location('to_airport')"
                                                           autocomplete="off">
                                                    To Airport
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12 mb-2 ">

                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">

                                            <div class="title">Pick-up location</div>
                                            <input type="text" name="pick-up-location" value=""
                                                   id="start"
                                                   placeholder="Enter a location"
                                                   autocomplete="off" readonly="">


                                        </div>

                                        <div class="col-lg-12 col-md-12 right-separator">
                                            <div class="title">Drop Off location</div>
                                            <input type="text" name="drop-off-location" value=""
                                                   id="end"
                                                   placeholder="Enter a location"
                                                   autocomplete="off">

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-12 ">

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12">

                                            <div class="row">

                                                <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">

                                                    <div class="autoroyal-rent-filter-date">

                                                        <div class="title">Pick-up date</div>
                                                        <div class="form-group input-append mt-2">
                                                            <input type="text"
                                                                   name="rezerve-pickup-date"
                                                                   placeholder="Choose a date"
                                                                   class="daySelect"
                                                                   id="curentDay-filter-airport-transfers"
                                                                   autocomplete="off">
                                                        </div>

                                                    </div>

                                                    <div class="autoroyal-rent-filter-time">

                                                        <div class="title">Pick-up time</div>
                                                        <div class="form-group input-append timepick mt-2">
                                                            <input type="text"
                                                                   name="rezerve-pickup-time"
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
                                                    class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn">
                                                Search
                                            </button>
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
                <div class="tab-pane fade" id="transfers" role="tabpanel"
                     aria-labelledby="contact-tab">
                    <form id="autoroyal-advance-search-form_transfers"
                          action="http://localhost/casons/rent/" method="GET">
                        <input type="hidden" value="transfers" name="form_source">

                        <div class="search-form-wrapper">
                            <div class="row">
                                <div class="col-md-12 mb-2 ">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                            <div class="title">Pick-up location</div>
                                            <input type="text" name="pick-up-location" value=""
                                                   id="transfer_start"
                                                   placeholder="Enter a location"
                                                   autocomplete="off">
                                        </div>

                                        <div class="col-lg-12 col-md-12 right-separator">
                                            <div class="title">Return location</div>
                                            <input type="text" name="pick-up-location" value=""
                                                   id="transfer_end"
                                                   placeholder="Enter a location"
                                                   autocomplete="off">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 ">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                                    <div class="autoroyal-rent-filter-date">
                                                        <div class="title">Pick-up date</div>
                                                        <div class="form-group input-append mt-2">
                                                            <input type="text"
                                                                   name="rezerve-pickup-date"
                                                                   placeholder="Choose a date"
                                                                   class="daySelect"
                                                                   id="curentDay-filter-transfers"
                                                                   autocomplete="off">
                                                        </div>
                                                    </div>

                                                    <div class="autoroyal-rent-filter-time">

                                                        <div class="title">Pick-up time</div>
                                                        <div class="form-group input-append timepick mt-2">
                                                            <input type="text"
                                                                   name="rezerve-pickup-time"
                                                                   placeholder="12:00 AM"
                                                                   id="curentHour-filter-transfers"
                                                                   autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-4 mt-2">
                                            <button type="submit"
                                                    class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn">
                                                Search
                                            </button>

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
                <div class="tab-pane fade" id="wedding" role="tabpanel"
                     aria-labelledby="contact-tab">
                    <form id="autoroyal-advance-search-form_wedding"
                          action="http://localhost/casons/rent/" method="GET">

                        <input type="hidden" value="wedding" name="form_source">
                        <div class="search-form-wrapper">
                            <div class="row">
                                <div class="col-md-12 mb-2 ">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                            <div class="title">Pick-up location</div>
                                            <select id="rezerve-pickup-place-filter-wedding"
                                                    name="rezerve-pickup-place-filter"
                                                    class="chosen-select" autocomplete="off"
                                                    style="display: none;">
                                                <option value="0">Select location</option>
                                                <option value="Bandaranaike International Airport">
                                                    Bandaranaike International
                                                    Airport
                                                </option>
                                                <option value="Casons Head Office">Casons Head
                                                    Office
                                                </option>
                                            </select>
                                            <div class="chosen-container chosen-container-single chosen-container-single-nosearch"
                                                 title=""
                                                 id="rezerve_pickup_place_filter_wedding_chosen"
                                                 style="width: 0px;"><a
                                                        class="chosen-single">
                                                    <span>Select location</span>
                                                    <div>
                                                        <i class="material-icons">expand_more</i>
                                                    </div>
                                                </a>
                                                <div class="chosen-drop">
                                                    <div class="chosen-search">
                                                        <input class="chosen-search-input"
                                                               type="text" autocomplete="off"
                                                               readonly="">
                                                    </div>
                                                    <ul class="chosen-results"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 ">
                                    <div class="row">


                                        <div class="col-lg-12 col-md-12">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 right-separator sm-mb-30 md-mb-30">
                                                    <div class="autoroyal-rent-filter-date">
                                                        <div class="title">Pick-up date</div>
                                                        <div class="form-group input-append mt-2">
                                                            <input type="text"
                                                                   name="rezerve-pickup-date"
                                                                   placeholder="Choose a date"
                                                                   class="daySelect"
                                                                   id="curentDay-filter-wedding"
                                                                   autocomplete="off">
                                                        </div>
                                                    </div>

                                                    <div class="autoroyal-rent-filter-time">
                                                        <div class="title">Pick-up time</div>
                                                        <div class="form-group input-append timepick mt-2">
                                                            <input type="text"
                                                                   name="rezerve-pickup-time"
                                                                   placeholder="12:00 AM"
                                                                   id="curentHour-filter-wedding"
                                                                   autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 right-separator">

                                            <div class="title">Vehicle Type</div>
                                            <select id="category-wedding" name="category"
                                                    class="chosen-select"
                                                    autocomplete="off" style="display: none;">
                                                <option value="0">Vehicle Type</option>
                                                <option value="1">Cars</option>
                                                <option value="2">SUV / Cabs</option>
                                            </select>
                                            <div class="chosen-container chosen-container-single chosen-container-single-nosearch"
                                                 title="" id="category_wedding_chosen"
                                                 style="width: 0px;"><a
                                                        class="chosen-single">
                                                    <span>Vehicle Type</span>
                                                    <div>
                                                        <i class="material-icons">expand_more</i>
                                                    </div>
                                                </a>
                                                <div class="chosen-drop">
                                                    <div class="chosen-search">
                                                        <input class="chosen-search-input"
                                                               type="text" autocomplete="off"
                                                               readonly="">
                                                    </div>
                                                    <ul class="chosen-results"></ul>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="col-lg-3 col-md-4 mt-2">

                                            <button type="submit"
                                                    class="btn btn-default autoroyal-homepage-filter-button mt-2 casons-search-btn">
                                                Search
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </form>
                </div>


            </div>


        </div>

        <?php ;

        return $html;


    }

    function base_url_redberylit()
    {
        return get_site_url();
    }

    function search_result($cars){

    }


}






