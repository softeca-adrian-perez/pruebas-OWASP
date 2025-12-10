var GMaps = (function () {

    var $map;
    var $item_map;
    var bounds;
    var marker;
    var markers;
    var autocomplete;
    var infowindow = null;

    var createMap = function ($item, latitude, longitude, zoom) {

        $item_map = $item;

        var styles = [
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [
                    { visibility: "off" }
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
            },
        ];
        var styledMap = new google.maps.StyledMapType(styles, { name: "Styled Map" });


        zoom = zoom || 14;

        $('#localizame').on('click', localizame);

        var center = latlng(latitude, longitude);

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

        var listener = google.maps.event.addListener($map, "idle", function () {
            google.maps.event.trigger($map, 'resize');
            google.maps.event.removeListener(listener);
        });

        var listenerClick = google.maps.event.addListener($map, 'click', function (event) {
            if ($item.data('editable')) {
                createMarker(event.latLng.lat(), event.latLng.lng());
            } else {
                google.maps.event.removeListener(listenerClick);
            }
        });
    };

    var localizame = function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(coordenadas, errores);
        } else {
            swal({
                title: $.i18n._('General.No_geo'),
                type: "warning"
            });
        }
    };

    var coordenadas = function (position) {
        setLatitude(position.coords.latitude);
        setLongitude(position.coords.longitude);
        createMap($item_map, position.coords.latitude, position.coords.longitude);
        createMarker(position.coords.latitude, position.coords.longitude);
    };

    var latitude = function () {
        return $('#latitude-localization').val();
    };

    var longitude = function () {
        return $('#longitude-localization').val();
    };

    var setLatitude = function (latitude) {
        $('#latitude-localization').val(latitude.toString());
    };

    var setLongitude = function (longitude) {
        $('#longitude-localization').val(longitude.toString());
    };

    var errores = function (err) {
        if (err.code == 0) {
            swal({
                title: $.i18n._('Constants.Error_alert_general'),
                text: "An error has occurred",
                type: "error"
            });
        }
        if (err.code == 1) {
            swal({
                title: $.i18n._('Constants.Error_alert_general'),
                text: "You haven't accept to share your position",
                type: "error"
            });
        }
        if (err.code == 2) {
            swal({
                title: $.i18n._('Constants.Error_alert_general'),
                text: "Cannot get the actual position",
                type: "error"
            });
        }
        if (err.code == 3) {
            swal({
                title: $.i18n._('Constants.Error_alert_general'),
                text: "Waiting time surpassed",
                type: "error"
            });
        }
    };

    var latlng = function (latitude, longitude) {
        if (latitude != '' && longitude != '') {
            return new google.maps.LatLng(latitude, longitude);
        } else {
            return new google.maps.LatLng(51.507339, -0.127737);
        }

    };

    var createMarker = function (latitude, longitude, address, dont_change_autocomplete_address) {
        removeMarkers();
        $('#latitude-localization').val(latitude);
        $('#longitude-localization').val(longitude);
        var position = new google.maps.LatLng(latitude, longitude);
        var marker = new google.maps.Marker({
            position: position,
            animation: google.maps.Animation.DROP,
            map: $map
        });
        if (address) {
            createInfoWindow(marker, address, true);
        } else {
            searchForAddress(marker, latitude, longitude, dont_change_autocomplete_address);
        }
        $('.remove-marker').show();
        bounds.extend(position);
        fitBounds();
        markers.push(marker);
        return marker;
    };

    var createMarkerWithoutInfo = function (latitude, longitude) {
        removeMarkers();
        $('#latitude-localization').val(latitude);
        $('#longitude-localization').val(longitude);
        var position = new google.maps.LatLng(latitude, longitude);
        var marker = new google.maps.Marker({
            position: position,
            animation: google.maps.Animation.DROP,
            map: $map
        });

        $('.remove-marker').show();
        bounds.extend(position);
        fitBounds();
        markers.push(marker);
        return marker;
    };



    var createMultipleMarkers = function (map_markers) {
        var marker, i, content;
        for (i = 0; i < map_markers.length; i++) {
            var position = new google.maps.LatLng(map_markers[i].latitude, map_markers[i].longitude);
            marker = new google.maps.Marker({
                position: position,
                map: $map
            });
            if (map_markers[i].address != '') {
                content = '<h3>' + map_markers[i].name + '</h3><p>' + map_markers[i].address + '</p>';
                if (map_markers[i].image_wave_id) {
                    content += '<img class="marker-image" src="/elements/get_image/' + map_markers[i].image_wave_id + '" />';
                }
                createInfoWindow(marker, content, false);
            } else {
                searchForAddress(marker, map_markers[i].latitude, map_markers[i].longitude);
            }
            bounds.extend(position);
            markers.push(marker);
        }
    };

    var searchAddress = function ($item) {
        var userAagRegionId = $('#autocomplete-address').data('user_aag_region_id');
        const UKIERegionId = 2;
        const BENLLURegionId = 1;

        if (userAagRegionId == UKIERegionId) {
            var restrictions = {
                componentRestrictions: { 'country': ['GB', 'IE'] }
            };
            autocomplete = new google.maps.places.Autocomplete($item[0], {
                language: 'en',
                ...restrictions
            });
        } else if (userAagRegionId == BENLLURegionId) {
            var restrictions = {
                componentRestrictions: { 'country': ['BE', 'NL', 'LU'] }
            };
            autocomplete = new google.maps.places.Autocomplete($item[0], {
                language: 'en',
                ...restrictions
            });
        }

        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            let lat = place.geometry.location.lat();
            let lng = place.geometry.location.lng();
            $('#latitude-localization').val(lat);
            $('#longitude-localization').val(lng);
            if (!place.geometry) {
                console.log("Autocomplete's returned place contains no geometry");
                return;
            }
            if (place.geometry.viewport) {
                $map.fitBounds(place.geometry.viewport);
            } else {
                $map.panTo(place.geometry.location);
            }

            var address = '';
            var address2 = '';
            var address3 = '';
            var address4 = '';
            var town = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
                address2 = (place.address_components[0] && place.address_components[0].short_name) || '';
                address3 = (place.address_components[1] && place.address_components[1].short_name) || '';
                address4 = (place.address_components[2] && place.address_components[2].short_name) || '';
                $('#autocomplete-address2').val(address2);
                $('#autocomplete-address3').val(address3);
                $('#autocomplete-address4').val(address4);
                var country = '';
                var province = '';
                var city = '';
                for (var i = 0; i < place.address_components.length; i++) {
                    var component = place.address_components[i];
                    //Checks component with postal_town type and it's used as town field
                    if (component.types == 'postal_town') {
                        town = component.short_name;
                        $('#autocomplete-town').val(town);
                    }
                    if (component.types.includes('postal_code')) {
                        var postCode = component.long_name;
                        break;
                    }
                    if (place.address_components[place.address_components.length - 2].short_name !== 'GB') {
                        if (component.types.includes('country')) {
                            country = component.short_name;
                        }
                        if (component.types.includes('administrative_area_level_1')) {
                            province = component.long_name;
                        }
                        if (component.types.includes('locality')) {
                            city = component.long_name;
                        }
                    } else {
                        if (component.types.includes('country')) {
                            country = component.short_name;
                        }
                        if (component.types.includes('administrative_area_level_2')) {
                            province = component.long_name;
                        }
                        if (component.types.includes('postal_town')) {
                            city = component.long_name;
                        }
                    }
                }
                let countrySelect = $('#autocomplete-country');
                let postCodeValid = '';
                countrySelect.data('cityGoogle', city);
                if (postCode.indexOf(' ') >= 0) {
                    postCodeValid = postCode.split(' ')[0];
                } else if (postCode.indexOf(' ') == -1) {
                    postCodeValid = postCode;
                } else {
                    countrySelect.data('provinceGoogle', province);
                }
                $('#autocomplete-postcode').val(postCode);
                let url = $('#autocomplete-address').data('url');
                if (postCodeValid !== 'undefined' && country !== 'undefined') {
                    let data = {};
                    data.postcode = postCodeValid;
                    data.country_code = country;
                    let request = PeticionAjax.get(url, data);
                    request.done(function (data2) {
                        let info = JSON.parse(data2);
                        if (info !== null) {
                            let province_name = info[0];
                            let country_id = info[1];
                            countrySelect.data('provinceGoogle', province_name);
                            countrySelect.val(country_id).trigger('change');
                        } else {
                            countrySelect.prop('selectedIndex', -1);
                            countrySelect.trigger('change');
                            return false;
                        }
                    });
                }
            }

            var content = '<div><strong>' + place.name + '</strong><br>' + address;
            createMarker(place.geometry.location.lat(), place.geometry.location.lng(), content);
        });

    };

    var searchForAddress = function (marker, latitude, longitude, dont_change_autocomplete_address) {
        var url = 'https://maps.googleapis.com/maps/api/geocode/json?language=en&latlng=' + latitude + ',' + longitude;
        $.ajax({
            type: "GET",
            encoding: "UTF-8",
            url: url
        }).done(function (data) {
            if (data.results[0].formatted_address) {
                createInfoWindow(marker, data.results[0].formatted_address, true);
                if (dont_change_autocomplete_address == null && document.getElementById("autocomplete-address")) {
                    $("#autocomplete-address").val(data.results[0].formatted_address);
                }
            }
        });
    };

    var createInfoWindow = function (marker, content, open_infowindow) {
        if (infowindow) {
            infowindow.close();
        }
        infowindow = new google.maps.InfoWindow();
        google.maps.event.addListener(infowindow, 'domready', function () {
            var iwOuter = $('.gm-style-iw');
            var iwCloseBtn = iwOuter.next();
            var iwCloseImg = iwCloseBtn.next();
            iwCloseBtn.css({ right: '2px', top: '2px' });
            iwCloseImg.css({ right: '2px', top: '2px' });
        });
        google.maps.event.addListener(marker, 'click', function () {
            infowindow.setContent(content);
            infowindow.open($map, marker);
        });
        if (open_infowindow) {
            infowindow.setContent(content);
            infowindow.open($map, marker);
        }
    };

    var extendBounds = function (position) {
        var location = new google.maps.LatLng(position.latitud, position.longitud);
        bounds.extend(location);
    };

    var fitBounds = function () {
        $map.fitBounds(bounds);
        var listener = google.maps.event.addListener($map, "idle", function () {
            if ($map.getZoom() > 16) $map.setZoom(15);
            if ($map.getZoom() < 6) $map.setZoom(6);
            google.maps.event.removeListener(listener);
        });
    };

    var removeMarkers = function () {
        if (typeof markers != 'undefined') {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers.length = 0;
        }
        bounds = new google.maps.LatLngBounds();
    };

    var getElements = function (url) {
        return $.ajax({
            type: "GET",
            encoding: "UTF-8",
            url: url
        });
    };

    var createMapWithPoints = function (element, points) {
        var map = new google.maps.Map(element[0], {
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var markerBounds = new google.maps.LatLngBounds();

        points.forEach(function (point) {
            latLng = new google.maps.LatLng(Number(point.latitude), Number(point.longitude));

            var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                title: point.garage_name,
                icon: 'http://maps.google.com/mapfiles/ms/icons/blue.png'
            });

            setInfoPoint(point, marker, map);

            markerBounds.extend(latLng);
        });

        map.fitBounds(markerBounds);
    };

    var createMapWithMarkerClustering = function (element, points) {
        var map = new google.maps.Map(element[0], {
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var markerBounds = new google.maps.LatLngBounds();
        var markers = [];
        points.forEach(function (point) {
            latLng = new google.maps.LatLng(Number(point.latitude), Number(point.longitude));

            var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                title: point.garage_name,
                icon: 'http://maps.google.com/mapfiles/ms/icons/blue.png'
            });

            markers.push(marker);

            setInfoPoint(point, marker, map);

            markerBounds.extend(latLng);
        });

        var host = window.location.hostname;

        var pathToIcon = 'http://' + host + '/img/iconos/cobertura_talleres.png';

        var clusterStyles = [
            {
                textColor: 'white',
                textSize: 15,
                url: pathToIcon,
                height: 32,
                width: 32
            },
            {
                textColor: 'white',
                textSize: 15,
                url: pathToIcon,
                height: 32,
                width: 32
            },
            {
                textColor: 'white',
                textSize: 15,
                url: pathToIcon,
                height: 32,
                width: 32
            }
        ];

        var mcOptions = {
            gridSize: 50,
            styles: clusterStyles,
            maxZoom: 15
        };

        var markerCluster = new MarkerClusterer(map, markers, mcOptions);
        map.fitBounds(markerBounds);
    };

    var setInfoPoint = function (point, marker, map) {
        var title = '<div class="map-title">' + point.garage_name + '</div>';
        var info = createInfoHtml(point);

        var contentString =
            '<div id="map-container">' +
            title +
            '<div class="map-content">' +
            info +
            '</div>' +
            '</div>';

        var infowindow = new google.maps.InfoWindow({
            content: contentString,
            maxWidth: 350,
            closeBoxURL: '',
        });

        marker.addListener('mouseover', function () {
            infowindow.open(map, marker);
        });
        marker.addListener('mouseout', function () {
            infowindow.close();
        });
    };

    var createInfoHtml = function (point) {
        var result = '';
        if (point.address != '') {
            result = result + '<b>' + point.address_str + ':</b> ' + point.address + '<br>';
        }

        if (point.town) {
            result = result + '<b>' + point.town_str + ':</b> ' + point.town + '<br>';
        }

        if (point.province) {
            result = result + '<b>' + point.province_str + ':</b> ' + point.province + '<br>';
        }

        if (point.phone) {
            result = result + '<b>' + point.phone_str + ':</b> ' + point.phone + '<br>';
        }

        if (point.member) {
            result = result + '<b>' + point.member_str + ':</b> ' + point.member;
        }

        return result;
    };

    return {
        createEditableMap: function () {
            var $map = $('#map-editable');
            var latitude = $('#latitude-localization').val();
            var longitude = $('#longitude-localization').val();
            createMap($map, latitude, longitude);
            if (latitude != '' && longitude != '') {
                createMarker(latitude, longitude);
            }
        },

        createSingleMap: function () {
            var $map = $('#map-single');
            var latitude = $map.data('latitude');
            var longitude = $map.data('longitude');
            createMap($map, latitude, longitude);
            if (latitude != '' && longitude != '') {
                createMarker(latitude, longitude);
            }
        },

        createSingleMapWithoutInfo: function () {
            var $map = $('#map-single-without-info');
            var latitude = $map.data('latitude');
            var longitude = $map.data('longitude');

            if (latitude != undefined && longitude != undefined) {
                createMap($map, latitude, longitude);
                if (latitude != '' && longitude != '') {
                    createMarkerWithoutInfo(latitude, longitude);
                }
            }
        },

        createMultipleMap: function () {
            var $map = $('#map-multiple');
            var url = $map.data('url');
            getElements(url).done(function (elements) {
                elements = JSON.parse(elements);
                if (elements.length) {
                    createMap($map, elements[0].latitude, elements[0].longitude);
                    createMultipleMarkers(elements);
                }
            });
        },

        createMapWithManyPoints: function () {
            var $map = $('#map-many-points');
            var url = $map.data('url') + '?' + window.location.search.substring(1);
            getElements(url).done(function (elements) {
                elements = JSON.parse(elements);
                if (elements.length) {
                    createMapWithPoints($map, elements);
                }
            });
        },

        createMapWithManyPointsAgroup: function () {
            var $map = $('#map-many-points-js');
            var url = $map.data('url') + '?' + window.location.search.substring(1);
            getElements(url).done(function (elements) {
                elements = JSON.parse(elements);
                if (elements.length) {
                    createMapWithMarkerClustering($map, elements);
                }
            });
        },

        loadBehaviours: function () {
            $('.center-element').click(function (event) {
                event.preventDefault();
                var latitude = $(this).data('latitude');
                var longitude = $(this).data('longitude');
                var position = new google.maps.LatLng(latitude, longitude);
                $map.panTo(position);
            });
            $('.remove-marker').click(function (event) {
                event.preventDefault();
                removeMarkers();
                $('#latitude-localization').val('');
                $('#longitude-localization').val('');
                $("#autocomplete-address").val('');
                $("#autocomplete-postcode").val('');
                $('#autocomplete-country').val('');
                $('.remove-marker').hide();
            });
            $('#locate-pin').click(function (event) {
                event.preventDefault();
                removeMarkers();
                var latitude = $('#latitude-localization').val();
                var longitude = $('#longitude-localization').val();
                $("#autocomplete-address").val('');
                $("#autocomplete-postcode").val('');
                $('#autocomplete-country').val('');
                $('.remove-marker').hide();
                if (latitude != '' && longitude != '') {
                    createMarker(latitude, longitude);
                }
            });
        },
        geolocate: function ($item) {
            var mapId = $item.data('map');
            var latitude = $('#latitude-localization').val();
            var longitude = $('#longitude-localization').val();
            createMap($('#' + mapId), latitude, longitude);
            if (latitude != '' && longitude != '') {
                var dont_change_autocomplete_address = true;
                createMarker(latitude, longitude, null, dont_change_autocomplete_address);
            }
            searchAddress($item);
        }
    }
})();


$(document).ready(function () {
    if (document.getElementById("map-editable")) {
        GMaps.createEditableMap();
    } else if (document.getElementById("map-single")) {
        GMaps.createSingleMap();
    } else if (document.getElementById("map-single-without-info")) {
        GMaps.createSingleMapWithoutInfo();
    } else if (document.getElementById("map-multiple")) {
        GMaps.createMultipleMap();
    } else if (document.getElementById("map-many-points")) {
        GMaps.createMapWithManyPoints();
    } else if (document.getElementById("map-many-points-js")) {
        GMaps.createMapWithManyPointsAgroup();
    } else if (document.getElementById("autocomplete-address")) {
        GMaps.geolocate($('#autocomplete-address'));
    }
    GMaps.loadBehaviours();
});