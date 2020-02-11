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
    initMap();
    initAutocomplete('start');
    initAutocomplete('end');
    init_form('self-drive');
    initMap_transfers()
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
            $("#return-transfer_end").val($("#transfer_start").val());
            $("#return-transfer_start").val($("#transfer_end").val());
            $("#return-curentDay-filter-transfers").val($("#curentDay-filter-transfers").val());
            $("#return-curentHour-filter-transfers").val($("#curentHour-filter-transfers").val());
        }
    }, 100);
}


