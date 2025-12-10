$(document).ready(function () {
    GMaps.createMapForIFrame($('#autocomplete-address'), true);
});

var GMaps = (function () {

    var $map;
    var $item_map;
    var bounds;
    var markers;
    var circle = null;
    var hide_markers_route = [];
    var last_center = null;
    var last_zoom = null;
    var max_zoom = 16;
    var zoom_country = 5;
    var styles = [
        {
            featureType: "poi",
            elementType: "labels",
            stylers: [
                {visibility: "off"}
            ]
        },
        {
            "featureType": "transit.station",
            "elementType": "all",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        },
        {
            "featureType": "road.highway",
            "elementType": "labels",
            "stylers": [
                {
                    "visibility": "off"
                }
            ]
        }
    ];
    var marker_center = null;
    var styledMap = new google.maps.StyledMapType(styles, {name: "Styled Map"});

    var createMap = function ($item, latitude, longitude, zoom) {
        $item_map = $item;

        zoom = zoom || zoom_country;

        var center = latlng();

        var options = {
            zoom: zoom,
            center: center,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        $map = new google.maps.Map($item[0], options);

        $map.mapTypes.set('map_style', styledMap);
        $map.setMapTypeId('map_style');

        bounds = new google.maps.LatLngBounds();

        markers = [];

        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer({
            suppressMarkers: true
        });
        directionsDisplay.setMap($map);

        GMaps.geolocate($map);
    };

    var geoLocate = function ($map, garages) {
        markers = [];
        garages.forEach(function (point) {
            if (point.Garage != undefined) {
                if ((Number(point.Garage.latitude != 0) && (Number(point.Garage.longitude) != 0))) {
                    var latLng = new google.maps.LatLng(Number(point.Garage.latitude), Number(point.Garage.longitude));
                    var routes_tmp = [];
                    if(point[0]['routes'] != null){
                        routes_tmp = point[0]['routes'].split(',');
                    }
                    var contacts_tmp = [];
                    if(point[0]['contacts'] != null){
                        contacts_tmp = point[0]['contacts'].split(',');
                    }
                    var marker = new google.maps.Marker({
                        id: point.Garage.id,
                        position: latLng,
                        map: $map,
                        title: point.Garage.business_name,
                        icon: '/img/pins/visits/Pin_' + point.Garage.last_visit + '.png',
                        visible: false,
                        last_visit: point.Garage.last_visit,
                        city: point.Garage.town,
                        networks: point.GarageNetwork.network_id,
                        networks_status: point.GarageNetwork.status,
                        distributors: point.GarageDistributor.distributor_id,
                        routes: routes_tmp,
                        in_route: false,
                        contacts: contacts_tmp
                    });

                    markers.push(marker);

                    setInfoWindow(point, marker);
                }
            }
        });

        $('#show_result').on('click', function () {
            removeMarkers();
            search();
            if (bounds.getCenter().lat() == 0 && bounds.getCenter().lng() == -180) {
                $map.setCenter(new google.maps.LatLng($('#latitude-center').data('lat'), $('#longitude-center').data('lng')));
            }
        });

        $('#last_position').on('change',function(){
            if($(this).prop('checked') == true){
                last_center = $map.getCenter();
                last_zoom = $map.getZoom();
            } else {
                last_center = null;
                last_zoom = null;
            }
        });
    };

    var search = function(){
        var limit = 0;
        var filters = {
            last_visit: false,
            client_type: false,
            distance: false,
            town: false,
            route: false,
            contact: false,
            network: false,
            network_status: false,
            distributor: false
        };
        var $address = $('#autocomplete-address').val();
        var $last_visit = $('#search_last_visit').val();
        var $client_type = $('#search_client_type').val();
        var $route = $('#search_route').val();
        var $town = $('#search_city').val();
        var $my_customers = $('#search_my_customers').prop('checked');
        var $distributor = $('#search_distributor').val();
        var $network = $('#search_network').val();
        var $contact = $('#search_contact').val();
        var $network_status = $('#search_network_status').val();
        var $aag_region_id = $('#aag_region_id').val();
        var $aag_region_uk = $('#aag_region_id_uk').val();

        if($address  != ''){
            var lat_center;
            var lng_center;
            if(autocomplete.getPlace() != undefined && autocomplete.getPlace().formatted_address == $('#autocomplete-address').val()){
                lat_center = autocomplete.getPlace().geometry.location.lat();
                lng_center = autocomplete.getPlace().geometry.location.lng();
            } else {
                lat_center = $('#latitude-localization').val();
                lng_center = $('#longitude-localization').val();
            }
            createMarker(lat_center, lng_center, null, false);

            markers.forEach(function(marker){
                var distance = getDistanceFromLatLonInKm(marker.position.lat(),marker.position.lng(), lat_center, lng_center );
                if($aag_region_id == $aag_region_uk){
                    distance = distance * 0.621371;
                }
                marker.distance = false;
                if(distance < Number($('#search_distance').val())) {
                    marker.distance = true;
                }
            });
            filters.distance = true;
        }
        if($last_visit != '' && $last_visit != null && $last_visit != undefined){
            filters.last_visit = $last_visit;
        }
        // if($client_type != '' && $client_type != null && $client_type != undefined){
        //     filters.client_type = $client_type;
        // }
        if($town != '' && $town != null && $town != undefined){
            markers.forEach(function (marker) {
                if (marker.city != null) {
                    var reg_exp = new RegExp($town.toLowerCase() + '.*');
                    var city_marker = marker.city.toLowerCase();
                    if(city_marker.match(reg_exp)){
                        marker.town = true;
                    } else {
                        marker.town = false;
                    }
                } else {
                    marker.town = false;
                }
            });
            filters.town = true;
        }
        if($route != '' && $route != null && $route != undefined){
            markers.forEach(function(marker){
                if(typeof marker.routes != "undefined"){
                    marker.route = false;
                    marker.routes.forEach(function(route_id){
                        if (route_id == $('#search_route').val()) {
                            marker.route = true;
                        }
                    });
                }
            });
            filters.route = true;
        }
        if($my_customers == true){
            markers.forEach(function (marker) {
                marker.contact = false;
                marker.contacts.forEach(function(contact_id){
                    if (contact_id == $('#contact_id').val()) {
                        marker.contact = true;
                    }
                })
            });
            filters.contact = true;
        }
        if($distributor != '' && $distributor != null && $distributor != undefined) {
            markers.forEach(function (marker) {
                if (typeof marker.distributors != "undefined") {
                    if ($distributor.length > 1) {
                        marker.distributor = false;
                        $distributor.forEach(function (item) {
                            if (marker.distributors == item) {
                                marker.distributor = true;
                            }
                        });
                    } else {
                        marker.distributor = false;
                        if (marker.distributors == $distributor) {
                            marker.distributor = true;
                        }
                    }
                }
            });
            filters.distributor = true;
        }
        if($network_status != '' && $network_status != null && $network_status != undefined){
            markers.forEach(function (marker) {
                if (typeof marker.networks_status != "undefined") {
                    marker.network_status = false;
                    if (marker.networks_status == $network_status) {
                        marker.network_status = true;
                    }
                }
            });
            filters.network_status = true;
        }

        if($network != '' && $network != null && $network != undefined){
            markers.forEach(function (marker) {
                if (typeof marker.networks != "undefined") {
                    if ($network.length > 1) {
                        marker.network = false;
                        $network.forEach(function (item) {
                            if (marker.networks == item) {
                                marker.network = true;
                            }
                        });
                    } else {
                        marker.network = false;
                        if (marker.networks == $network) {
                            marker.network = true;
                        }
                    }
                }
            });
            filters.network = true;
        }

        if($contact != '' && $contact != null && $contact != undefined){
            markers.forEach(function (marker) {
                if (typeof marker.contacts != "undefined") {
                    marker.contact = false;
                    marker.contacts.forEach(function(contact_id){
                        if (contact_id == $('#search_contact').val()) {
                            marker.contact = true;
                        }
                    });
                }
            });
            filters.contact = true;
        }

        number_visible_markers = filter_markers(filters, limit);

        if( last_center != null && last_zoom != null){
            $map.setCenter(last_center);
            $map.setZoom(last_zoom);
        } else {
            bounds = new google.maps.LatLngBounds();
            markers.forEach(function(marker){
                if(marker.visible == true){
                    bounds.extend(marker.getPosition());
                }
             });

            if(bounds != null){
                $map.fitBounds(bounds);

                if(number_visible_markers == 0){
                    $map.setZoom(zoom_country);
                }
                if( $map.getZoom() > max_zoom ){
                    $map.setZoom(max_zoom);
                }
            }
        }
    };

    var filter_markers = function(filters, limit) {
        var set_filters = get_set_options(filters);
        if(!filters.distance && circle != null){
            circle.setMap(null);
        }

        markers.forEach(function(marker){
            var show = true;
            for (var opt=0; opt<set_filters.length; opt++) {
                if (marker[set_filters[opt]] != filters[set_filters[opt]]) {
                    show = false;
                }
            }
            if(marker.in_route){
                show = true;
            }
            if(show){
                limit++;
            }
            if(limit > 100){
                show = false;
            }
            marker.setVisible(show);
        });

        return limit;
    };

    var get_set_options = function(filters) {
        var ret_array = [];
        for (var option in filters) {
            if (filters[option]) {
                ret_array.push(option)
            }
        }
        return ret_array;
    };

    var getDistanceFromLatLonInKm = function (lat1, lon1, lat2, lon2) {
        var R = 6371;
        var dLat = deg2rad(lat2 - lat1);
        var dLon = deg2rad(lon2 - lon1);
        var a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2)
            ;
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c;
    };

    var deg2rad = function (deg) {
        return deg * (Math.PI / 180)
    };

    var latlng = function () {
        if ($('#longitude-localization').val() != '' && $('#latitude-localization').val() != '') {
            return new google.maps.LatLng(latitude(), longitude());
        } else {
            return new google.maps.LatLng($('#latitude-center').data('lat'), $('#longitude-center').data('lng'));
        }
    };

    var latitude = function(){
        return $('#latitude-localization').val();
    };

    var longitude = function(){
        return $('#longitude-localization').val();
    };

    var createMarker = function (latitude, longitude, address, dont_change_autocomplete_address) {

        $('#latitude-localization').val(latitude);
        $('#longitude-localization').val(longitude);
        var $aag_region_id = $('#aag_region_id').val();
        var $aag_region_uk = $('#aag_region_id_uk').val();
        var position = new google.maps.LatLng(latitude, longitude);
        var marker = new google.maps.Marker({
            position: position,
            animation: google.maps.Animation.DROP,
            map: $map,
            visible: false
        });
        if (circle != null) {
            circle.setMap(null);
        }
        var radius = '';
        if($aag_region_id != $aag_region_uk){
            radius = 1000;
        } else {
            radius = 1609.34;
        }
        circle = new google.maps.Circle({
            map: $map,
            radius: Number($('#search_distance').val()) * radius,
            fillColor: '#CC0000',
            strokeWeight: 0.5,
            strokeColor: '#777'
        });
        if (!address) {
            searchForAddress(marker, latitude, longitude, dont_change_autocomplete_address);
        }
        circle.bindTo('center', marker, 'position');
        marker_center = marker;
        return marker;
    };

    var removeMarkers = function () {
        if (typeof markers != "undefined") {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setVisible(false);
            }
        }
    };

    var searchAddress = function ($item) {
        autocomplete = new google.maps.places.Autocomplete($item[0]);
        var $distance = $('#search_distance');

        $distance.on('change', function () {
            var place = autocomplete.getPlace();
            if (place != undefined && $('#autocomplete-address').val() != '') {
                if (!place.geometry) {
                    return;
                }

                if( $('#last_position').prop('checked') == true ){
                    $map.setCenter(last_center);
                    $map.setZoom(last_zoom);
                } else if (place.geometry.viewport) {
                    $map.fitBounds(place.geometry.viewport);
                    if( $map.getZoom() > max_zoom ){
                        $map.setZoom(max_zoom);
                    }
                } else {
                    $map.panTo(place.geometry.location);
                }

                var address = '';
                if (place.address_components) {
                    address = [
                        (place.address_components[0] && place.address_components[0].short_name || ''),
                        (place.address_components[1] && place.address_components[1].short_name || ''),
                        (place.address_components[2] && place.address_components[2].short_name || '')
                    ].join(' ');
                }

                var content = '<div><strong>' + place.name + '</strong><br>' + address;
                createMarker(place.geometry.location.lat(), place.geometry.location.lng(), content);
            }
        });

        autocomplete.addListener('place_changed',function(){
            $distance.trigger('change');
            if ($distance.val() == '') {
                $distance.val($distance.find('option').eq(1).val());
                $distance.trigger('change').prop('disabled',false);
            } else {
                $distance.val($distance.val());
                $distance.trigger('change').prop('disabled',false);
            }
        });
    };

    var searchForAddress = function (marker, latitude, longitude, dont_change_autocomplete_address) {
        var url = 'https://maps.googleapis.com/maps/api/geocode/json?language=' + language_code + '&latlng=' + latitude + ',' + longitude;
        $.ajax({
            type: "GET",
            encoding: "UTF-8",
            url: url
        }).done(function (data) {
            if(typeof data.results[0] != 'undefined'){
                if (data.results[0].formatted_address) {
                    if (dont_change_autocomplete_address == null && document.getElementById("autocomplete-address")) {
                        $("#autocomplete-address").val(data.results[0].formatted_address);
                    }
                }
            }
        });
    };

    var setInfoWindow = function (point, marker) {
        var infowindow = new google.maps.InfoWindow();

        marker.addListener('mouseover', function () {
            var contentString =
                '<div id="map-container">' +
                '<div class="map-content">' +
                '<div>' +
                '<h2>' + point.Garage.business_name + '</h2>' +
                '<div class="alliance_bar"></div>' +
                '</div>' +
                '</div>' +
                '</div>';

            infowindow.setContent(contentString);
            infowindow.open($map, marker);
        });
        marker.addListener('mouseout', function () {
            infowindow.close();
        });

        marker.addListener('click', function () {
            marker.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(function() {
                marker.setAnimation(null)
            }, 3000);
            cargar_info_window(marker, this.id);
        });
    };

    var drawRoute = function () {
        hide_markers_route.forEach(function (marker_tmp) {
            marker_tmp.setIcon('/img/pins/visits/Pin_' + marker_tmp.last_visit + '.png');
            marker_tmp.in_route = false;
            var label = marker_tmp.getLabel();
            label.text = " ";
            marker_tmp.setLabel(label);
        });
        hide_markers_route = [];

        var waypts = [];
        var garages_ids = [];
        $('.garage_added').each(function () {
            var $fila = $(this);
            waypts.push({
                location: new google.maps.LatLng($fila.data('lat'), $fila.data('lng')),
                stopover: true
            });
            garages_ids.push($fila.data('id'));
        });
        if(waypts.length > 0){
            var inicio = waypts[0].location;
            var fin = waypts[waypts.length - 1].location;

            var request = {
                origin: inicio,
                destination: fin,
                waypoints: waypts,
                travelMode: 'DRIVING'
            };

            if( $('#last_position').prop('checked') == true ){
                directionsDisplay.setOptions({ preserveViewport: true });
            } else {
                directionsDisplay.setOptions({ preserveViewport: false });
            }

            directionsService.route(request, function (result, status) {
                if (status == 'OK') {
                    directionsDisplay.setDirections(result);
                }
            });

            var counter = '1';

            markers.forEach(function (marker) {
                if(isInArray(Number(marker.id), garages_ids)){
                    hide_markers_route.push(marker);
                }
            });

            garages_ids.forEach(function(garage_id){
                hide_markers_route.forEach(function(marker){
                    if(marker.id == garage_id){
                        marker.icon = '/img/pins/visits/sel_1.png';
                        marker.setLabel({
                            text: String(counter),
                            color: 'white',
                            zIndex: 99999999
                        });
                        marker.setZIndex(99999999);
                        counter = Number(counter) + 1;
                        marker.in_route = true;
                    } else {
                        marker.in_route = false;
                    }
                });
            });
            Visit.setOrder();
        } else {
            $map.setCenter(new google.maps.LatLng($('#latitude-center').data('lat'), $('#longitude-center').data('lng')));
            $map.setZoom(zoom_country);
        }
    };

    var cargar_info_window = function(marker, garage_id) {
        var $info = $('#info');
        var url = '/visits/ajax_get_data_infowindow_garage';
        var data = {};
        data.garage_id = garage_id;
        var request = PeticionAjax.get(url,data);

        request.done(function (data) {
            $info.html(data);
            $info.show();

            $('#add_to_route').show();
            $('.garage_added').each(function(){
                if($(this).data('id') == garage_id){
                    $('#remove_from_route').show();
                    $('#add_to_route').hide();
                }
            });


            $('#close_info').off('click').on('click',function(){
                $info.hide();
            });
            $('#add_to_route').off('click').on('click',function(){
                var url = $('#ajax_get_data_garage').data('url');
                var data = {};
                data.garage_id = marker.id;
                var request = PeticionAjax.post(url,data);
                request.done(function(data){
                    const cleanData = DOMPurify.sanitize(data);
                    $('#table-added-garages').append(cleanData);
                    if($('.garage_added').length > 0){
                        drawRoute();
                    }
                    $('.timepicker').each(function () {
                        $(this).timepicker();
                    });
                    Visit.loadRemoveGarage();
                    Visit.setOrder();
                    //Visit.setOrderTime();
                    Visit.setOrderTimeByStart();
                    //Visit.setOrderTimeByStartCurrentElement($('#table-added-garages').find('.input_time').prev().first());
                    Visit.setOrderTimeByStartCurrentElement($('#table-added-garages tr:nth-last-child(2)').find('.input_time').first());
                });
                $('#add_to_route').hide();
                $('#remove_from_route').show();
                /*setTimeout(function(){
                    //$('#table-added-garages').find('.input_time').first().trigger('blur');
                    $(this).prev().trigger('blur');
                }, 250);*/
            });
            $('#remove_from_route').off('click').on('click',function(){
                var garage_id = marker.id;
                $('.garage_added').each(function(){
                    if($(this).data('id') == garage_id){
                        $(this).find('.remove-garage').trigger('click');
                        $('#add_to_route').show();
                        $('#remove_from_route').hide();
                    }
                });
                setTimeout(function(){
                    $('#table-added-garages').find('.input_time').first().trigger('blur');
                }, 250);
            });
        });
    };

    var isInArray = function(value, array) {
        return array.indexOf(value) > -1;
    };

    return {
        createMapForIFrame: function ($item, $first_time) {
            var mapId = $item.data('map');
            var latitude = $('#latitude-localization').val();
            var longitude = $('#longitude-localization').val();
            createMap($('#' + mapId), latitude, longitude);
            if (latitude != '' && longitude != '') {
                var dont_change_autocomplete_address = true;
                createMarker(latitude, longitude, null, dont_change_autocomplete_address);
            }
            if($first_time){
                searchAddress($item);
            }
        },
        geolocate: function ($map) {
            var map = $('#map-visits');
            var url = map.data('url') + '?' + window.location.search.substring(1);
            if (map.length > 0) {
                $.ajax({type: "GET", encoding: "UTF-8", url: url})
                    .done(function (elements) {
                        elements = JSON.parse(elements);
                        if (typeof elements != "undefined") {
                            geoLocate($map, elements);
                        }
                    });
            }
        },
        drawRoute: function(){
            drawRoute();
        }
    }
})();