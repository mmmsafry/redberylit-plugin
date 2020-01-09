function initMap() {
    var directionsService = new google.maps.DirectionsService();
    var directionsRenderer = new google.maps.DirectionsRenderer();
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 10,
        center: {lat: 7.1802, lng: 79.8843}
    });
    directionsRenderer.setMap(map);

    var onChangeHandler = function () {
        calculateAndDisplayRoute(directionsService, directionsRenderer);
    };
    document.getElementById('start').addEventListener('change', onChangeHandler);
    document.getElementById('end').addEventListener('change', onChangeHandler);
}


function calculateAndDisplayRoute(directionsService, directionsRenderer) {
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
            } else {
                $("#map_output").html('')
            }
        });
}


/**
 * Auto Complete
 * */
var autocomplete;

function initAutocomplete(ElementID) {
    autocomplete = new google.maps.places.Autocomplete(document.getElementById(ElementID), {
        types: ['geocode'], componentRestrictions: {country: "lk"}
    });
    autocomplete.addListener('place_changedXX', function () {
        focus_date(ElementID)
    });
}


/**
 * Google Map for Transfers
 * */
function initAutocompleteTransfers(ElementID) {
    autocomplete = new google.maps.places.Autocomplete(document.getElementById(ElementID), {
        types: ['geocode'], componentRestrictions: {country: "lk"}
    });
    autocomplete.addListener('place_changed', function () {
        focus_date(ElementID)
    });
}


function initMap_transfers() {
    var directionsService = new google.maps.DirectionsService();
    var directionsRenderer = new google.maps.DirectionsRenderer();
    var map = new google.maps.Map(document.getElementById('map2'), {
        zoom: 10,
        center: {lat: 7.1802, lng: 79.8843}
    });
    directionsRenderer.setMap(map);

    var onChangeHandler = function () {
        calculateAndDisplayRoute_transfers(directionsService, directionsRenderer);
    };

    document.getElementById('transfer_start').addEventListener('change', onChangeHandler);
    document.getElementById('transfer_end').addEventListener('change', onChangeHandler);
}

function calculateAndDisplayRoute_transfers(directionsService, directionsRenderer) {
    directionsService.route(
        {
            origin: {query: document.getElementById('transfer_start').value},
            destination: {query: document.getElementById('transfer_end').value},
            travelMode: 'DRIVING'
        },
        function (response, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
                var time = response.routes[0].legs[0].duration.text;
                var distance = response.routes[0].legs[0].distance.text;
                $("#map_output_transfer").html(distance + '&nbsp;| &nbsp;' + time);
                console.log('OK: map from airport transfer');
                console.log('start ' + document.getElementById('transfer_start').value);
                console.log('end ' + document.getElementById('transfer_end').value)
            } else {
                $("#map_output_transfer").html('')
                console.log('map from airport transfer');
                console.log('Directions request failed due to ' + status);
                console.log('E: map from airport transfer');
                console.log('start ' + document.getElementById('transfer_start').value);
                console.log('end ' + document.getElementById('transfer_end').value)

            }
        });
}

