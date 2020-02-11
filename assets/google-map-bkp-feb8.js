function initMap() {

    var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 7.1802, lng: 79.8843},
        zoom: 10,
        mapTypeId: 'roadmap'
    });
    var options = {
        componentRestrictions: {country: 'lk'}
    };

    var directionsService = new google.maps.DirectionsService();
    var directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);

    var input = document.getElementById('start');
    var input2 = document.getElementById('end');

    new google.maps.places.Autocomplete(input, options);
    new google.maps.places.Autocomplete(input2, options);


    var onChangeHandler = function () {
        calculateAndDisplayRoute(directionsService, directionsRenderer);
    };
    input.addEventListener('change', onChangeHandler);
    input2.addEventListener('change', onChangeHandler);
}


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


function initMap_transfers() {
    var map = new google.maps.Map(document.getElementById('map2'), {
        center: {lat: 7.1802, lng: 79.8843},
        zoom: 10,
        mapTypeId: 'roadmap'
    });
    var options = {
        componentRestrictions: {country: 'lk'}
    };

    var input = document.getElementById('transfer_start');
    var input2 = document.getElementById('transfer_end');
    new google.maps.places.Autocomplete(input, options);
    new google.maps.places.Autocomplete(input2, options);

    var directionsRenderer = new google.maps.DirectionsRenderer();
    var directionsService = new google.maps.DirectionsService();
    directionsRenderer.setMap(map);

    var onChangeHandler = function () {
        calculateAndDisplayRoute_transfers(directionsService, directionsRenderer);
    };
    document.getElementById('transfer_end').addEventListener('change', onChangeHandler);
    document.getElementById('transfer_start').addEventListener('change', onChangeHandler);

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
        setTransferValues();
    }, 100);


}

