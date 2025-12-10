var GMaps = (function(){
    var map = null;
    var protocol = location.protocol;
    var host = protocol + '//' + window.location.hostname;
    var styledMapType = new google.maps.StyledMapType(
        [
            {
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#f5f5f5"
                    }
                ]
            },
            {
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#616161"
                    }
                ]
            },
            {
                "featureType": "poi",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "water",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            }
        ],
        {name: 'Alliance Map'});
    var styledMapTypeDefault = new google.maps.StyledMapType(
        [
            {
                "featureType": "poi",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "transit",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            }
        ],
        {name: 'Default'});

    var createMapWithMarkerClustering = function(element, points){
        var map = new google.maps.Map(element[0], {
            mapTypeControlOptions: {
                mapTypeIds: ['styled_map_default', 'styled_map']
            }
        });

        map.mapTypes.set('styled_map_default', styledMapTypeDefault);
        map.mapTypes.set('styled_map', styledMapType);
        map.setMapTypeId('styled_map_default');

        var bounds = new google.maps.LatLngBounds();
        var markers = [];
        points.forEach(function(point){
            if((Number(point.Distributor.latitude != 0) && (Number(point.Distributor.longitude) != 0))){
                latLng = new google.maps.LatLng(Number(point.Distributor.latitude), Number(point.Distributor.longitude));
                var url = host + '/distributors/view/' + point.Distributor.id;

                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map,
                    icon: '/img/pins/Pin-13.png',
                    url: url
                });

                markers.push(marker);

                bounds.extend(latLng);

                setInfoWindow(point, marker);
            }
        });

        var mcOptions = {
            gridSize: 50,
            styles: [{
                textColor: 'white',
                textSize: 13,
                url: '/img/pins/Cluster-12.png',
                width: 61,
                height: 31
            }],
            maxZoom: 15
        };

        var cluster = new MarkerClusterer(map, markers, mcOptions);
        map.fitBounds(bounds);
    };

    var setInfoWindow = function(point, marker){

        var contentString =
            '<div id="map-container">' +
                '<div class="map-content">' +
                    '<div>' +
                        '<h2>' + point.Distributor.name + '</h2>' +
                        '<div class="alliance_bar"></div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        var infowindow = new google.maps.InfoWindow({
            content: contentString,
            maxWidth: 350,
            closeBoxURL: ''
        });

        marker.addListener('click', function(){
            window.location.href = marker.url;
        });
        marker.addListener('mouseover', function(){
            infowindow.open(map, marker);
        });
        marker.addListener('mouseout', function(){
            infowindow.close();
        });
    };

    return {
        createMapForIFrame: function(){
            var $map = $('#map-distributors');
            var url = $map.data('url') + '?' + window.location.search.substring(1);
            if($map.length > 0){
                $.ajax({type: "GET", encoding: "UTF-8", url: url})
                    .done(function(elements){
                        elements = JSON.parse(elements);
                        if(elements.length){
                            createMapWithMarkerClustering($map, elements);
                        }
                    });
            }
        }
    }
})();

$(document).ready(function(){
    $('#button_show_map').click(function(){
        $('.row_map').slideDown();
        $('#button_show_map').hide();
        GMaps.createMapForIFrame()
    });
});