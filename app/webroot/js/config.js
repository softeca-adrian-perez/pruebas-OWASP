$(document).ready(function () {
    Config.load();
    timer = true;
});

var Config = (function () {

    var loadSelects = function () {
        $('#generic-default-js').on('click', function () {
            genericCheck = $('#generic-default-js').hasClass("c-fallo") ? 0 : 1;
            if (genericCheck == 1) {
                $('#region-js').hide();
                $('#role-js').hide();
                $('#modules-config-js').show();
            } else {
                $('#region-js').show();
                $('#role-js').show();
                $('#modules-config-js').hide();
            }
        });
    }

    var submit_changes = function () {
        $('.switch-config').find('input').on('change', function () {
            _submit_change();
        });
    };

    var _submit_change = function () {
        var url = $('#config_form').data('url');
        var data = {};
        $('.switch-config').each(function () {
            data[$(this).find('input').data('config-id')] = $(this).find('input').prop('checked');
        });

        if (timer) {
            timer = false;
            var request = PeticionAjax.post(url, data);
            request.done(function () {
                swal($.i18n._('Constants.Message_well_saved'), '', 'success');
                setTimeout(function () {
                    timer = true;
                }, 500);
            });
        }
    };

    var conditions_toggles = function () {
        $('.section_name').off('click').on('click', function () {
            $('#' + $(this).data('id')).trigger('click');
        });

        $('#all_modules').on('click', function (e, action = true) {
            let id = document.getElementById('all_modules');
            let className = id.className;

            $('.ico-toggle').each(function () {
                if ($(this).data('config-id') >= 62 && action && $(this).hasClass(className)) {
                    change_toggle($(this));
                }
            });
            change_toggle($('.all_modules'));
            if (action) {
                if ($("#config-module-region").val()) {
                    submit_module_change();
                } else {
                    submit_change();
                }
            }
        });

        $('.ico-toggle').on('click', function (e, action = true) {
            if ($(this).data('config-id') == 6 && action) {
                change_toggle($(this));
                change_toggle($('.ico-toggle[data-config-id="5"'));
                $('.ico-toggle[data-config-id="5"').trigger('click', [false]);
            } else if ($(this).data('config-id') == 5 && action) {
                change_toggle($(this));
                change_toggle($('.ico-toggle[data-config-id="6"'));
                $('.ico-toggle[data-config-id="6"').trigger('click', [false]);
            } else if ($(this).data('config-id') == 1 && action) {
                change_toggle($(this));
                if ($(this).hasClass('ion-toggle')) {
                    if ($('.ico-toggle[data-config-id="2"').hasClass('ion-toggle-filled')) {
                        change_toggle($('.ico-toggle[data-config-id="2"'));
                    }
                    if ($('.ico-toggle[data-config-id="3"').hasClass('ion-toggle')) {
                        change_toggle($('.ico-toggle[data-config-id="3"'));
                    }
                } else {
                    if ($('.ico-toggle[data-config-id="3"').hasClass('ion-toggle-filled')) {
                        change_toggle($('.ico-toggle[data-config-id="3"'));
                    }
                }
            } else if ($(this).data('config-id') == 2 && action) {
                change_toggle($(this));
                if ($(this).hasClass('ion-toggle-filled')) {
                    if ($('.ico-toggle[data-config-id="1"').hasClass('ion-toggle')) {
                        change_toggle($('.ico-toggle[data-config-id="1"'));
                    }
                    if ($('.ico-toggle[data-config-id="3"').hasClass('ion-toggle-filled')) {
                        change_toggle($('.ico-toggle[data-config-id="3"'));
                    }
                }
            } else if ($(this).data('config-id') == 3 && action) {
                change_toggle($(this));
                if ($(this).hasClass('ion-toggle-filled')) {
                    if ($('.ico-toggle[data-config-id="1"').hasClass('ion-toggle-filled')) {
                        change_toggle($('.ico-toggle[data-config-id="1"'));
                    }
                    if ($('.ico-toggle[data-config-id="2"').hasClass('ion-toggle-filled')) {
                        change_toggle($('.ico-toggle[data-config-id="2"'));
                    }
                } else {
                    if ($('.ico-toggle[data-config-id="1"').hasClass('ion-toggle')) {
                        change_toggle($('.ico-toggle[data-config-id="1"'));
                    }
                }
            } else if ($(this).data('config-id') == 49 && action) {
                change_toggle($(this));
                change_toggle($('.ico-toggle[data-config-id="50"'));
                $('.ico-toggle[data-config-id="50"').trigger('click', [false]);
            } else if ($(this).data('config-id') == 50 && action) {
                change_toggle($(this));
                change_toggle($('.ico-toggle[data-config-id="49"'));
                $('.ico-toggle[data-config-id="49"').trigger('click', [false]);
            } else if ($(this).data('config-id') == 53 && action) {
                change_toggle($(this));
                if ($(this).hasClass('c-fallo') && $('.ico-toggle[data-config-id="54"').hasClass('c-exito')) {
                    change_toggle($('.ico-toggle[data-config-id="54"'));
                }
            } else if ($(this).data('config-id') == 54 && action) {
                change_toggle($(this));
                if ($(this).hasClass('c-exito') && $('.ico-toggle[data-config-id="53"').hasClass('c-fallo')) {
                    change_toggle($('.ico-toggle[data-config-id="53"'));
                }
            } else {
                if (action) {
                    change_toggle($(this));
                }
            }
            if (action) {
                submit_change();
            }
        });
    };

    var change_toggle = function (toggle) {
        if (toggle.hasClass('ion-toggle-filled')) {
            toggle.removeClass('ion-toggle-filled c-exito').addClass('ion-toggle c-fallo');
        } else if (toggle.hasClass('ion-toggle')) {
            toggle.removeClass('ion-toggle c-fallo').addClass('ion-toggle-filled c-exito');
        }
    }

    var load_toggle = function (element, active) {
        if (active) {
            element.removeClass('ion-toggle c-fallo').addClass('ion-toggle-filled c-exito');
        } else {
            element.removeClass('ion-toggle-filled c-exito').addClass('ion-toggle c-fallo');
        }
    }

    var submit_change = function () {

        var url = $('#config_form').data('url');
        var data = {};

        $('.ico-toggle').each(function () {
            if ($(this).hasClass('ion-toggle')) {
                data[$(this).data('config-id')] = false;
            } else {
                data[$(this).data('config-id')] = true;
            }
        });

        var request = PeticionAjax.post(url, data);
        request.done(function () {
            if (timer) {
                timer = false;
                swal($.i18n._('Constants.Message_well_saved'), '', 'success');
                setTimeout(function () {
                    timer = true;
                }, 500);
            }
        });
    }

    var submit_module_change = function () {

        var url = $('#config_form').data('url-module');
        var data = { aag_region_id: $("#config-module-region").val(), role_id: $("#config-module-role").val() };

        $('.ico-toggle').each(function () {
            if ($(this).hasClass('ion-toggle')) {
                data[$(this).data('config-id')] = false;
            } else {
                data[$(this).data('config-id')] = true;
            }
        });

        var request = PeticionAjax.post(url, data);
        request.done(function (message) {
            if (message == "true") {
                $('#all_modules').removeClass('ion-toggle c-fallo').addClass('ion-toggle-filled c-exito');
            } else {
                $('#all_modules').removeClass('ion-toggle-filled c-exito').addClass('ion-toggle c-fallo');
            }
            if (timer) {
                timer = false;
                swal($.i18n._('Constants.Message_well_saved'), '', 'success');
                setTimeout(function () {
                    timer = true;
                }, 500);
            }
        });
    }

    /**
     *  Changes module status to active.
     */
    var activate_modules = function () {

        $('.activateAction').click(function (event) {
            var url = $('#config_form').data('url-module');
            var data = { aag_region_id: $("#config-module-region").val(), role_id: $("#config-module-role").val() };
            data['config-id'] = $(this).data('config-id');
            data['active'] = 1;
            if (!$(this).hasClass('disabled') && $(this).hasClass('outlined')) {
                var request = PeticionAjax.post(url, data);
                request.done(function (message) {
                    assignStatus();
                    if (timer) {
                        timer = false;
                        swal($.i18n._('Constants.Message_well_saved'), '', 'success');
                        setTimeout(function () {
                            timer = true;
                        }, 500);
                    }
                });
            }
            if ($(this).hasClass('disabled')) {
                swal($.i18n._('Constants.Message_already_active'), '', 'info');
            }
        });
    }

    /**
     *  Changes module status to inactive.
     */
    var deactivate_modules = function () {
        $('.deactivateAction').click(function (event) {
            var url = $('#config_form').data('url-module');
            var data = { aag_region_id: $("#config-module-region").val(), role_id: $("#config-module-role").val() };
            data['config-id'] = $(this).data('config-id');
            data['active'] = 0;
            if (!$(this).hasClass('disabled') && $(this).hasClass('outlined')) {
                var request = PeticionAjax.post(url, data);
                request.done(function (message) {
                    assignStatus();
                    if (timer) {
                        timer = false;
                        swal($.i18n._('Constants.Message_well_saved'), '', 'success');
                        setTimeout(function () {
                            timer = true;
                        }, 500);
                    }
                });
            }
            if ($(this).hasClass('disabled')) {
                swal($.i18n._('Constants.Message_already_inactive'), '', 'info');
            }
        });
    }

    /**
     *  Changes all modules status to active.
     */
    var activate_all_modules = function () {
        $('.activateAllAction').click(function (event) {
            var url = $('#config_form').data('url-all-modules');
            var data = { aag_region_id: $("#config-module-region").val(), role_id: $("#config-module-role").val() };
            data['active'] = 1;
            if (!$(this).hasClass('disabled') && $(this).hasClass('outlined')) {
                var request = PeticionAjax.post(url, data);
                request.done(function (message) {
                    assignStatus();
                    if (timer) {
                        timer = false;
                        swal($.i18n._('Constants.Message_well_saved'), '', 'success');
                        setTimeout(function () {
                            timer = true;
                        }, 500);
                    }
                });
            }
            if ($(this).hasClass('disabled')) {
                swal($.i18n._('Constants.Message_already_active'), '', 'info');
            }
        });
    }

    /**
     *  Changes all modules status to inactive.
     */
    var deactivate_all_modules = function () {
        $('.deactivateAllAction').click(function (event) {
            var url = $('#config_form').data('url-all-modules');
            var data = { aag_region_id: $("#config-module-region").val(), role_id: $("#config-module-role").val() };
            data['active'] = 0;
            if (!$(this).hasClass('disabled') && $(this).hasClass('outlined')) {
                var request = PeticionAjax.post(url, data);
                request.done(function (message) {
                    assignStatus();
                    if (timer) {
                        timer = false;
                        swal($.i18n._('Constants.Message_well_saved'), '', 'success');
                        setTimeout(function () {
                            timer = true;
                        }, 500);
                    }
                });
            }
            if ($(this).hasClass('disabled')) {
                swal($.i18n._('Constants.Message_already_inactive'), '', 'info');
            }
        });
    }

    var selectBehaviour = function () {
        $('.modules').on('change', function () {
            var url = $('#config-module-region').data('url');
            var data = { aag_region_id: $('#config-module-region').val(), role_id: $('#config-module-role').val() };
            var request = PeticionAjax.post(url, data);
            request.done(function (result) {
                resultado = JSON.parse(result);
                if (resultado['aag_region_and_role']) {
                    $('#modules-config-js').show();
                } else {
                    $('#modules-config-js').hide();
                }
                if (resultado['message'] == 'true') {
                    $('#all_modules').removeClass('ion-toggle c-fallo').addClass('ion-toggle-filled c-exito');
                } else {
                    $('#all_modules').removeClass('ion-toggle-filled c-exito').addClass('ion-toggle c-fallo');
                }
                if (resultado['config_modules'].length == 0) {
                    deactivateConfigModuleRegionRoleTabs();
                }
                resultado['config_modules'].forEach(element => {
                    load_toggle($('.ico-toggle[data-config-id="' + element.id + '"'), element.active == 1);
                });
            });
        })
    }

    /**
     *  Sets module status name after is changed.
     */
    var assignStatus = function () {

        var url = $('#config-module-region').data('url-status');
        var url2 = $('#config_form').data('active-config-modules');
        var data = { aag_region_id: $('#config-module-region').val(), role_id: $('#config-module-role').val() };
        if (data['aag_region_id'] != '' && data['role_id'].length !== 0) {
            var request = PeticionAjax.post(url, data);
            var request2 = PeticionAjax.post(url2, data);

            request.done(function (result) {
                resultadoModulosStatus = JSON.parse(result);
                let keys = (Object.keys(resultadoModulosStatus));
                let className = '';
                let message = '';
                elementos = Array;
                elementos = document.getElementsByClassName("moduleStatus");
                activateButtons = document.getElementsByClassName("activateAction");
                deactivateButtons = document.getElementsByClassName("deactivateAction");
                for (i = 0; i < keys.length; i++) {
                    if (resultadoModulosStatus[keys[i]] == 1) {
                        className = 'activeStatus';
                        message = 'Active';
                        activateButtons[i].classList.remove('outlined');
                        activateButtons[i].classList.add('disabled');
                        deactivateButtons[i].classList.add('outlined');
                        deactivateButtons[i].classList.remove('disabled');
                    } else if (resultadoModulosStatus[keys[i]] == 0) {
                        className = 'inactiveStatus';
                        message = 'Inactive';
                        deactivateButtons[i].classList.remove('outlined');
                        deactivateButtons[i].classList.add('disabled');
                        activateButtons[i].classList.add('outlined');
                        activateButtons[i].classList.remove('disabled');
                    } else {
                        className = 'multivalueStatus';
                        message = 'Multivalue';
                        activateButtons[i].classList.add('outlined');
                        deactivateButtons[i].classList.add('outlined');
                        activateButtons[i].classList.remove('disabled');
                        deactivateButtons[i].classList.remove('disabled');
                    }
                    elementos[i].className = "moduleStatus";
                    elementos[i].classList.add(className);
                    elementos[i].innerHTML = message;
                }
            });

            request2.done(function (result) {
                message = JSON.parse(result);
                statusField = document.getElementsByClassName('allModulesStatus');
                className = '';
                activateButton = document.getElementsByClassName("activateAllAction");
                deactivateButton = document.getElementsByClassName("deactivateAllAction");
                if (message == 'true') {
                    className = 'activeStatus';
                    message = 'Active';
                    deactivateButton[0].classList.add('outlined');
                    deactivateButton[0].classList.remove('disabled');
                    activateButton[0].classList.remove('outlined');
                    activateButton[0].classList.add('disabled');
                } else if (message == 'false') {
                    className = 'inactiveStatus';
                    message = 'Inactive';
                    activateButton[0].classList.add('outlined');
                    activateButton[0].classList.remove('disabled');
                    deactivateButton[0].classList.remove('outlined');
                    deactivateButton[0].classList.add('disabled');
                } else {
                    className = 'multivalueStatus';
                    message = 'Multivalue';
                    activateButton[0].classList.add('outlined');
                    deactivateButton[0].classList.add('outlined');
                    activateButton[0].classList.remove('disabled');
                    deactivateButton[0].classList.remove('disabled');
                }
                statusField[0].classList.add(className);
                statusField[0].innerHTML = message;
            });
        }
    }

    var loadStatus = function () {
        $('.modules').on('change', function () {
            assignStatus();
        });
    }

    var deactivateConfigModuleRegionRoleTabs = function () {
        $('.ico-toggle').each(function () {
            if ($(this).data('config-id') >= 62) {
                $(this).removeClass('ion-toggle-filled c-exito').addClass('ico-toggle icono-grande ion-toggle c-fallo');
            }
        });
    }

    var submit_default_config_changes = function () {
        $('.ico-toggle').on('click', function () {
            url = '/config/ajax_save_config';
            var data = {};

            $('.ico-toggle').each(function () {
                if ($(this).hasClass('ion-toggle')) {
                    data[$(this).data('id')] = false;
                } else {
                    data[$(this).data('id')] = true;
                }
            });

            if ($(this).hasClass('default')) {
                var request = PeticionAjax.post(url, data);
                request.done(function (message) {
                    if (timer) {
                        timer = false;
                        swal($.i18n._('Constants.Message_well_saved'), '', 'success');
                        setTimeout(function () {
                            timer = true;
                        }, 500);
                    }
                });
            }
        });
    }

    var loadTabs = function () {

        if ($("#config-list").val() == '') {
            $("#tabla-listas").hide();
            $("#tabla-listas2").hide();
            $("#tabla-listas3").hide();
            $("#tabla-listas4").hide();
            $("#tabla-listas5").hide();
            $("#tabla-listas6").hide();
            $("#addValueButton").hide();
        }

        $("#config-list").on('change', function () {
            loadValues();
        })

        $('.btn-link').on('click', function () {
            $('.restore_default_values-js').hide();
            if ($(this).attr('id') == 'button_data' && !$(this).hasClass('active-button')) {

                $('#button_data').addClass('active-button');
                $('#button_module_access').removeClass('active-button');
                $('#button_config').removeClass('active-button');
                $('#button_lists').removeClass('active-button');
                $('#button_tabs').removeClass('active-button');

                $('#cuerpo-data').show();
                $('#cuerpo-module-access').hide();
                $('#cuerpo-config').hide();
                $('#cuerpo-listas').hide();
                $('#cuerpo-tabs').hide();

                $('.restore_default_values-js').show();

            } else if ($(this).attr('id') == 'button_module_access' && !$(this).hasClass('active-button')) {

                $('#button_data').removeClass('active-button');
                $('#button_module_access').addClass('active-button');
                $('#button_config').removeClass('active-button');
                $('#button_lists').removeClass('active-button');
                $('#button_tabs').removeClass('active-button');

                $('#cuerpo-data').hide();
                $('#cuerpo-module-access').show();
                $('#cuerpo-config').hide();
                $('#cuerpo-listas').hide();
                $('#cuerpo-tabs').hide();

            } else if ($(this).attr('id') == 'button_config' && !$(this).hasClass('active-button')) {

                $('#button_data').removeClass('active-button');
                $('#button_module_access').removeClass('active-button');
                $('#button_config').addClass('active-button');
                $('#button_lists').removeClass('active-button');
                $('#button_tabs').removeClass('active-button');

                $('#cuerpo-data').hide();
                $('#cuerpo-module-access').hide();
                $('#cuerpo-config').show();
                $('#cuerpo-listas').hide();
                $('#cuerpo-tabs').hide();

            } else if ($(this).attr('id') == 'button_lists' && !$(this).hasClass('active-button')) {

                $('#button_data').removeClass('active-button');
                $('#button_module_access').removeClass('active-button');
                $('#button_config').removeClass('active-button');
                $('#button_lists').addClass('active-button');
                $('#button_tabs').removeClass('active-button');

                $('#cuerpo-data').hide();
                $('#cuerpo-module-access').hide();
                $('#cuerpo-config').hide();
                $('#cuerpo-tabs').hide();
                $('#cuerpo-listas').show();

            } else if ($(this).attr('id') == 'button_tabs' && !$(this).hasClass('active-button')) {

                $('#button_data').removeClass('active-button');
                $('#button_module_access').removeClass('active-button');
                $('#button_config').removeClass('active-button');
                $('#button_lists').removeClass('active-button');
                $('#button_tabs').addClass('active-button');

                $('#cuerpo-data').hide();
                $('#cuerpo-module-access').hide();
                $('#cuerpo-config').hide();
                $('#cuerpo-listas').hide();
                $('#cuerpo-tabs').show();
            }
        });
    };

    var loadValuesListener = function () {
        $('#config-list').on('change', function () {
            loadValues();
        })
    }

    var loadValues = function () {
        var url = $('#config-list').data('url');
        var config_list_val = $("#config-list").val();
        var data = {};
        data['list'] = config_list_val;

        var citiesConst = $('#config-list').data('cities');
        var servicesConst = $('#config-list').data('services');
        var orderTypesConst = $('#config-list').data('orders');
        var citiesFromNetworkAgnConst = $('#config-list').data('cities-from-network-agn');
        var citiesFromNetworkGvConst = $('#config-list').data('cities-from-network-gv');
        var citiesFromNetworkGcConst = $('#config-list').data('cities-from-network-gc');
        var servicesDriversFromNetworkAgnConst = $('#config-list').data('services-from-network-agn');
        var servicesDriversFromNetworkGvConst = $('#config-list').data('services-from-network-gv');
        var servicesDriversFromNetworkGcConst = $('#config-list').data('services-from-network-gc');

        var is_cities_from_network = config_list_val == citiesFromNetworkAgnConst || config_list_val == citiesFromNetworkGvConst || config_list_val == citiesFromNetworkGcConst;
        var is_services_drivers_from_network = config_list_val == servicesDriversFromNetworkAgnConst || config_list_val == servicesDriversFromNetworkGvConst || config_list_val == servicesDriversFromNetworkGcConst;

        if (timer) {
            timer = false;
            var request = PeticionAjax.post(url, data);
            request.done(function (result) {
                if (result == '') {
                    $("#tabla-listas").hide();
                    $("#tabla-listas2").hide();
                    $("#tabla-listas3").hide();
                    $("#tabla-listas4").hide();
                    $("#tabla-listas5").hide();
                    $("#tabla-listas6").hide();
                    $("#addValueButton").hide();
                } else {
                    var resultado = JSON.parse(result);

                    if (config_list_val == orderTypesConst) {
                        if (resultado['OrderProduct']) {
                            var orderType = resultado['OrderProduct'];
                            $('#val-name8').empty();
                            $.each(orderType, function (i, v) {
                                $('#val-name8').append($('<option>').text(v).attr('value', i));
                            });
                            delete resultado.OrderProduct;
                        }
                        if (resultado['OrderType']) {
                            var orderType = resultado['OrderType'];
                            $('#val-name7').empty();
                            $.each(orderType, function (i, v) {
                                $('#val-name7').append($('<option>').text(v).attr('value', i));
                            });
                            delete resultado.OrderType;
                        }
                    } else if (is_cities_from_network) {
                        if (resultado['Country']) {
                            var country = resultado['Country'];
                            $('#val-name9').empty();
                            $.each(country, function (i, v) {
                                $('#val-name9').append($('<option>').text(v).attr('value', i));
                            });
                            delete resultado.Country;
                        }

                        $('#val-name10').empty();
                        $("#val-name10").attr("disabled", "disabled");

                        if (resultado['City']) {
                            var city = resultado['City'];
                            $('#val-name11').empty();
                            $.each(city, function (i, v) {
                                $('#val-name11').append($('<option>').text(v).attr('value', i));
                            });
                            delete resultado.City;
                        }
                    } else if (is_services_drivers_from_network) {
                        if (resultado['ServiceDriver']) {
                            var serviceDriver = resultado['ServiceDriver'];
                            $('#val-name12').empty();
                            $.each(serviceDriver, function (i, v) {
                                $('#val-name12').append($('<option>').text(v).attr('value', i));
                            });
                            delete resultado.ServiceDriver;
                        }
                    }
                }

                var text;
                $.each(resultado, function (index, value) {
                    if (
                        $("#config-list").val() != citiesConst && $("#config-list").val() != servicesConst && $("#config-list").val() != orderTypesConst &&
                        !is_cities_from_network && !is_services_drivers_from_network
                    ) {
                        text += '<tr><td><span class="section_name unselectable cursor-pointer">' + value['name'] + '</span></td>';
                        $("#tabla-listas").show();
                        $("#tabla-listas2").hide();
                        $("#tabla-listas3").hide();
                        $("#tabla-listas4").hide();
                        $("#tabla-listas5").hide();
                        $("#tabla-listas6").hide();
                    } else if ($("#config-list").val() == citiesConst) {
                        text += '<tr><td><span class="section_name unselectable cursor-pointer">' + value['name'] + '</span></td>'
                            + '<td><span class="section_name unselectable cursor-pointer">' + value['latitude'] + '</span></td>'
                            + '<td><span class="section_name unselectable cursor-pointer">' + value['longitude'] + '</span></td>'
                            + '<td><span class="section_name unselectable cursor-pointer">' + value['province'] + '</span></td>';
                        $("#tabla-listas").hide();
                        $("#tabla-listas2").show();
                        $("#tabla-listas3").hide();
                        $("#tabla-listas4").hide();
                        $("#tabla-listas5").hide();
                        $("#tabla-listas6").hide();
                    } else if ($("#config-list").val() == servicesConst) {
                        text += '<tr><td><span class="section_name unselectable cursor-pointer">' + value['name'] + '</span></td>'
                            + '<td><span class="section_name unselectable cursor-pointer">' + value['network'] + '</span></td>'
                        $("#tabla-listas").hide();
                        $("#tabla-listas2").hide();
                        $("#tabla-listas3").show();
                        $("#tabla-listas4").hide();
                        $("#tabla-listas5").hide();
                        $("#tabla-listas6").hide();
                    } else if ($("#config-list").val() == orderTypesConst) {
                        text += '<tr><td><span class="section_name unselectable cursor-pointer">' + value['order_type_name'] + '</span></td>'
                            + '<td><span class="section_name unselectable cursor-pointer">' + value['order_product_name'] + '</span></td>'
                        $("#tabla-listas").hide();
                        $("#tabla-listas2").hide();
                        $("#tabla-listas3").hide();
                        $("#tabla-listas4").show();
                        $("#tabla-listas5").hide();
                        $("#tabla-listas6").hide();
                    } else if (is_cities_from_network) {
                        text += '<tr><td><span class="section_name unselectable cursor-pointer">' + value['city_name'] + '</span></td>'
                            + '<td><span class="section_name unselectable cursor-pointer">' + value['province_name'] + '</span></td>'
                            + '<td><span class="section_name unselectable cursor-pointer">' + value['country_name'] + '</span></td>';
                        $("#tabla-listas").hide();
                        $("#tabla-listas2").hide();
                        $("#tabla-listas3").hide();
                        $("#tabla-listas4").hide();
                        $("#tabla-listas5").show();
                        $("#tabla-listas6").hide();
                    } else if (is_services_drivers_from_network) {
                        text += '<tr><td><span class="section_name unselectable cursor-pointer">' + value['name'] + '</span></td>';
                        $("#tabla-listas").hide();
                        $("#tabla-listas2").hide();
                        $("#tabla-listas3").hide();
                        $("#tabla-listas4").hide();
                        $("#tabla-listas5").hide();
                        $("#tabla-listas6").show();
                    }

                    if ($('#cuerpo-listas').data('role-id') != 16 && !is_services_drivers_from_network) {
                        text += '<td class="ta-center"><span class="delete-js aag-icon-papelera c-fallo cursor-pointer" data-key="' + value['id'] + '"></span>';
                        if (!is_cities_from_network) {
                            text += '<span class="aag-icon-editar cursor-pointer" data-key="' + value['id'] + '"></span>';
                        }
                        text += '</td></tr>';
                    }
                });

                $("#tabla-listas > tbody").empty();
                $("#tabla-listas > tbody").append(text);
                $("#tabla-listas2 > tbody").empty();
                $("#tabla-listas2 > tbody").append(text);
                $("#tabla-listas3 > tbody").empty();
                $("#tabla-listas3 > tbody").append(text);
                $("#tabla-listas4 > tbody").empty();
                $("#tabla-listas4 > tbody").append(text);
                $("#tabla-listas5 > tbody").empty();
                $("#tabla-listas5 > tbody").append(text);
                $("#tabla-listas6 > tbody").empty();
                $("#tabla-listas6 > tbody").append(text);

                timer = true;

                prepareModal();
                delValue();
                saveValueData();
                editValueData();
            });
        }

        if ($("#config-list").val() == '' || is_services_drivers_from_network) {
            $("#addValueButton").hide();
            $("#addValueButton").attr("disabled", "disabled");
            $("#addValueButton").addClass("disabled");
        } else {
            $("#addValueButton").show();
            $("#addValueButton").removeAttr("disabled");
            $("#addValueButton").removeClass("disabled");
        }
    }

    var saveValueData = function () {
        $('#saveValueData').on('click', function () {
            if (validateData("#addValue")) {
                var url = $(this).data('url-save');
                var data = {};

                var config_list_val = $("#config-list").val();

                var citiesConst = $('#config-list').data('cities');
                var servicesConst = $('#config-list').data('services');
                var ordersConst = $('#config-list').data('orders');
                var citiesFromNetworkAgnConst = $('#config-list').data('cities-from-network-agn');
                var citiesFromNetworkGvConst = $('#config-list').data('cities-from-network-gv');
                var citiesFromNetworkGcConst = $('#config-list').data('cities-from-network-gc');

                var is_cities_from_network = config_list_val == citiesFromNetworkAgnConst || config_list_val == citiesFromNetworkGvConst || config_list_val == citiesFromNetworkGcConst;

                if (
                    $("#config-list").val() != citiesConst && $("#config-list").val() != servicesConst && $("#config-list").val() != ordersConst &&
                    !is_cities_from_network
                ) {
                    data['name'] = $("#addValue #val-name").val();
                } else if ($("#config-list").val() == citiesConst) {
                    data['name'] = $("#addValue #val-name2").val();
                    data['latitude'] = $("#addValue #val-name3").val();
                    data['longitude'] = $("#addValue #val-name4").val();
                    data['province_id'] = $("#addValue #val-name5").val();
                } else if ($("#config-list").val() == servicesConst) {
                    data['name'] = $("#addValue #val-name").val();
                    data['network_id'] = $("#addValue #val-name6").val();
                } else if ($("#config-list").val() == ordersConst) {
                    data['order_type_id'] = $("#addValue #val-name7").val();
                    data['order_product_id'] = $("#addValue #val-name8").val();
                } else if (is_cities_from_network) {
                    data['country_id'] = $('#addValue #datos9').find('select').val();
                    data['province_id'] = $('#addValue #datos10').find('select').val();
                    data['city_id'] = $('#addValue #datos11').find('select').val();
                }
                data['list_id'] = $("#config-list").val();

                if (timer) {
                    timer = false;
                    var request = PeticionAjax.post(url, data);
                    request.done(function (result) {
                        if (result) {
                            swal({
                                title: $.i18n._('Constants.Message_well_saved'),
                                type: "success"
                            }).then(function () {
                                loadValues();
                                $('#addValue').foundation('close');
                            });
                        } else {
                            swal({
                                title: $.i18n._('Constants.Message_bad_saved'),
                                type: "error"
                            });
                        }
                        timer = true;
                    });
                    request.fail(function () {
                        swal({
                            title: $.i18n._('Constants.Message_bad_saved'),
                            type: "error"
                        });
                    })
                }
            }
        })
    }

    var closeModalAddValueBehaviour = function () {
        $('#addValue').find('.close-modal').on('click', function () {
            loadValues();
        });
    }

    var editValueData = function () {
        $('#editValueData').on('click', function () {
            if (validateData("#editValue")) {
                var url = $(this).data('url-edit');
                var data = {};

                var citiesConst = $('#config-list').data('cities');
                var servicesConst = $('#config-list').data('services');
                var orderTypesConst = $('#config-list').data('orders');

                if ($("#config-list").val() != citiesConst && $("#config-list").val() != servicesConst && $("#config-list").val() != orderTypesConst) {
                    data['list'] = $("#config-list").val();
                    data['value_id'] = $("#editValue #value_id").val();
                    data['name'] = $("#editValue #datos1 #val-name").val();
                } else if ($("#config-list").val() == citiesConst) {
                    data['list'] = $("#config-list").val();
                    data['value_id'] = $("#editValue #value_id").val();
                    data['name'] = $("#editValue #datos2 #val-name2").val();
                    data['latitude'] = $("#editValue #datos3 #val-name3").val();
                    data['longitude'] = $("#editValue #datos4 #val-name4").val();
                    data['province_id'] = $("#editValue #datos5 #val-name5").val();
                } else if ($("#config-list").val() == servicesConst) {
                    data['list'] = $("#config-list").val();
                    data['value_id'] = $("#editValue #value_id").val();
                    data['name'] = $("#editValue #datos1 #val-name").val();
                    data['network_id'] = $("#editValue #datos6 #val-name6").val();
                } else if ($("#config-list").val() == orderTypesConst) {
                    data['list'] = $("#config-list").val();
                    data['value_id'] = $("#editValue #value_id").val();
                    data['order_type_id'] = $("#editValue #datos7 #val-name7").val();
                    data['order_product_id'] = $("#editValue #datos8 #val-name8").val();
                }

                if (timer) {
                    timer = false;
                    var request = PeticionAjax.post(url, data);
                    request.done(function (result) {
                        if (result) {
                            swal({
                                title: $.i18n._('Constants.Message_well_saved'),
                                type: "success"
                            }).then(function (result) {
                                loadValues();
                                $('#editValue').foundation('close');
                            });
                        } else {
                            swal({
                                title: $.i18n._('Constants.Message_bad_saved'),
                                type: "error"
                            }).then(function (result) {

                            });
                        }
                        timer = true;
                    });
                    request.fail(function () {
                        swal({
                            title: $.i18n._('Constants.Message_bad_saved'),
                            type: "error"
                        }).then(function (result) {

                        });
                    })
                }
            }
        })
    }

    var validateData = function (identificador) {
        var config_list_val = $("#config-list").val();

        var citiesConst = $('#config-list').data('cities');
        var ordersConf = $('#config-list').data('orders');
        var citiesFromNetworkAgnConst = $('#config-list').data('cities-from-network-agn');
        var citiesFromNetworkGvConst = $('#config-list').data('cities-from-network-gv');
        var citiesFromNetworkGcConst = $('#config-list').data('cities-from-network-gc');

        var is_cities_from_network = config_list_val == citiesFromNetworkAgnConst || config_list_val == citiesFromNetworkGvConst || config_list_val == citiesFromNetworkGcConst;

        if ($("#config-list").val() != citiesConst && $("#config-list").val() != ordersConf && !is_cities_from_network) {
            if (!$(identificador + " #val-name").val()) {
                swal({
                    title: $.i18n._('Config.Empty_field'),
                    type: "error"
                });
                return false;
            }
            return true;
        } else if ($('#config-list').val() == citiesConst) {
            if (!$(identificador + " #val-name2").val()) {
                swal({
                    title: $.i18n._('Config.Empty_field'),
                    type: "error"
                });
                return false;
            }
            return true;
        } else if ($('#config-list').val() == ordersConf) {
            if (!$(identificador + " #val-name7").val() || !$(identificador + " #val-name8").val()) {
                swal({
                    title: $.i18n._('Config.Empty_field'),
                    type: "error"
                });
                return false;
            }
            return true;
        } else if (is_cities_from_network) {
            if (!$(identificador + " #datos11").find('select').val()) {
                swal({
                    title: $.i18n._('Config.Empty_field'),
                    type: "error"
                });
                return false;
            }
            return true;
        }
    }

    var delValue = function () {
        $('#cuerpo-listas span.aag-icon-papelera').on('click', function () {
            var key = $(this).data('key');
            swal({
                title: $.i18n._('Config.Confirm_delete_value'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
                    var url = $("#editValueData").data('url-del');
                    var data = {};
                    data['list'] = $("#config-list").val();
                    data['value_id'] = key;
                    var request = PeticionAjax.post(url, data);
                    request.done(function (result) {
                        if (result == 1) {
                            swal({
                                title: $.i18n._('Constants.Message_well_deleted'),
                                type: "success"
                            }).then(function (result) {
                                loadValues();
                            });
                        } else if (result == 2) {
                            swal({
                                title: $.i18n._('Config.Error_delete_item_in_other_list'),
                                type: "error"
                            }).then(function (result) {
                                loadValues();
                            });
                        } else if (result == 0) {
                            swal({
                                title: $.i18n._('Config.Error_delete_item_in_use'),
                                type: "error"
                            }).then(function (result) {
                                loadValues();
                            });
                        } else {
                            swal({
                                title: $.i18n._('Constants.Message_bad_deleted'),
                                type: "error"
                            }).then(function (result) {

                            });
                        }
                    });
                    request.fail(function () {
                        swal({
                            title: $.i18n._('Constants.Message_bad_deleted'),
                            type: "error"
                        }).then(function (result) {

                        });
                    })
                }
            });
        });
    }

    var prepareModal = function () {
        var config_list_val = $("#config-list").val();

        var citiesConst = $('#config-list').data('cities');
        var servicesConst = $('#config-list').data('services');
        var ordersConst = $('#config-list').data('orders');
        var citiesFromNetworkAgnConst = $('#config-list').data('cities-from-network-agn');
        var citiesFromNetworkGvConst = $('#config-list').data('cities-from-network-gv');
        var citiesFromNetworkGcConst = $('#config-list').data('cities-from-network-gc');

        var is_cities_from_network = config_list_val == citiesFromNetworkAgnConst || config_list_val == citiesFromNetworkGvConst || config_list_val == citiesFromNetworkGcConst;

        $("#addValueButton").on('click', function () {
            if (!$(this).attr("disabled")) {

                $("#addValue modal-body").each(function () {
                    $(this).addClass("d-none");
                });

                if (
                    $("#config-list").val() != citiesConst && $("#config-list").val() != servicesConst && $("#config-list").val() != ordersConst &&
                    !is_cities_from_network
                ) {
                    $("#addValue #datos1 #val-name").val("");
                    $("#addValue #datos1").removeClass("d-none");
                    $("#addValue #datos2").addClass("d-none");
                    $("#addValue #datos3").addClass("d-none");
                    $("#addValue #datos4").addClass("d-none");
                    $("#addValue #datos5").addClass("d-none");
                    $("#addValue #datos6").addClass("d-none");
                    $("#addValue #datos7").addClass("d-none");
                    $("#addValue #datos8").addClass("d-none");
                    $("#addValue #datos9").addClass("d-none");
                    $("#addValue #datos10").addClass("d-none");
                    $("#addValue #datos11").addClass("d-none");

                } else if ($("#config-list").val() == citiesConst) {
                    $("#addValue #datos1").addClass("d-none");
                    $("#addValue #datos2 #val-name2").val("");
                    $("#addValue #datos2").removeClass("d-none");
                    $("#addValue #datos3 #val-name3").val("");
                    $("#addValue #datos3").removeClass("d-none");
                    $("#addValue #datos4 #val-name4").val("");
                    $("#addValue #datos4").removeClass("d-none");
                    $("#addValue #datos5 #val-name5").val("");
                    $("#addValue #datos5").removeClass("d-none");
                    $("#addValue #datos6").addClass("d-none");
                    $("#addValue #datos7").addClass("d-none");
                    $("#addValue #datos8").addClass("d-none");
                    $("#addValue #datos9").addClass("d-none");
                    $("#addValue #datos10").addClass("d-none");
                    $("#addValue #datos11").addClass("d-none");

                } else if ($("#config-list").val() == servicesConst) {
                    $("#addValue #datos1 #val-name").val("");
                    $("#addValue #datos1").removeClass("d-none");
                    $("#addValue #datos2").addClass("d-none");
                    $("#addValue #datos3").addClass("d-none");
                    $("#addValue #datos4").addClass("d-none");
                    $("#addValue #datos5").addClass("d-none");
                    $("#addValue #datos6 #val-name6").val("");
                    $("#addValue #datos6").removeClass("d-none");
                    $("#addValue #datos7").addClass("d-none");
                    $("#addValue #datos8").addClass("d-none");
                    $("#addValue #datos9").addClass("d-none");
                    $("#addValue #datos10").addClass("d-none");
                    $("#addValue #datos11").addClass("d-none");

                } else if ($("#config-list").val() == ordersConst) {
                    $("#addValue #datos1").addClass("d-none");
                    $("#addValue #datos2").addClass("d-none");
                    $("#addValue #datos3").addClass("d-none");
                    $("#addValue #datos4").addClass("d-none");
                    $("#addValue #datos5").addClass("d-none");
                    $("#addValue #datos6").addClass("d-none");
                    $("#addValue #datos7 #val-name7").val("").trigger('change');
                    $("#addValue #datos7").removeClass("d-none");
                    $("#addValue #datos8 #val-name8").val("").trigger('change');
                    $("#addValue #datos8").removeClass("d-none");
                    $("#addValue #datos9").addClass("d-none");
                    $("#addValue #datos10").addClass("d-none");
                    $("#addValue #datos11").addClass("d-none");

                } else if (is_cities_from_network) {
                    $("#addValue #datos1").addClass("d-none");
                    $("#addValue #datos2").addClass("d-none");
                    $("#addValue #datos3").addClass("d-none");
                    $("#addValue #datos4").addClass("d-none");
                    $("#addValue #datos5").addClass("d-none");
                    $("#addValue #datos6").addClass("d-none");
                    $("#addValue #datos7").addClass("d-none");
                    $("#addValue #datos8").addClass("d-none");
                    $("#addValue #datos9 #val-name9").val("").trigger('change');
                    $("#addValue #datos9").removeClass("d-none");
                    $("#addValue #datos10 #val-name10").val("").trigger('change');
                    $("#addValue #datos10").removeClass("d-none");
                    $("#addValue #datos11 #val-name11").val("").trigger('change');
                    $("#addValue #datos11").removeClass("d-none");
                }

                $('#addValue').foundation('open');
            }
        });

        $("#cuerpo-listas span.aag-icon-editar").on('click', function () {
            $("#editValue modal-body").each(function () {
                $(this).addClass("d-none");
            });

            var url = $('#editValueData').data('url-get-data');
            var data = {};
            data['list'] = $("#config-list").val();
            data['value_id'] = $(this).data("key");
            $("#editValue #value_id").val($(this).data("key"));

            var request = PeticionAjax.post(url, data);
            request.done(function (result) {
                resultado = JSON.parse(result);
                if (resultado['OrderProduct']) {
                    var orderType = resultado['OrderProduct'];
                    $('#editValue #datos8 #val-name8').empty();
                    $.each(orderType, function (i, v) {
                        $('#editValue #datos8 #val-name8').append($('<option>').text(v).attr('value', i));
                    });
                    delete resultado.OrderProduct;
                };
                if (resultado['OrderType']) {
                    var orderType = resultado['OrderType'];
                    $('#editValue #datos7 #val-name7').empty();
                    $.each(orderType, function (i, v) {
                        $('#editValue #datos7 #val-name7').append($('<option>').text(v).attr('value', i));
                    });
                    delete resultado.OrderType;
                };
                if ($("#config-list").val() != citiesConst && $("#config-list").val() != servicesConst && $("#config-list").val() != ordersConst) {
                    $("#editValue #datos1 #val-name").val(resultado['name']);
                    $("#editValue #datos1").removeClass("d-none");
                    $("#editValue #datos2").addClass("d-none");
                    $("#editValue #datos3").addClass("d-none");
                    $("#editValue #datos4").addClass("d-none");
                    $("#editValue #datos5").addClass("d-none");
                    $("#editValue #datos6").addClass("d-none");
                    $("#editValue #datos7").addClass("d-none");
                    $("#editValue #datos8").addClass("d-none");
                } else if ($("#config-list").val() == citiesConst) {
                    $("#editValue #datos1").addClass("d-none");
                    $("#editValue #datos2 #val-name2").val(resultado['name']);
                    $("#editValue #datos2").removeClass("d-none");
                    $("#editValue #datos3 #val-name3").val(resultado['latitude']);
                    $("#editValue #datos3").removeClass("d-none");
                    $("#editValue #datos4 #val-name4").val(resultado['longitude']);
                    $("#editValue #datos4").removeClass("d-none");
                    $("#editValue #datos5 #val-name5").val(resultado['province_id']).trigger('change');
                    $("#editValue #datos5").removeClass("d-none");
                    $("#editValue #datos6").addClass("d-none");
                    $("#editValue #datos7").addClass("d-none");
                    $("#editValue #datos8").addClass("d-none");
                } else if ($("#config-list").val() == servicesConst) {
                    $("#editValue #datos1 #val-name").val(resultado['name']);
                    $("#editValue #datos1").removeClass("d-none");
                    $("#editValue #datos2").addClass("d-none");
                    $("#editValue #datos3").addClass("d-none");
                    $("#editValue #datos4").addClass("d-none");
                    $("#editValue #datos5").addClass("d-none");
                    $("#editValue #datos6 #val-name6").val(resultado['network_id']).trigger('change');
                    $("#editValue #datos6").removeClass("d-none");
                    $("#editValue #datos7").addClass("d-none");
                    $("#editValue #datos8").addClass("d-none");
                } else if ($("#config-list").val() == ordersConst) {
                    $("#editValue #datos1").addClass("d-none");
                    $("#editValue #datos2").addClass("d-none");
                    $("#editValue #datos3").addClass("d-none");
                    $("#editValue #datos4").addClass("d-none");
                    $("#editValue #datos5").addClass("d-none");
                    $("#editValue #datos6").addClass("d-none");
                    $("#editValue #datos7 #val-name7").val(resultado['order_type_id']).trigger('change');
                    $("#editValue #datos7").removeClass("d-none");
                    $("#editValue #datos8 #val-name8").val(resultado['order_product_id']).trigger('change');
                    $("#editValue #datos8").removeClass("d-none");
                }
                $('#editValue').foundation('open');
            });
        });
    }

    return {
        load: function () {
            conditions_toggles();
            submit_changes();
            loadTabs();
            loadValuesListener();
            saveValueData();
            editValueData();
            prepareModal();
            selectBehaviour();
            deactivateConfigModuleRegionRoleTabs();
            submit_default_config_changes();
            loadSelects();
            closeModalAddValueBehaviour();
            loadStatus();
            activate_modules();
            activate_all_modules();
            deactivate_all_modules();
            deactivate_modules();
        }
    }

})();