$(document).ready(function () {
    Garages.load();
});

var Garages = (function () {
    var removeContact = function () {
        $('.remove-contact-js').click(function () {
            selected_row = $(this);
            var request = PeticionAjax.post(selected_row.data('url'));
            request.done(function () {
                $(selected_row).closest('tr').remove()
            });
            request.fail(function () {
                Alertas.show('fallo', $.i18n._('Constants.Error_alert_general'), $('#alert-msg'))
            })
        });

    };

    var searchTime = function () {
        var timer;

        $("#name-garages").keyup(function (e) {
            clearTimeout(timer);
            timer = setTimeout(function search() {
                searchContract();
            }, 750);
        });
    };

    var searchContract = function () {
        let name_garage = $('#name-garages').val() == undefined ? '' : 'name=' + $('#name-garages').val();
        let trading_group_id = $('#trading-group-id').val() == undefined ? '' : 'trading_group_id=' + $('#trading-group-id').val();
        let region_id = $('#region-id').val() == undefined ? '' : 'region_id=' + $('#region-id').val();
        let country_id = $('#country-id').val() == undefined ? '' : 'country_id=' + $('#country-id').val();
        let g_number_id = $('#g-number-id').val() == undefined ? '' : 'g_number_id=' + $('#g-number-id').val();
        let city_id = $('#city-id').val() == undefined ? '' : 'city_id=' + $('#city-id').val();
        let network_id = $('#network-id').val() == undefined ? '' : 'network_id=' + $('#network-id').val();
        let status_id = $('#status-id').val() == undefined ? '' : 'status_id=' + $('#status-id').val()
        let annex_detail_id = $('#annex_detail-id').val() == undefined ? '' : 'annex_detail_id=' + $('#annex_detail-id').val()
        let bdm_id = $('#bdm-id').val() == undefined ? '' : 'bdm_id=' + $('#bdm-id').val();
        let postcode = $('#postcode-id').val() == undefined ? '' : 'postcode=' + $('#postcode-id').val();
        let service_id = $('#service-id').val() == undefined ? '' : 'service_id=' + $('#service-id').val();
        let distributor_id = $('#distributor-id-js').val() == undefined ? '' : 'distributor_id=' + $('#distributor-id-js').val();
        let town = $('#town').val() == undefined ? '' : 'town=' + $('#town').val()
        let ramps = $('#ramps-id').val() == undefined ? '' : 'ramps=' + $('#ramps-id').val();
        let MOT_bays = $('#MOT-bays-id').val() == undefined ? '' : 'MOT_bays=' + $('#MOT-bays-id').val();
        let phone = $('#phone-id').val() == undefined ? '' : 'phone=' + $('#phone-id').val();
        let vehicle_type_id = $('#vehicle-type-id').val() == undefined ? '' : 'vehicle_type_id=' + $('#vehicle-type-id').val();
        let foundation_year = $('#foundation-year-id').val() == undefined ? '' : 'foundation_year=' + $('#foundation-year-id').val();
        let lead_source_id = $('#lead_source_id').val() == undefined ? '' : 'lead_source=' + $('#lead_source_id').val();
        let ref_code = $('#ref-code-id').val() == undefined ? '' : 'ref_code=' + $('#ref-code-id').val();
        let external_agreements_id = $('#external-agreements-id').val() == undefined ? '' : 'external_agreements_id=' + $('#external-agreements-id').val()
        let internal_agreements_id = $('#internal_agreements_id').val() == undefined ? '' : 'internal_agreements_id=' + $('#internal_agreements_id').val()
        let erp_id = $('#erp_id-id').val() == undefined ? '' : 'erp_id=' + $('#erp_id-id').val()

        let url = name_garage + '&' + trading_group_id + '&' + network_id + '&' + region_id + '&' + country_id + '&' + g_number_id + '&' + postcode + '&' + service_id + '&' + distributor_id + '&' + city_id + '&' + ramps + '&' + MOT_bays + '&' + phone + '&' + vehicle_type_id + '&' + foundation_year + '&' + lead_source_id + '&' + ref_code + '&' + status_id + '&' + annex_detail_id + '&' + town + '&' + external_agreements_id + '&' + internal_agreements_id + '&' + bdm_id + '&' + erp_id;

        let request = PeticionAjax.post($('#name-garages').data('url') + '?' + url);
        request.done(function (data) {
            $('#ajax_search_home').html(data);
            if ($('#total_paginator').html() != undefined) {
                $('#counter-total').html($('#total_paginator').html());
            } else {
                $('#counter-total').html($('table tbody tr').length);
            }

            $('tr.link-js td').click(function () {
                if (!$(this).hasClass('no-link-js')) {
                    window.location = $(this).parent('tr').data('url');
                }
            });
        });
    };

    var garageNetworks = function () {
        let checkContractStartDate = 0;
        let checkLiveStatus = 0;
        $('#select-network').on('change', function () {
            var url = $('#div_trading_group').data('url');
            var network_id = $('#select-network').val();
            url += '/' + network_id;
            PeticionAjax.mostrarCargando();
            var request = PeticionAjax.get(url);
            request.done(function (data) {
                PeticionAjax.ocultarCargando();
                $('#div_trading_group').html(data);
                $("#trading_group_id").select2();
                if (network_id == '') {
                    network_id = null;
                    $('#div_trading_group').addClass('d-none');
                } else {
                    $('#div_trading_group').removeClass('d-none');
                }
            });

        });

        $('#contract_start_date').datepicker().on("input change", function (e) {
            if (e.target.value == '') {
                $('.current_charge-js').parent().removeClass("required");
                $('.member_pays-js').parent().removeClass("required");
                $('.garage_pays-js').parent().removeClass("required");
            } else {
                checkContractStartDate = 1;
            }
            if (checkContractStartDate == 1 && checkLiveStatus == 1) {
                $('.current_charge-js').parent().addClass("required");
                $('.member_pays-js').parent().addClass("required");
                $('.garage_pays-js').parent().addClass("required");
            }
        });

        $("#status").on('change', function () {
            if ($(this).val() == 7) {
                var url = $("#reason-hold-select2").data('url');
                var data = {};
                data['status_id'] = $("#status").val();

                var request = PeticionAjax.post(url, data);
                request.done(function (data) {
                    if (data) {
                        $('#reason-hold-select2').empty().trigger('change');
                        resultado = JSON.parse(data);
                        $.each(resultado, function (index, value) {
                            info = {
                                id: index,
                                text: value
                            };
                            var newOption = new Option(info.text, info.id, false, false);
                            $('#reason-hold-select2').append(newOption).trigger('change');
                        });
                        $('#reason-hold-select2').val('').trigger('change');
                    }
                    $('#reason_leaving_id').addClass("d-none");
                    $('#comment_reason').addClass('d-none');
                    $('#leaving_comment').addClass("d-none");
                    $('#reason_hold_id').removeClass("d-none");
                    $('#reason-hold-select2').parent().addClass("required");
                });
            } else if ($(this).val() == 8) {
                var url = $("#reason-leaving-select2").data('url');
                var data = {};
                data['status_id'] = $("#status").val();
                $('#comment_reason').removeClass('d-none');

                var request = PeticionAjax.post(url, data);
                request.done(function (data) {
                    if (data) {
                        $('#reason-leaving-select2').empty().trigger('change');
                        resultado = JSON.parse(data);
                        $.each(resultado, function (index, value) {
                            info = {
                                id: index,
                                text: value
                            };
                            var newOption = new Option(info.text, info.id, false, false);
                            $('#reason-leaving-select2').append(newOption).trigger('change');
                        });
                        $('#reason-leaving-select2').val('').trigger('change');
                    }
                    $('#reason_hold_id').addClass("d-none");
                    if ($("#reason-leaving-select2").data('inactive') == false) {
                        $('#reason_leaving_id').removeClass("d-none");
                    }
                    $('#leaving_comment').removeClass("d-none");
                    $('#reason-leaving-select2').parent().addClass("required");
                    $('#comment_reason').parent().addClass("required");
                });
            } else {
                $('#reason-select2').val('').trigger('change');
                $('#reason_hold_id').addClass("d-none");
                $('#date-left').addClass("d-none");
                $('#reason_leaving_id').addClass("d-none");
                $('#leaving_comment').addClass("d-none");
                $('#date-hold').addClass("d-none");
                $('#date_leaving').val("");
                $('#date_on_hold').val("");
                $('#comment_reason').addClass("d-none");
            }
            if ($(this).val() == 5) {
                checkLiveStatus = 1;
            }
            if (checkContractStartDate == 1 && checkLiveStatus == 1) {
                $('.current_charge-js').parent().addClass("required");
                $('.member_pays-js').parent().addClass("required");
                $('.garage_pays-js').parent().addClass("required");
            }
        });

        $("#btn-guardar").on('click', function () {
            event.preventDefault();
            if ($("#status").val() == 7) {
                if (!$("#reason-hold-select2").val()) {
                    swal({
                        title: $.i18n._('Config.Empty_reason'),
                        type: "error"
                    }).then(function (result) {
                    });
                } else {
                    $("#reason-leaving-select2").val(null);
                    $("#form-garages").submit();
                }
            } else if ($("#status").val() == 8) {
                if (!$("#reason-leaving-select2").val() && $("#reason-leaving-select2").data('inactive') == false) {
                    swal({
                        title: $.i18n._('Config.Empty_reason'),
                        type: "error"
                    }).then(function (result) {
                    });
                } else if (!$('#comment_reason').val()) {
                    swal({
                        title: $.i18n._('GaragesNetwork.Empty_comment'),
                        type: "error"
                    }).then(function (result) {
                    });
                } else {
                    $("#reason-hold-select2").val(null);
                    $("#form-garages").submit();
                }
            } else {
                $("#form-garages").submit();
            }
        });
    };

    return {
        load: function () {
            removeContact();
            searchTime();
            garageNetworks();
        }
    }

})();