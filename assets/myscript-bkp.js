function init_form(tabName) {
    let fmt = 'm/d/Y';

    if (tabName == 'self-drive') {
        tabName = '';
    } else {
        tabName = '-' + tabName;
    }
    jQuery.fn.exists = function () {
        return this.length > 0;
    }
    if (jQuery("#curentDay-filter" + tabName).exists()) {

        jQuery('#curentDay-filter' + tabName).datetimepicker({
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

                jQuery('.autoroyal-rent-booking-dates-pickup-date .date').text(jQuery('#curentDay-filter' + tabName).val());

                let date = new Date(jQuery('#curentDay-filter' + tabName).val());
                let newdate = new Date(date);
                newdate.setDate(newdate.getDate() + 3);
                let dd = newdate.getDate();
                let mm = newdate.getMonth() + 1;
                let y = newdate.getFullYear();
                let someFormattedDate = (mm < 10 ? '0' : '') + mm + '/' + (dd < 10 ? '0' : '') + dd + '/' + y;

                let start = new Date(jQuery('#curentDay-filter' + tabName).val());
                let end = new Date(jQuery('#dropDay-filter' + tabName).val());
                if (!jQuery('#dropDay-filter' + tabName).val() || start.getTime() >= end.getTime()) {
                    jQuery('#dropDay-filter' + tabName).val(someFormattedDate);
                    jQuery('.autoroyal-rent-booking-dates-return-date .date').text(someFormattedDate);
                    jQuery('#dropDay').val(someFormattedDate);
                }

                jQuery('#curentDay').datetimepicker({
                    setDate: jQuery('#curentDay-filter' + tabName).val(),
                });
                jQuery('#curentDay').val(jQuery('#curentDay-filter' + tabName).val());
                //console.log( jQuery('#curentDay').val() );

                days();
                //diffDaysFilter();
                autoroyal_update_rent_car_price();
            }
        });

    }

    function days() {

        let pickup_date = $("#curentDay").val(),
            pickup_time = "12:00 PM",
            drop_date = $("#dropDay").val(),
            drop_time = "12:00 PM",
            c = 24 * 60 * 60 * 1000;

        let pickup = getAsDate(pickup_date, pickup_time),
            drop = getAsDate(drop_date, drop_time),
            diffDays = Math.round(Math.abs((drop - pickup) / (c)));

        jQuery('.autoroyal-rent-booking-dates-rental-period .date').text(diffDays);

        jQuery("#daysN, .daysN").text(diffDays + " days");
        jQuery("#startR, .startR").text(jQuery('#curentDay').val());
        jQuery("#endR, .endR").text(jQuery('#dropDay').val());

        let new_price = 0;
        let total_sets = jQuery('#reservation-modal #car-price-sets .total-sets').val();

        for (let n = 0; n <= total_sets; ++n) {
            if (jQuery('#reservation-modal #car-price-sets #price-set-period-' + n).val() <= diffDays) {
                new_price = jQuery('#reservation-modal #car-price-sets #price-set-price-' + n).val();
            }
        }

        if (new_price == 0) {
            new_price = jQuery('#dayP span').html();
        }

        jQuery('.reserv-car-price-day').val(new_price);

        //console.log(new_price);

        // output calculate costs
        let days_num = diffDays;
        let total_p = new_price * diffDays;
        let reduced_p = new_price * diffDays;

        jQuery("#dayP span, .dayP span").text(new_price);

        jQuery("#totDayP span, .totDayP span").text(total_p);
        jQuery("#reserv-car-price-total, #reserv-car-price-without-extras").val(total_p);
        update_extras_total_price();

        jQuery('#reserv-car-price-total').val(total_p);
    }

}

jQuery(function ($) {
    $("#myTab a").click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        let target = (($(e.target).attr("href")).substr(1)).replace("_", "-") // activated tab
        init_form(target);
    });


});



