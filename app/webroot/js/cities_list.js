$(document).ready(function () {
    CitiesList.load();
});

var CitiesList = function () {
    var loadBehaviourSelectProvince = function () {
        $('.select-province-js').change(function (event) {
            event.preventDefault();
            var url = $(this).data('url');
            var div = $(this).data('div_cities');
            var city_field_name = $(this).data('city_field_name');
            var city_selected = $(this).data('city_selected');
            var cityGoogle = $('#autocomplete-country').data('cityGoogle');
            var is_config = $(this).data('is_config');

            var province_id = $(this).val();

            if (province_id != null && province_id.length !== 0) {
                data = {};
                data.province_id = $(this).val();
                data.city_field_name = city_field_name;
                data.city_selected = city_selected;
                data.cityGoogle = cityGoogle;
                data.is_config = is_config;

                PeticionAjax.mostrarCargando();
                var request = PeticionAjax.post(url, data);
                request.done(function (data) {
                    PeticionAjax.ocultarCargando();
                    $(div).html(data);
                    Select2.load();

                    if ($(div).data('change_disabled') == undefined) { // is necessary in config view
                        if ($('#edit-btn-disable').val() == 0) {
                            $('#autocomplete-city').prop('disabled', false);
                        } else {
                            $('#autocomplete-city').prop('disabled', true);
                        }
                    }

                    var citiesList = $('#autocomplete-city option');
                    var cities = $.map(citiesList, function (option) {
                        return option.text;
                    });

                    var foundCity = false;
                    $.each(cities, function (index, city) {
                        if (city == cityGoogle) {
                            $('#autocomplete-city option:contains("' + city + '")').prop('selected', true);
                            $('#autocomplete-city').trigger('change');
                            foundCity = true;
                            return false;
                        }
                    });
                    if (!foundCity) {
                        if (city_selected != undefined && city_selected != null) {
                            $('#autocomplete-city').val(city_selected).trigger('change');
                        } else {
                            $('#autocomplete-city').prop('selectedIndex', -1);
                            $('#autocomplete-city').trigger('change');
                            return false;
                        }
                    }
                });
            } else {
                $('#autocomplete-city').prop('selectedIndex', -1);
                $('#autocomplete-city').trigger('change');
                $('#autocomplete-city').empty();
                return false;
            }
        });
    };

    return {
        load: function () {
            loadBehaviourSelectProvince();
        }
    }
}();
