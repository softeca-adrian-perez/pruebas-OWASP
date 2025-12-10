$(document).ready(function () {
    pointsnet = [];
    markers = [];
    bounds = new google.maps.LatLngBounds();
    if( $('#button_show_map').length > 0){
        $('#button_show_map').click(function(){
            $('.row_map').slideDown();
            $('#button_show_map').hide();
            Maps.load();
        });
    }else{
        $('.row_map').slideDown();
        Maps.load();
    }
});

var Maps = (function () {

    var loadPoints = function () {
        var select = $('#map');
        var uri = select.data('url');
        var img = select.data('img');
        var request = PeticionAjax.post(uri);
        request.done(function (data) {
            var points = JSON.parse(data);
            for (var i = 0; i < points.length; i++) {
                if (points[i].Garage.latitude != null && points[i].Garage.longitude != null) {
                    if (points[i].GarageNetwork.id == null) {
                        points[i].Network.image_pin = 'Pin-15.png';
                        points[i].Network.image_cluster = 'Cluster-15.png';
                    }
                    var marker = new google.maps.Marker({
                        position: {lat: Number(points[i].Garage.latitude), lng: Number(points[i].Garage.longitude)},
                        town: points[i].Garage.town,
                        name: points[i].Garage.name,
                        network: points[i].Garage.network_id,
                        network_name: points[i].Network.name,
                        icon: img + points[i].Network.image_pin,
                        cluster: img + points[i].Network.image_cluster,
                        garage_id: points[i].Garage.id,
                        visible: false,
                        map: map
                    });
                    marker = samemarkers(marker);
                    keysExists(marker);
                    info(marker);
                }
            }
            cluster(pointsnet);
        });
    };

    var loadPointsDashboard = function () {
        var select = $('#map');
        var uri = select.data('url');
        var img = select.data('img');
        var data = {};
        data.region_id = $('#region-id').val();
        data.country_id =  $('#country-id').val();
        data.network_id =  $('#network-id').val();

        var request = PeticionAjax.post(uri, data);
        request.done(function (data) {
            var points = JSON.parse(data);
            for (var i = 0; i < points.length; i++) {
                if (points[i].Garage.latitude != null && points[i].Garage.longitude != null) {
                    if (points[i].GarageNetwork.id == null) {
                        points[i].Network.image_pin = 'Pin-15.png';
                        points[i].Network.image_cluster = 'Cluster-15.png';
                    }
                    var marker = new google.maps.Marker({
                        position: {lat: Number(points[i].Garage.latitude), lng: Number(points[i].Garage.longitude)},
                        town: points[i].Garage.town,
                        name: points[i].Garage.name,
                        network: points[i].Garage.network_id,
                        network_name: points[i].Network.name,
                        icon: img + points[i].Network.image_pin,
                        cluster: img + points[i].Network.image_cluster,
                        garage_id: points[i].Garage.id,
                        visible: true,
                        map: map
                    });
                    marker = samemarkers(marker);
                    keysExists(marker);
                    info(marker);
                }
            }
            cluster(pointsnet);
        });
    };

    var keysExists = function (marker) {
        var val = 0;
        var name = marker.network_name;
        for (point in pointsnet) {
            if (name == null) {
                name = "nonet";
            }
            if (point == name) {
                val = 1;
            }
        }
        if (val == 0) {
            pointsnet[name] = [marker];
        } else {
            pointsnet[name].push(marker);
        }
    };


    var info = function (marker) {
        var title = '';
        if(marker.network_name !== null){
            title =
                '<div>' +
                    '<h2 class="ta-center">' + marker.network_name + '</h2>' +
                    '<div class="alliance_bar"></div>' +
                '</div>';
        } else {
            title =
                '<div>' +
                    '<h2 class="ta-center">Network Not Associated</h2>' +
                    '<div class="alliance_bar"></div>' +
                '</div>';
        }
        var contentString = '' +
            '<div id="map-container">' +
                title +
                '<div class="map-content">' +
                    '<p><strong>'+$.i18n._('Garage.Garage')+': </strong>' + marker.name + '</p>' +
                '</div>' +
            '</div>';

        var infowindow = new google.maps.InfoWindow({
            content: contentString,
            maxWidth: 350,
            closeBoxURL: ''
        });

        marker.addListener('mouseover', function () {
            infowindow.open(marker.map, marker);
        });
        marker.addListener('mouseout', function () {
            infowindow.close();
        });

        marker.addListener('click', function () {
            location.href = '/garages/view/' + marker.garage_id;
        })
    };

    var cluster = function (pointsnet) {
        for (net in pointsnet) {
            var img = pointsnet[net][0].cluster;
            if (net == "nonet") {
                color = "black";
            }
            else {
                color = "white";
            }
            markerCluster = new MarkerClusterer(map, pointsnet[net], {
                styles: [{
                    textColor: color,
                    textSize: 13,
                    url: img,
                    height: 31,
                    width: 61
                }]
            });
            markerCluster.setIgnoreHidden(true);
            markerCluster.repaint();
        }
    };

    var showhide = function () {
        $(".overmap").on('click', 'button', function () {
            if ($(this).hasClass('filled')) {
                $(this).removeClass('filled');
                $(this).addClass('outlined');
                showmarkers($(this).data('net'),false);
            } else {
                $(this).removeClass('outlined');
                $(this).addClass('filled');
                showmarkers($(this).data('net'),true);
            }
        });
    };

    var showmarkers = function (name,visible) {
        if(pointsnet[name] != undefined){
            for (var i = 0;i < pointsnet[name].length; i++) {
                pointsnet[name][i].setVisible(visible);

                bounds.extend(pointsnet[name][i].position);
            }
            // if(last_center != null && last_zoom != null) {
            //     map.setCenter(last_center);
            //     map.setZoom(last_zoom);
            // } else
            if(bounds.getCenter().lat() == 0 && bounds.getCenter().lng() == -180) {
                map.setZoom(6);
                map.setCenter(new google.maps.LatLng( $('#latitude-center').html().trim() , $('#longitude-center').html().trim() ));
            } else {
                map.fitBounds(bounds);
            }

            markerCluster.setIgnoreHidden(true);
            markerCluster.repaint();
        }
    };

    var samemarkers = function (marker) {
        for(var i  = 0; i < markers.length; i++) {
            if(markers[i].position['lat'] == marker.position['lat'] && markers[i].position['lng'] == marker.position['lng']) {
                marker.position['lat'] += (Math.random() -.5) / 1500;
                marker.position['lng'] += (Math.random() -.5) / 1500;
            }
            markers.push(marker);
        }
        return marker;
    };

    var remenber = function () {
        last_center = null;
        last_zoom = null;
        $('#last_position').click(function () {
            if($(this).prop('checked') == true) {
                last_center = map.getCenter();
                last_zoom = map.getZoom();
            } else {
                last_center = null;
                last_zoom = null;
            }
        });
    };

    var initMap = function () {
        var loc = {lat: 42, lng: -3};
        map = new google.maps.Map($('#map')[0], {
            zoom: 4,
            center: loc
        });
        if( $('#button_show_map').length > 0){
            loadPoints();
        }else{
            loadPointsDashboard();
        }
        remenber();
    };

    return {
        load: function () {
            initMap();
            showhide();
        }
    }
})();