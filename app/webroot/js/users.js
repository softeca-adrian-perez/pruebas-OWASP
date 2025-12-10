$(document).ready(function () {
    if ($('#positions_bdm_id').val() != undefined && $('#positions_bdm_id').val() != '') {
        array_list_positions = JSON.parse($('#positions_bdm_id').val());
    } else {
        array_list_positions = '';
    }
    User.load();
});

var User = (function () {
    var parent = function () {
        var position = $('#position');
        var parent = $('#parent');
        var garage_distributor_type = $('#garage_distributor_type');
        if (Object.values(array_list_positions).indexOf(position.val()) > -1) {
            parent.show();
            garage_distributor_type.show();
        } else {
            parent.hide();
            garage_distributor_type.hide();
        }

        if (position.val() != '') {
            positionBehaviour(position.val(), garage_distributor_type, parent);
        }

        position.change(function () {
            positionBehaviour(position.val(), garage_distributor_type, parent);
        });
    };

    var positionBehaviour = function (position_val, garage_distributor_type, parent) {
        if (Object.values(array_list_positions).indexOf(position_val) > -1) {
            $('#garage_type').prop('disabled', false);
            $('#distributor_type').prop('disabled', false);
            garage_distributor_type.show();
            parent.show();
        } else {
            $('#parent_select').val('0').trigger('change');
            $('#garage_type').prop('disabled', true);
            $('#distributor_type').prop('disabled', true);
            parent.hide();
            garage_distributor_type.hide();
        }

        if (position_val == 32 || position_val == 33) {
            $('#country-select').prop('disabled', true);
        } else {
            $('#country-select').prop('disabled', false);
        }

        if (position_val != 1 && position_val != 29 && position_val != 31 && position_val != 38) {
            $('#country-select option[value="-2"]').remove().trigger('change');
        } else {
            if ($('#country-select option[value="-2"]').length == 0) {
                var newOption = new Option('All', -2, false, false);
                $('#country-select').append(newOption).trigger('change');
            }
        }

        garage();
    }

    var garage = function () {
        var position = $('#position');
        var garage = $('#garage');
        var action_form = $('#action_form');
        var garage_select = $('#garage_select');
        var country_select = $('#country-select');

        setTimeout(function () {
            var url = garage_select.data('url');
            garage_select.select2({
                minimumInputLength: 2,
                language: {
                    inputTooShort: function (args) {
                        return $.i18n._('General.Enter_at_least_two_characters');
                    },
                    noResults: function (args) {
                        return $.i18n._('General.No_results_found');
                    },
                    searching: function (args) {
                        return $.i18n._('General.Searching');
                    },
                    errorLoading: function () {
                        return $.i18n._('General.The_results_could_not_be_loaded');
                    },
                    loadingMore: function () {
                        return $.i18n._('General.Loading_more_results');
                    },
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (obj) {
                                return {
                                    id: obj.Garage.id, text: obj.Garage.complete_name
                                };
                            }),
                            pagination: {
                                more: data.length >= 10
                            }
                        };
                    }
                }
            });
        }, 0);
        if (position.val() == 32 || ($('#position :selected').parent().attr('label') == 'Generic Staff' && action_form.data('param3') == 'garages')) {
            garage.show();
            if (action_form.data('param1') == 'add_contacts_staff') {
                garage_select.val(action_form.data('param2')).trigger('change');
                garage_select.prop("disabled", true);
            }
            if (action_form.data('param1') == 'add_contact_and_user' && action_form.data('param2') != '') {
                garage_select.val(action_form.data('param2')).trigger('change');
                garage_select.prop("disabled", true);
                country_select.prop("disabled", true);
            }
        } else {
            garage.hide();
        }

        position.change(function () {
            if (position.val() == 32) {
                garage.show();
                if (action_form.data('param1') == 'add_contacts_staff') {
                    garage_select.val(action_form.data('param2')).trigger('change');
                    garage_select.prop("disabled", true);
                }
                if (action_form.data('param1') == 'add_contact_and_user' && action_form.data('param2') != '') {
                    garage_select.val(action_form.data('param2')).trigger('change');
                    garage_select.prop("disabled", true);
                }
            } else if ($('#position :selected').parent().attr('label') == 'Generic Staff' && action_form.data('param3') != 'distributors') {
                if (action_form.data('param1') == 'add_contacts_staff') {
                    garage.show();
                    garage_select.val(action_form.data('param2')).trigger('change');
                    garage_select.prop("disabled", true);
                } else if (action_form.data('param1') == 'add_contact_and_user' && action_form.data('param2') != '') {
                    garage_select.val(action_form.data('param2')).trigger('change');
                    garage_select.prop("disabled", true);
                    country_select.prop("disabled", true);
                }
            } else {
                garage_select.val('0').trigger('change');
                garage.hide();
            }
        });

        garage_select.change(function () {
            var country_select = $('#country-select');
            if (garage_select.val() != 0 && garage_select.val() != null && country_select.prop('disabled')) {
                var country_select = $('#country-select');
                var country_select_hidden = $('#country-select-hidden');
                var url = garage_select.data('url-country');
                var data = {};

                data['customer_id'] = garage_select.val();
                var request = PeticionAjax.post(url, data);
                request.done(function (data) {
                    if (data) {
                        country_select.val(data).trigger('change');
                        country_select_hidden.val(data);
                    }
                });
            }
        });
    };

    var distributor = function () {
        var position = $('#position');
        var distributor = $('#distributor');
        var distributor_select = $('#distributor_select');
        var action_form = $('#action_form');

        setTimeout(function () {
            var url = distributor_select.data('url');
            distributor_select.select2({
                minimumInputLength: 2,
                language: {
                    inputTooShort: function (args) {
                        return $.i18n._('General.Enter_at_least_two_characters');
                    },
                    noResults: function (args) {
                        return $.i18n._('General.No_results_found');
                    },
                    searching: function (args) {
                        return $.i18n._('General.Searching');
                    },
                    errorLoading: function () {
                        return $.i18n._('General.The_results_could_not_be_loaded');
                    },
                    loadingMore: function () {
                        return $.i18n._('General.Loading_more_results');
                    },
                },
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (obj) {
                                return {
                                    id: obj.Distributor.id, text: obj.Distributor.complete_name
                                };
                            }),
                            pagination: {
                                more: data.length >= 10
                            }
                        };
                    }
                }
            })
        }, 0);

        if (position.val() == 33 || $('#position :selected').parent().attr('label') == 'Generic Staff' && action_form.data('param3') == 'distributors') {
            distributor.show();
            if (action_form.data('param1') == 'add_contacts_staff') {
                distributor_select.val(action_form.data('param2')).trigger('change');
                distributor_select.prop("disabled", true);
            }
        } else {
            distributor.hide();
        }

        position.change(function () {
            if (position.val() == 33 || $('#position :selected').parent().attr('label') == 'Generic Staff' && action_form.data('param3') == 'distributors') {
                distributor.show();
                if (action_form.data('param1') == 'add_contacts_staff') {
                    distributor_select.val(action_form.data('param2')).trigger('change');
                    distributor_select.prop("disabled", true);
                }
            } else {
                distributor_select.val('0').trigger('change');
                distributor.hide();
            }
        });

        distributor_select.change(function () {
            var country_select = $('#country-select');
            if (distributor_select.val() != 0 && distributor_select.val() != null && country_select.prop('disabled')) {
                var country_select = $('#country-select');
                var country_select_hidden = $('#country-select-hidden');
                var url = distributor_select.data('url-country');
                var data = {};

                data['customer_id'] = distributor_select.val();
                var request = PeticionAjax.post(url, data);
                request.done(function (data) {
                    if (data) {
                        country_select.val(data).trigger('change');
                        country_select_hidden.val(data);
                    }
                });
            }
        });
    };

    var delete_contact = function () {
        $('#delete-contact').on('click', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            if ($(this).data('delete')) {
                swal({
                    title: $.i18n._('Contact.Delete_contact?'),
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: $.i18n._('Alert.Confirm_delete'),
                    cancelButtonText: $.i18n._('General.No')
                }).then(function (result) {
                    if (result.value) {
                        window.location = url;
                    }
                });
            } else {
                swal($.i18n._('General.Error'), $.i18n._('Contact.Cant_delete'), 'error');
            }
        });
    };

    var region = function () {
        var position = $('#position');
        var region = $('#simple-region');

        if (Object.values(array_list_positions).indexOf(position.val()) > -1) {
            region.show();
        } else {
            region.hide();
        }

        position.change(function () {
            if (Object.values(array_list_positions).indexOf(position.val()) > -1) {
                region.show();
            } else {
                $('#parent_select').val('0').trigger('change');
                region.hide();
            }
        });

    };

    var regions = function () {
        var position = $('#position');
        var regions_cv = $('#multiple-region-cv');
        var regions_lv = $('#multiple-region-lv');

        if (position.val() == 20) {
            regions_cv.show();
            regions_lv.hide();
            regions_cv.find('.multiple-region').prop('disabled', false);
            regions_lv.find('.multiple-region').prop('disabled', true);
        } else if (position.val() == 21) {
            regions_cv.hide();
            regions_lv.show();
            regions_cv.find('.multiple-region').prop('disabled', true);
            regions_lv.find('.multiple-region').prop('disabled', false);
        } else {
            regions_cv.hide();
            regions_lv.hide();
            regions_cv.find('.multiple-region').prop('disabled', true);
            regions_lv.find('.multiple-region').prop('disabled', true);
        }

        position.change(function () {
            if (position.val() == 20) {
                regions_cv.show();
                regions_lv.hide();
                regions_cv.find('.multiple-region').prop('disabled', false);
                regions_lv.find('.multiple-region').prop('disabled', true);
            } else if (position.val() == 21) {
                regions_cv.hide();
                regions_lv.show();
                regions_cv.find('.multiple-region').prop('disabled', true);
                regions_lv.find('.multiple-region').prop('disabled', false);
            } else {
                $('#parent_select').val('0').trigger('change');
                regions_cv.hide();
                regions_lv.hide();
                regions_cv.find('.multiple-region').prop('disabled', true);
                regions_lv.find('.multiple-region').prop('disabled', true);
            }
        });
    };

    var contact_networks = function () {
        var position = $('#position');
        var contact_networks = $('#contact_networks_form');

        if (Object.values(array_list_positions).indexOf(position.val()) > -1) {
            contact_networks.show();
        } else {
            contact_networks.hide();
        }

        position.change(function () {
            if (Object.values(array_list_positions).indexOf(position.val()) > -1) {
                contact_networks.show();
                $("#selected_network").prop("disabled", false);
                $("#selected_distributor_network").prop("disabled", false);
            } else {
                contact_networks.hide();
                $("#selected_network").prop("disabled", true);
                $("#selected_distributor_network").prop("disabled", true);
            }
        });
    };

    var sendData = function () {
        $("#btn-guardar").on('click', function (e) {
            if ($('#region-select').length > 0) {
                e.preventDefault();
                $('#region-select').attr('disabled', false);
                $('#form').submit();
            }
        });
    }

    var recover_password = function () {
        $(".recover_password-js").on('click', function (e) {
            e.preventDefault();
            var element = $(this);
            var url = element.data('url');
            var url_redirect = element.data('url_redirect');
            var confirmmsg = element.data('confirmmsg');
            var msg_correct = element.data('msg_correct');
            var msg_bad = element.data('msg_bad');
            swal({
                title: confirmmsg,
                type: "info",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.postJSON(url);
                    request.done(function (data) {
                        if (data.precess == 'true') {
                            swal({ type: "success", title: msg_correct }).then(function () {
                                window.location.replace(url_redirect);
                            })
                        } else {
                            swal({ type: "error", title: msg_bad, text: data.error_text })
                        }
                    });
                }
            });
        });
    };

    var country = function () {
        var region = $("#region-select");
        var country_select = $("#country-select");
        var url = region.data('url');
        var data = {};

        region.change(function () {
            data['aag_region_id'] = region.val();
            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                if (data) {
                    country_select.empty().trigger('change');
                    resultado = JSON.parse(data);
                    $.each(resultado, function (index, value) {
                        info = {
                            id: index,
                            text: value
                        };
                        var newOption = new Option(info.text, info.id, false, false);
                        country_select.append(newOption).trigger('change');
                    });
                }
            });
        });
    }

    return {
        load: function () {
            parent();
            garage();
            distributor();
            delete_contact();
            region();
            regions();
            contact_networks();
            sendData();
            recover_password();
            country();
        }
    }
})();
