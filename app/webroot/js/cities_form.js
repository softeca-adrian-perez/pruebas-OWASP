$(document).ready(function(){
	CitiesForm.load();
});

var CitiesForm = function() {
	var autocompleteCoordinates = function (selector) {
		var geocoder = new google.maps.Geocoder();

		$(document).on('input', '#val-name2', function (event) {
			event.preventDefault();
			var $input = $(this);

			if ($input.val().length >= 3) {
				geocoder.geocode({
					'address': $input.val(),
				}, function(results, status) {
					if (status === google.maps.GeocoderStatus.OK) {
						var location = results[0];
						var lat = location.geometry.location.lat();
						var lng = location.geometry.location.lng();
						$(selector + ' #val-name3').val(lat);
						$(selector + ' #val-name4').val(lng);
					} else {
						$(selector + ' #val-name3').val('');
						$(selector + ' #val-name4').val('');
					}
				});
			} else {
				$(selector + ' #val-name3').val('');
				$(selector + ' #val-name4').val('');
			}
		});
	};

	return {
		load: function(){
			autocompleteCoordinates('#addValue');
			autocompleteCoordinates('#editValue');
		}
	};
}();
