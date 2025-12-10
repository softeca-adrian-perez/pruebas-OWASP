$(document).ready(function () {
    ProvincesList.load();
});

var ProvincesList = function () {

    var loadBehaviourSelectCountry = function () {
        $('.select-country-js').change(function (event) {
            event.preventDefault();
            var url = $(this).data('url');
            var div = $(this).data('div_provinces');
            var province_field_name = $(this).data('province_field_name');
            var provinceGoogle = $(this).data('provinceGoogle');
            var is_config = $(this).data('is_config');

            var country_id = $(this).val();

            if (country_id != null && country_id.length !== 0) {
                data = {};
                data.country_id = country_id;
                data.province_field_name = province_field_name;
                data.provinceGoogle = provinceGoogle;
                data.is_config = is_config;

                PeticionAjax.mostrarCargando();
                var request = PeticionAjax.post(url, data);

                request.done(function (data) {
                    PeticionAjax.ocultarCargando();
                    $(div).html(data);
                    $(div).children('div.input').addClass('required');
                    CitiesList.load();
                    Select2.load();
                    $('#autocomplete-province').prop('disabled', false);

                    var provincesList = $('#autocomplete-province option');
                    var provinces = $.map(provincesList, function (option) {
                        return option.text;
                    });

                    var foundProvince = false;
                    $.each(provinces, function (index, province) {
                        if (province == provinceGoogle) {
                            $('#autocomplete-province option:contains("' + province + '")').prop('selected', true);
                            $('#autocomplete-province').trigger('change');
                            foundProvince = true;
                            return false;
                        }
                    });
                    if (!foundProvince) {
                        $('#autocomplete-province').prop('selectedIndex', -1);
                        $('#autocomplete-province').trigger('change');
                        return false;
                    }
                });
            } else {
                $('#autocomplete-province').prop('selectedIndex', -1);
                $('#autocomplete-province').trigger('change');
                $('#autocomplete-province').empty();
                return false;
            }
        });
    };

    return {
        load: function () {
            loadBehaviourSelectCountry();
        }
    }
}();
