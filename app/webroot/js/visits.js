$(document).ready(function () {
    document.getElementById("show_result").addEventListener("click", function (event) {
        event.preventDefault();
    });
    Visit.load();


    var load = false;
    var $route = $('#route_id');
    $(document).ajaxComplete(function () {
        if (!load && $route.val() != '') {
            $('#search_route').val($route.val()).trigger('change');
            $('#show_result').trigger('click');
            $("html, body").stop();
            load = true;
        }
    });
});

var Visit = (function () {

    var loadBehaviour = function () {
        $('#add_garage').on('click', function () {
            var page = $("html, body");
            if ($("#cnt-added-garages").length > 0) {
                page.animate({ scrollTop: $("#cnt-added-garages").offset().top - 96 }, 2000, function () {
                    page.off("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove");
                });
            }
            page.on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function () {
                page.stop();
            });
            var counter_added = 1;
            var last_child = '';
            var table_not_empty = $('#table-added-garages').children().length > 0;
            // if table wasn't empty
            if (table_not_empty) {
                last_child = $('#table-added-garages tr:last-child').find('.input_time').first();
            }
            $('.garage-result').each(function () {
                var $row_garage = $(this);
                if ($row_garage.find('.check-garage').prop('checked') == true) {
                    var garage_added = $row_garage.clone();
                    var duplicated = false;
                    garage_added.removeClass('garage-result');
                    garage_added.addClass('garage_added');
                    garage_added.find('.check-td').remove();
                    garage_added.find('.td-time').removeClass("d-none");
                    garage_added.attr('data-id', garage_added.attr('id').replace('garage-', ''));
                    garage_added.attr('id', garage_added.attr('id') + '-added');
                    $('.garage_added').each(function () {
                        if ($(this).attr('id') == garage_added.attr('id')) {
                            duplicated = true;
                        }
                    });
                    if (garage_added.data('lat') == '' && garage_added.data('lng') == '') {
                        swal($.i18n._('Visit.Garage_no_location'), $.i18n._('Visit.Will_not_be_add'), 'warning');
                    } else if (!duplicated) {
                        //garage_added.prepend('<td class="ta-center order-td">' + counter_added + '</td>');
                        garage_added.append('' +
                            '<td class="ta-center">' +
                            '<a class="remove-garage" data-id="' + garage_added.attr('id') + '">' +
                            '<span class="aag-icon-papelera c-fallo"></span>' +
                            '</a>' +
                            '</td>');
                        garage_added.hide().appendTo("#table-added-garages").fadeIn();
                        if (!table_not_empty && counter_added == 1) {
                            last_child = $('#table-added-garages tr:last-child').find('.input_time').first();
                        }
                        counter_added++;
                    }
                }
            });
            $('.timepicker').each(function () {
                $(this).timepicker();
            });
            sortTableList();
            loadRemoveGarage();
            setOrder();
            //setOrderTime();
            setOrderTimeByStart();
            setOrderTimeByStartCurrentElement(last_child);
            GMaps.drawRoute();
        });
    };

    var loadRemoveGarage = function () {
        $('.remove-garage').off('click').on('click', function (event, type, last) {
            var tr_start = $(this).closest('tr');
            var tr_prev = tr_start.prev();
            $('#' + $(this).data('id')).fadeOut(function () {
                $(this).remove();
                if (!type || (type && last)) {
                    GMaps.drawRoute();
                }
            });
            setOrder();
            //setOrderTime();
            setOrderTimeByStart();
            setTimeout(function () {
                tr_prev.find('.input_time').last().trigger('blur');
            }, 250);
        });
    };

    var searchRouteMap = function () {
        $('#search_route_map').on('click', function () {
            $('.clear_field').each(function () {
                if ($(this).attr('type') == 'checkbox') {
                    $(this).prop('checked', false);
                } else if (typeof ($(this).attr('multiple')) != 'undefined') {
                    $(this).val('0').select2();
                } else {
                    $(this).val('').trigger('change');
                }
            });
            $('#search_route').val($('#search_route_map_select').val()).trigger('change');
            $('#loaded_route').attr('data-id', $("#search_route_map_select").val());
            if ($('#search_route_map_select').val() == '') {
                $('#loaded_route').html('');
                $('#edit_route').addClass('d-none');
                $('#delete_all_garages_added').trigger('click');
            } else {
                $('#edit_route').removeClass('d-none');
                $('#loaded_route').html($.i18n._('Route.Route_loaded') + $("#search_route_map_select :selected").text());
            }
            setTimeout(function () {
                $('#show_result').trigger('click', [true]);
            }, 500);
        });
    }

    var selectDinamic = function () {
        $('.select2Dinamico').select2({
            placeholder: "",
            allowClear: true,
            tags: true
        });
    };

    var cargar_distributors = function () {
        $('.cargar_distributors').select2({
            ajax: {
                beforeSend: ocultarCargando(),
                url: '/distributors/get_distributors_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    var query = {
                        name: params.term,
                        status: 2
                    }
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 3,
            placeholder: $('.cargar_distributors').data('texto1'),
        });
    };

    var ocultarCargando = function () {
        $('.preloader').remove();
    };


    var searchVisits = function () {
        $('#show_result').on('click', function (e, route) {
            var route = route || false;
            var page = $("html, body");
            page.animate({ scrollTop: $("#search_results").offset().top - 96 }, 2000, function () {
                page.off("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove");
            });
            page.on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function () {
                page.stop();
            });

            $('.check-garage').each(function () {
                $(this).prop('checked', false);
            });
            var $distance = $('#search_distance');
            if ($distance.val() == '' && $('#autocomplete-address').val() != '') {
                $distance.val($distance.find('option').eq(1).val()).trigger('change');
            }
            var url = $('#search').data('url');
            var data = {};
            data.town = $('#search_city').val();
            data.location = $('#autocomplete-address').val();
            data.lat = $('#latitude-localization').val();
            data.lng = $('#longitude-localization').val();
            data.distance = $distance.val();
            data.latest_visit = $('#search_last_visit').val();
            data.network = $('#search_network').val();
            data.network_status = $('#search_network_status').val();
            data.distributor = $('#search_distributor').val();
            data.route = $('#search_route').val();
            data.contact = $('#search_contact').val();
            data.region = $('#region-id').val();
            data.my_customers = $('#search_my_customers').prop('checked');

            var request = PeticionAjax.post(url, data);
            request.done(function (res) {
                $('#results_table_container').html(res);
                paginateResults();
                markUnmark();
                deleteAllGaragesAdded();
                $('table.tabla-responsive, .table-tracking').basictable();
                if (data.route != '' && route == true) {
                    var url_ = $('#ajax_search_route').data('url');
                    var request_ = PeticionAjax.post(url_, data);
                    request_.done(function (res) {
                        $('#table-added-garages').html(res);
                        $('.timepicker').each(function () {
                            $(this).timepicker();
                        });
                        sortTableList();
                        loadRemoveGarage();
                        setOrder();
                        setOrderTimeByStart();
                        GMaps.drawRoute();
                    });
                }
            });
        });
    };

    var paginateResults = function () {
        $.fn.dataTable.moment('DD-MM-YYYY');
        $('#visit-table').DataTable({
            oLanguage: {
                sUrl: "../../js/lib/dataTables/locale/" + language_code + ".json"
            },
            autoWidth: false,
            deferRender: true,
            order: [[5, "desc"]],
            columnDefs: [{ orderable: false, targets: 0 }],
            drawCallback: function () {
                $('.paginate_button', this.api().table().container()).on('click', function () {
                    $('#mark_unmark_all').prop('checked', false);
                    $('.check-distributor').prop('checked', false);
                });
            }
        });
        $('#visit-table_info').append($.i18n._('Visit.Search_limit'));
        $('.garage-result').on('click', function (e) {
            if (e.target.nodeName === "INPUT") return;
            $(this).find('.check-garage').trigger('click');
        });
    };

    var generateVisits = function () {
        var $date = $('#date-visit');
        var users = {};
        $("#users_list").find("option").each(function () {
            if ($(this).val() != '') {
                users[$(this).val()] = $(this).text();
            }
        });
        $('#show_calendar').on('click', function () {
            if ($('.garage_added').length > 0) {
                swal({
                    title: $.i18n._('Visit.Assign_to_user'),
                    text: $.i18n._('Visit.Assign_to_user'),
                    input: 'select',
                    inputOptions: users,
                    inputValue: $('#calendar-visits').data('user_assigned_id'),
                    showCancelButton: true,
                    inputPlaceholder: $.i18n._('Visit.Select_user'),
                    showCloseButton: true,
                    inputValidator: function (value) {
                        return new Promise(function (resolve) {
                            if (value === false) {
                                resolve();
                            } else if (value === "") {
                                resolve($.i18n._('Visit.Select_user'));
                                return false
                            } else {
                                $('#calendar-visits').data('user_assigned_id', value);
                                resolve();
                                let modalCalendar = new Foundation.Reveal($('#modal-calendar'));
                                modalCalendar.open();
                            }
                        })
                    }
                });
                $('.swal2-select').select2();
            } else {
                swal($.i18n._('Visit.Select_garage'), '', 'error');
            }
        });

        $('#generate_visit').on('click', function () {
            var data = {};
            var url = $(this).data('url');
            data.date = $date.val();
            var counter = 0;
            $('.garage_added').each(function () {
                data[counter] = {};
                data[counter]['garage_id'] = $(this).data('id');
                data[counter]['start_time'] = $(this).find('.start_time').val();
                data[counter]['end_time'] = $(this).find('.end_time').val();
                data[counter]['user_assigned_id'] = $('#calendar-visits').data('user_assigned_id');
                counter++;
            });

            var request = PeticionAjax.post(url, data);
            request.done(function () {
                var $calendar_visits = $('#calendar-visits');
                $calendar_visits.fullCalendar('rerenderEvents');
                $('#delete_all_garages_added').trigger('click');
                $('#table-added-garages').fadeOut(function () {
                    $(this).html('');
                    $(this).show();
                });

                setTimeout(function () {
                    swal($.i18n._('Visit.Visit_added'), '', 'success');
                }, 500);

            });
            request.fail(function () {
                swal({
                    title: $.i18n._('Constants.Error_alert_general'),
                    type: "error"
                });
            });
        });
    };

    /**
     * Sorts table elements including times.
     */
    var sortTableList = function () {
        $(function () {
            var posicionOriginal = 0;
            var posicionFinal = 0;
            $("#table-added-garages").sortable({
                start: function (event, ui) {
                    posicionOriginal = ui.item.index() + 1;
                },
                stop: function (event, ui) {

                    posicionFinal = ui.item.index() + 1;
                    if (posicionFinal == 1) {
                        setOrderTime();
                    }
                    else {
                        var last_child = '';
                        if (posicionFinal > posicionOriginal) {
                            last_child = $('#table-added-garages > tr:nth-child(' + (posicionOriginal - 1) + ')').find('.input_time').first();
                        }
                        else {
                            last_child = $('#table-added-garages > tr:nth-child(' + (posicionFinal - 1) + ')').find('.input_time').first();
                        }
                        setOrderTimeByStartCurrentElement(last_child);
                    }

                    GMaps.drawRoute();
                    //setOrderTime();
                    setOrder();
                }
            });
        });
    };

    var setOrder = function () {
        var counter = 1;
        $('.garage_added').each(function () {
            $(this).find('.order-td').html(counter);
            counter++;
        });
    };

    var setOrderTime = function () {
        var start_time = '08';
        var end_time = '09';
        $('.garage_added').each(function () {
            $(this).find('.start_time').val(start_time + ':00');
            $(this).find('.end_time').val(end_time + ':30');

            if (start_time == '22') {
                start_time = '08';
                end_time = '09';
            } else {
                start_time = Number(start_time) + 2;
                end_time = Number(end_time) + 2;
            }
        });
    };

    /**
     * Sets elements times from start
     */
    var setOrderTimeByStart = function () {
        var last_item_focus = null;
        $('tr').off('click').on('click', function () {
            if (last_item_focus != null) {
                last_item_focus.trigger('blur');
            }
        });
        $('.input_time').off('focus').on('focus', function () {
            last_item_focus = $(this);
        });
        $('.input_time').off('blur').on('blur', function () {
            var td_time = $(this).closest('.td-time');
            var last_time = '';
            if ($(this).hasClass('start_time')) {
                last_time = moment($(this).val(), 'HH:mm').add(90, 'minutes').format('HH:mm');
                td_time.find('.end_time').val(last_time);
            }
            setOrderTimeByStartCurrentElement($(this));
        });
    };

    /**
     * Gets previous element time to order next elements times.
     * @param {*} input_time
     */
    var setOrderTimeByStartCurrentElement = function (input_time) {
        setTimeout(function () {
            var td_time = input_time.closest('.td-time');
            var tr_start = input_time.closest('tr');
            var last_time = td_time.find('.end_time').val();
            var tr_next = tr_start.next();
            while (tr_next.is('tr')) {
                last_time = moment(last_time, 'HH:mm').add(30, 'minutes').format('HH:mm');
                tr_next.find('.start_time').val(last_time);
                last_time = moment(last_time, 'HH:mm').add(90, 'minutes').format('HH:mm');
                tr_next.find('.end_time').val(last_time);
                tr_next = tr_next.next();
            }
        }, 200);
    }

    var markUnmark = function () {
        $('#mark_unmark_all').on('click', function () {
            if ($(this).prop('checked')) {
                $('.check-garage').each(function () {
                    $(this).prop('checked', true);
                })
            } else {
                $('.check-garage').each(function () {
                    $(this).prop('checked', false);
                })
            }
        });
    };

    var addRoute = function () {
        var $route = $('#add_route');
        $route.on('click', function () {
            var counter = 0;
            var data = {};
            data.garages = {};
            $('.garage_added').each(function () {
                data.garages[counter] = {};
                data.garages[counter]['id'] = $(this).data('id');
                data.garages[counter]['order'] = counter + 1;
                data.garages[counter]['start_time'] = $(this).find('.start_time').val();
                data.garages[counter]['end_time'] = $(this).find('.end_time').val();
                counter++;
            });
            swal({
                title: $.i18n._('Visit.Route_name'),
                text: $.i18n._('Visit.Select_route_name'),
                input: 'text',
                inputPlaceholder: $.i18n._('Visit.My_new_route'),
                showCancelButton: true,
                showCloseButton: true,
                inputValidator: function (value) {
                    return new Promise(function (resolve) {
                        if (value === false) {
                            resolve();
                        } else if (value === "") {
                            resolve($.i18n._('Visit.Write_something'));
                            return false
                        } else if (counter === 0) {
                            resolve($.i18n._("Visit.Select_garage"));
                            return false
                        } else {
                            if (!ValidateJS.validateCharacters(value)) {
                                resolve($.i18n._("Validation.Special_chars_not_allowed"));
                                return false
                            } else {
                                var url = $route.data('url');
                                data.name = value;
                                data.type = $route.data('type');

                                var request = PeticionAjax.post(url, data);
                                request.done(function () {
                                    Select2.load();
                                    editRoute();
                                    addRoute();
                                    swal($.i18n._('Visit.Nice'), $.i18n._('Visit.Route_created', value), "success");
                                });
                            }
                        }
                    })
                }
            });
        });
    };

    var editRoute = function () {
        var $route = $("#search_route");
        var $edit_route = $('#edit_route');

        $edit_route.on('click', function () {
            var counter = 0;
            var data = {};
            data.garages = {};
            $('.garage_added').each(function () {
                data.garages[counter] = {};
                data.garages[counter]['id'] = $(this).data('id');
                data.garages[counter]['order'] = counter + 1;
                data.garages[counter]['start_time'] = $(this).find('.start_time').val();
                data.garages[counter]['end_time'] = $(this).find('.end_time').val();
                counter++;
            });

            var routes = {};
            $("#search_route").find("option").each(function () {
                if ($(this).val() != '') {
                    routes[$(this).val()] = $(this).text();
                }
            });

            swal({
                title: $.i18n._('Visit.Select_route_name_update'),
                input: 'text',
                showCancelButton: true,
                inputValue: $('#search_route_map_select option[value=' + $('#loaded_route').attr('data-id') + ']').text(),
                inputValidator: function (value) {
                    return new Promise(function (resolve) {
                        if (value === "") {
                            resolve($.i18n._('Visit.Write_something'));
                            return false
                        } else {
                            if (!ValidateJS.validateCharacters(value)) {
                                resolve($.i18n._("Validation.Special_chars_not_allowed"));
                                return false
                            } else {
                                var url = $edit_route.data('url');
                                data.name = value;
                                data.id = $('#loaded_route').attr('data-id');
                                data.type = $edit_route.data('type');
                                data.route = $route.val();
                                var request = PeticionAjax.post(url, data);

                                request.done(function () {
                                    $('#search_route_map_select option[value=' + $('#loaded_route').attr('data-id') + ']').text(value);
                                    $('#search_route_map_select').select2();
                                    $('#search_route option[value=' + $('#loaded_route').attr('data-id') + ']').text(value);
                                    $('#search_route').select2();
                                    $('#loaded_route').html($.i18n._('Route.Route_loaded') + value);
                                    Select2.load();
                                    editRoute();
                                    addRoute();
                                    GMaps.createMapForIFrame($('#autocomplete-address'));
                                    swal($.i18n._('Visit.Nice'), $.i18n._('Visit.Route_edited', value), "success");
                                });
                            }
                        }
                    })
                }
            });
        });
    };

    var deleteAllGaragesAdded = function () {
        $('#delete_all_garages_added').on('click', function () {
            var total = $('.remove-garage').length - 1;
            $('.remove-garage').each(function (index) {
                if (index == total || index == (total - 1)) {
                    $(this).trigger('click', ['all', 'last']);
                } else {
                    $(this).trigger('click', ['all']);
                }
            });
        });
    };

    var searchRoute = function () {
        if ($('#route_id').val() != '') {
            $('#search_route').val($('#route_id')).trigger('change');
            $('#show_result').trigger('click');
        }
    };

    var exportExcelPlaningVisit = function () {
        $('.gd-export-excel-planing-visit-js').click(function (event) {
            event.preventDefault();
            var exportados = '';
            $('.garage_added').each(function () {
                if (exportados.length != 0) {
                    exportados += '&' + ($(this).data('id'));
                } else {
                    exportados += ($(this).data('id'));
                }
            });
            var _url = $(this).data('url');
            _url += '?' + exportados;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
                    prepareCallback: function () {
                        PeticionAjax.mostrarCargando();
                    },
                    successCallback: function () {
                        PeticionAjax.ocultarCargando();
                    },
                    failCallback: function () {
                        PeticionAjax.ocultarCargando();
                        swal({
                            title: $.i18n._('Constants.Error_alert_general'),
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var search = function () {
        $('#search_select').on('change', function () {
            if ($(this).val() != null && $(this).val() != '') {
                $('#delete_search').removeClass('d-none');
                var $my_customer = $('#search_my_customers');
                var url = $(this).data('url');
                var data = {};
                data.id = $(this).val();
                $.ajax({
                    data: data,
                    type: 'POST',
                    dataType: "json",
                    url: url
                }).done(function (data) {
                    $('#search_route').val(data.UserSearch.route_id).trigger('change');
                    $('#search_distance').val(data.UserSearch.distance).trigger('change').prop('disabled', true);
                    $('#search_last_visit').val(data.UserSearch.last_visit).trigger('change');
                    $('#latitude-localization').val(data.UserSearch.lat);
                    $('#longitude-localization').val(data.UserSearch.lng);

                    if (data.UserSearch.my_customers == 1) {
                        $my_customer.prop('checked', true)
                    } else {
                        $my_customer.prop('checked', false);
                    }
                    $('#search_city').val(data.UserSearch.city);

                    $('#autocomplete-address').val(data.UserSearch.location);
                    if (data.UserSearch.networks != undefined) {
                        $('#search_network').val(data.UserSearch.networks).select2();
                    } else {
                        $('#search_network').val('0').select2();
                    }

                    $('#search_contact').val(data.UserSearch.contacts).trigger('change');
                    $('#search_network_status').val(data.UserSearch.network_status_id).trigger('change');

                    if (data.UserSearch.distributors != undefined) {
                        $('#search_distributor').val(data.UserSearch.distributors).select2();
                    } else {
                        $('#search_distributor').val('0').select2();
                    }

                    $('#show_result').trigger('click');
                });
            } else {
                $('#delete_search').addClass('d-none');
            }
        });
    };

    var saveSearch = function () {
        $('#save_search').on('click', function () {
            var url = $(this).data('url');
            swal({
                title: $.i18n._('Visit.Search_name'),
                input: 'text',
                inputPlaceholder: $.i18n._('Visit.My_new_search'),
                showCancelButton: true,
                showCloseButton: true,
                inputValidator: function (value) {
                    return new Promise(function (resolve) {
                        if (value === false) {
                            resolve();
                        } else if (value === "") {
                            resolve($.i18n._('Visit.Write_something'));
                            return false
                        } else {
                            var data = {};
                            data.route_id = $('#search_route').val();
                            data.name = value;
                            data.is_garages = 1;
                            data.location = $('#autocomplete-address').val();
                            data.city = $('#search_city').val();
                            data.lat = $('#latitude-localization').val();
                            data.lng = $('#longitude-localization').val();
                            data.distance = $('#search_distance').val();
                            data.last_visit = $('#search_last_visit').val();
                            data.networks = $('#search_network').val();
                            data.network_status_id = $('#search_network_status').val();
                            data.distributors = $('#search_distributor').val();
                            data.my_customers = $('#search_my_customers').prop('checked');
                            data.contact = $('#search_contact').val();
                            var request = PeticionAjax.post(url, data);
                            request.done(function (data) {
                                $('#search_select').append('<option value="' + data + '">' + value + '</option>');
                                resolve();
                            });
                        }
                    })
                }
            });
        });
    };

    var deleteSearch = function () {
        var $select = $('#search_select');
        $('#delete_search').on('click', function () {
            var search_id = $select.val();
            var url = $(this).data('url');
            var data = {};
            data.id = search_id;

            swal({
                title: $.i18n._('Visit.Delete_search?'),
                text: $.i18n._('Alert.No_revert'),
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF5745',
                cancelButtonColor: '#d33',
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(url, data);
                    request.done(function () {
                        $select.find('option').each(function () {
                            if ($(this).val() == $select.val()) {
                                $(this).remove();
                                $select.val(0).trigger('change')
                            }
                        })
                    });
                }
            });

        });
    };

    return {
        load: function () {
            loadBehaviour();
            searchVisits();
            generateVisits();
            sortTableList();
            addRoute();
            editRoute();
            exportExcelPlaningVisit();
            searchRoute();
            search();
            saveSearch();
            deleteSearch();
            searchRouteMap();
            selectDinamic();
            cargar_distributors();
        },
        loadRemoveGarage: function () {
            loadRemoveGarage();
        },
        setOrderTime: function () {
            setOrderTime();
        },
        setOrder: function () {
            setOrder();
        },
        setOrderTimeByStart: function () {
            setOrderTimeByStart();
        },
        setOrderTimeByStartCurrentElement: function (input_time) {
            setOrderTimeByStartCurrentElement(input_time);
        },
        ocultarCargando: function () {
            ocultarCargando();
        }
    }
})();
