$(document).ready(function () {
    Appointments.load();
    $("div").find("[data-alert]").css({
        "z-index": "6",
        "position": "fixed",
        "width": "100%",
    });
    Appointments.visitContact();
    if (($('#garage_name').val() != undefined && $('#garage_name').val() != '') && ($('#distributor_name').val() != undefined && $('#distributor_name').val() != '')) {
        $('.appointments_types').trigger('change');
    }
    Appointments.disabledFields();
});

$(window).on("load", function () {
    setTimeout(function () { Appointments.loadInputGarage(); }, 3000);
});

var Appointments = (function () {
    var assigned_to = function () {
        loadButtons();
    };

    var loadButtons = function () {
        $("#button_img").off("click").on("click", function () {
            $(this).addClass('active-button');
            $("#button_map").removeClass('active-button');
            $("#button_data").removeClass('active-button');
            $("#button_kpi").removeClass('active-button');

            $('#info_kpi').addClass('hide_element');
            $('#info_data').addClass('hide_element');
            $('#info_img').removeClass('hide_element');
            $('#info_map').addClass('hide_element');
        });

        $("#button_data").off("click").on("click", function () {
            $(this).addClass('active-button');
            $("#button_img").removeClass('active-button');
            $("#button_map").removeClass('active-button');
            $("#button_kpi").removeClass('active-button');

            $('#info_kpi').addClass('hide_element');
            $('#info_data').removeClass('hide_element');
            $('#info_img').addClass('hide_element');
            $('#info_map').addClass('hide_element');
        });

        $("#button_kpi").off("click").on("click", function () {
            $(this).addClass('active-button');
            $("#button_img").removeClass('active-button');
            $("#button_map").removeClass('active-button');
            $("#button_data").removeClass('active-button');

            $('#info_kpi').removeClass('hide_element');
            $('#info_data').addClass('hide_element');
            $('#info_img').addClass('hide_element');
            $('#info_map').addClass('hide_element');
        });

        $("#button_map").off("click").on("click", function () {
            $(this).addClass('active-button');
            $("#button_img").removeClass('active-button');
            $("#button_data").removeClass('active-button');
            $("#button_kpi").removeClass('active-button');

            $('#info_kpi').addClass('hide_element');
            $('#info_data').addClass('hide_element');
            $('#info_img').addClass('hide_element');
            $('#info_map').removeClass('hide_element');
            if (!$(this).hasClass('no-coordinates')) {
                GMaps.createSingleMapWithoutInfo();
            }
        });

    };

    var hide_cnt_feedback = function () {

        var _hide_cnt_feedback = function (element) {
            var selectedDate = element.val();
            var date_split = selectedDate.split("-");
            var moment_date_now = moment();
            var moment_date = selectedDate.replace(".", "-");
            moment_date = moment_date + ' ' + $('#start_time').val();
            if (moment(moment_date + ', DD-MM-YYYY HH:mm"').isValid() != false) {
                moment_date = moment(moment_date, "DD-MM-YYYY HH:mm");
            } else {
                moment_date = moment(moment_date, "DD-MM-YYYY HH:mm:ss");
            }
            selectedDate = new Date(date_split[2] + '-' + date_split[1] + '-' + date_split[0]);
            var today = new Date();

            if ($('#appointment-status-hidden').val() != STATUS_APPOINTMENTS_CANCEL && $('#appointment-status-hidden').val() != STATUS_APPOINTMENTS_COMPLETED && $('#appointment-status-hidden').val() != STATUS_APPOINTMENTS_RUNNING) {

                if ((selectedDate.getYear() < today.getYear())) {
                    $('#cnt_appointments_notify_to').show();
                    if (($('#appointment-status option[value=' + STATUS_APPOINTMENTS_PENDING + ']').length > 0) !== true) {
                        $('#appointment-status').append($('<option>').text($.i18n._('CRM.Pending')).attr('value', STATUS_APPOINTMENTS_PENDING));
                        $('#appointment-status').prop('disabled', true).trigger('change');
                    }

                    $('#appointment-status').val(STATUS_APPOINTMENTS_PENDING).trigger('change');
                    $('#appointment-status-hidden').val(STATUS_APPOINTMENTS_PENDING).trigger('change');
                }
                else if ((selectedDate.getYear() <= today.getYear()) && (selectedDate.getMonth() < today.getMonth())) {
                    $('#cnt_appointments_notify_to').show();
                    if (($('#appointment-status option[value=' + STATUS_APPOINTMENTS_PENDING + ']').length > 0) !== true) {
                        $('#appointment-status').append($('<option>').text($.i18n._('CRM.Pending')).attr('value', STATUS_APPOINTMENTS_PENDING));
                        $('#appointment-status').prop('disabled', true).trigger('change');
                    }
                    $('#appointment-status').val(STATUS_APPOINTMENTS_PENDING).trigger('change');
                    $('#appointment-status-hidden').val(STATUS_APPOINTMENTS_PENDING).trigger('change');
                } else if ((selectedDate.getYear() <= today.getYear()) && (selectedDate.getMonth() == today.getMonth())) {
                    if ((selectedDate.getYear() <= today.getYear()) && (selectedDate.getDate() >= today.getDate()) && (moment_date > moment_date_now)) {
                        $('#cnt_appointments_notify_to').hide();
                        $('#appointment-status').val(STATUS_APPOINTMENTS_PLANNED).trigger('change');
                        $('#appointment-status-hidden').val(STATUS_APPOINTMENTS_PLANNED).trigger('change');
                        $('#appointment-status').prop('disabled', false).trigger('change');
                        $('#appointment-status option[value=' + STATUS_APPOINTMENTS_PENDING + ']').remove().trigger('change');

                    } else {
                        $('#cnt_appointments_notify_to').show();
                        if (($('#appointment-status option[value=' + STATUS_APPOINTMENTS_PENDING + ']').length > 0) !== true) {
                            $('#appointment-status').append($('<option>').text($.i18n._('CRM.Pending')).attr('value', STATUS_APPOINTMENTS_PENDING));
                            $('#appointment-status').prop('disabled', true).trigger('change');
                        }
                        $('#appointment-status').val(STATUS_APPOINTMENTS_PENDING).trigger('change');
                        $('#appointment-status-hidden').val(STATUS_APPOINTMENTS_PENDING).trigger('change');
                    }
                } else if ((selectedDate.getYear() >= today.getYear()) && (selectedDate.getMonth() > today.getMonth())) {
                    $('#cnt_appointments_notify_to').hide();
                    $('#appointment-status').val(STATUS_APPOINTMENTS_PLANNED).trigger('change');
                    $('#appointment-status-hidden').val(STATUS_APPOINTMENTS_PLANNED).trigger('change');
                    $('#appointment-status').prop('disabled', false).trigger('change');
                    $('#appointment-status option[value=' + STATUS_APPOINTMENTS_PENDING + ']').remove().trigger('change');

                } else {
                    $('#cnt_appointments_notify_to').hide();
                    $('#appointment-status').val(STATUS_APPOINTMENTS_PLANNED).trigger('change');
                    $('#appointment-status-hidden').val(STATUS_APPOINTMENTS_PLANNED).trigger('change');
                    $('#appointment-status').prop('disabled', false).trigger('change');
                    $('#appointment-status option[value=' + STATUS_APPOINTMENTS_PENDING + ']').remove().trigger('change');
                }
            }
        }

        _hide_cnt_feedback($('#appointment-date'))

        $('#appointment-date').on('change', function () {
            _hide_cnt_feedback($(this))
        });
    };

    var loadNotesFeedback = function () {
        if ($('#notes_area').val() == '') {
            $('#div_notes').slideUp();
            $('#flecha_abajo_notes').show();
            $('#flecha_arriba_notes').hide();
        } else {
            $('#flecha_abajo_notes').hide();
            $('#flecha_arriba_notes').show();
        }
        if ($('#feedback_area').val() == '') {
            $('#flecha_abajo_feedback').show();
            $('#flecha_arriba_feedback').hide();
        } else {
            $('#flecha_abajo_feedback').hide();
            $('#flecha_arriba_feedback').show();
        }
        $('#flecha_abajo_notes').on('click', function () {
            $('#div_notes').slideDown();
            $('#flecha_abajo_notes').hide();
            $('#bottom-div-notes').removeClass('b-bottom-1');
            $('#flecha_arriba_notes').show();
        });
        $('#flecha_arriba_notes').on('click', function () {
            $('#div_notes').slideUp();
            $('#flecha_abajo_notes').show();
            $('#bottom-div-notes').addClass('b-bottom-1');
            $('#flecha_arriba_notes').hide();
        });
        $('#flecha_abajo_feedback').on('click', function () {
            $('#div_feedback').slideToggle();
            $('#flecha_abajo_feedback').hide();
            $('#flecha_arriba_feedback').show();
        });
        $('#flecha_arriba_feedback').on('click', function () {
            $('#div_feedback').slideToggle();
            $('#flecha_abajo_feedback').show();
            $('#flecha_arriba_feedback').hide();
        });
    };

    var loadBehaviour = function (save_task) {
        assigned_to();
        loadButtons();

        $('.uncheck-task').each(function () {
            $(this).prop('checked', true);
        });

        $('.appointments_types').on('change', function () {
            if ($(this).attr('id') == 'appointment_type_garage') {
                $('#no_customer').hide();
                $('#cnt_garage_name').show();
                $('#cnt_distributor_name').hide();
                $('#advanced_search_garage').show();
                $('#advanced_search_distributor').hide();
                $('#advanced_search_select').hide();
            } else if ($(this).attr('id') == 'appointment_type_distributor') {
                $('#no_customer').hide();
                $('#cnt_garage_name').hide();
                $('#cnt_distributor_name').show();
                $('#advanced_search_garage').hide();
                $('#advanced_search_distributor').show();
                $('#advanced_search_select').hide();
            }
            $('#garage_name').val('').trigger('change');
            $('#distributor_name2').val('').trigger('change');
            $('#modal-follow-up-visit').hide();
        });

        if ($('#appointment_type_garage').prop('checked') == true) {
            $('#no_customer').hide();
            $('#cnt_garage_name').show();
            $('#cnt_distributor_name').hide();
            $('#advanced_search_garage').show();
            $('#advanced_search_distributor').hide();
            $('#advanced_search_select').hide();
        } else if ($('#appointment_type_distributor').prop('checked') == true) {
            $('#no_customer').hide();
            $('#cnt_garage_name').hide();
            $('#cnt_distributor_name').show();
            $('#advanced_search_garage').hide();
            $('#advanced_search_distributor').show();
            $('#advanced_search_select').hide();
        } else {
            $('#no_customer').show();
            $('#cnt_garage_name').hide();
            $('#cnt_distributor_name').hide();
        }

        if (!save_task) {
            $('#garage_name').off('change').on('change', function () {
                if ($(this).val() != '' && $(this).val() != null) {
                    $('#modal-follow-up-visit').show();
                    var $result = $(this);
                    if ($result.val() != null) {
                        var url = $('#get_data_garage_info').data('url');
                        var request = PeticionAjax.post(url + '/' + $result.val());
                        var garage_name = $('#garage_name');
                        request.done(function (data) {
                            $('#garage_info').html(data);
                            $('#distributor_objectives').addClass('d-none');
                            GMaps.createSingleMapWithoutInfo();
                            loadButtons();
                            assigned_to();
                        });
                    }
                } else {
                    if ($('#distributor_name').val() == '') {
                        $('#go-to-garage').remove();
                    }
                }
            });

            $('#distributor_name').on('change', function () {
                if ($(this).val() != '' && $(this).val() != null) {
                    $('#modal-follow-up-visit').show();
                    var $result = $(this);
                    var url = $('#get_data_distributor_info').data('url');
                    var request = PeticionAjax.post(url + '/' + $result.val());
                    request.done(function (data) {
                        $('#garage_info').html(data);
                        $('#distributor_objectives').removeClass('d-none');
                        GMaps.createSingleMapWithoutInfo();
                        loadButtons();
                        assigned_to();
                    });
                } else {
                    if ($('#garage_name').val() == '') {
                        $('#go-to-garage').remove();
                    }
                }
            });
        }

        length_container();
    };

    var length_container = function () {
        var check_task = false;
        var uncheck_task = false;
        //var locked_task = false;
        if ($('.check-task').length == 0) {
            $('#task-container-pending').hide();
            check_task = true;
        } else {
            $('#task-container-pending').show();
        }
        if ($('.uncheck-task').length == 0) {
            $('#task-container-completed').hide();
            uncheck_task = true;
        } else {
            $('#task-container-completed').show();
        }
        //if($('.unlock-task').length == 0){
        //    $('#task-container-locked').hide();
        //    locked_task = true;
        //} else {
        //    $('#task-container-locked').show();
        //}

        if (check_task == false && uncheck_task == false) {
            $('#my_tasks').addClass('d-none');
        }
    };

    var appointments = function () {
        var select = false;
        var click = false;
        var current_btn_info = null;
        $('#search').on('click', function () {
            $('.btn-info').each(function () {
                if (!$(this).hasClass('hide_element')) {
                    current_btn_info = $(this).attr('id');
                }
            });
            $('.appointments_types').each(function () {
                if ($(this).prop('checked') != false) {
                    if ($('#search').hasClass("search_normal_selected")) {
                        $('#search').removeClass("search_normal_selected");
                        $('#advanced_search_select').hide();
                        $('#advanced_search').hide();
                        $('#title_garage').show();
                        $('#garage_info').show();
                        $('#' + current_btn_info).removeClass('hide_element');
                    } else {
                        $('#search').addClass("search_normal_selected");
                        $('#advanced_search_select').hide();
                        $('#advanced_search').show();
                        $('#title_garage').hide();
                        $('#' + current_btn_info).addClass('hide_element');
                    }
                    select = true;
                }
            });
            if (!select && !click) {
                $('#search').addClass("search_normal_selected");
                $('#advanced_search_select').show();
                $('#advanced_search').show();
                $('#title_garage').hide();
                click = true;
                $('#' + current_btn_info).addClass('hide_element');
            } else if (!select && click) {
                $('#search').removeClass("search_normal_selected");
                $('#advanced_search_select').show();
                $('#advanced_search').hide();
                $('#title_garage').show();
                $('#garage_info').show();
                click = false;
                $('#' + current_btn_info).removeClass('hide_element');
            }
        });

        var booRadio;
        $('.radio_feelings').each(function () {
            $(this).on('click', function () {
                if (booRadio == this) {
                    $(this).prop('checked', false);
                    booRadio = null;
                } else {
                    booRadio = this;
                }
            })
        });

        $('#back_to_search').on('click', function () {
            $('#results_list').hide();
            $('#search_list').show();
        });

        $('#back_to_search_distributor').on('click', function () {
            $('#results_list_distributor').hide();
            $('#search_list_distributor').show();
        });

        $('#show_result').on('click', function () {
            var data = {};
            data.name = $('#name_advanced_search').val();
            data.network = $('#network_advanced_search').val();
            data.distributor = $('#distributor_advanced_search').val();
            data.town = $('#town_advanced_search').val();
            data.user_assigned_id = $('#assigned-to-appointments').val();
            var url = $(this).data('url');
            var request = $.ajax({
                data: data,
                type: "POST",
                dataType: "json",
                url: $(this).data('url')
            });
            request.done(function (data) {
                var $results = $('#print_results_list');
                $results.html('');
                $('#search_list').hide();
                $('#results_list').show();
                var counter = 0;
                for (var item in data) {
                    $results.append('<p data-id="' + data[item]['id'] + '" class="c-blanco cursor-pointer result_advanced_search">' + data[item]['name'] + ' - ' + data[item]['g_number_id'] + ' - ' + data[item]['town'] + '</p>');
                    counter++;
                    if (counter == 5) {
                        $results.append('<p class="c-blanco">' + $results.data("msg") + '</p>');
                    }
                }

                $('.result_advanced_search').on('click', function () {
                    var $result = $(this);
                    var url = $('#get_data_garage_info').data('url');
                    var request = PeticionAjax.post(url + '/' + $result.data('id'));
                    request.done(function (data) {
                        $('#garage_name').empty();
                        $('#garage_name').append($('<option>', {
                            value: $result.data('id'),
                            text: $result.html()
                        }));
                        $('#garage_info').html(data);
                        $('#garage_name').val($result.data('id')).trigger('change', ['no-task']);
                        $('#back_to_search').trigger('click');
                        $('#search').trigger('click');
                        GMaps.createSingleMapWithoutInfo();
                    });
                });
            });
        });

        $('#show_result_distributor').on('click', function () {
            var data = {};
            data.name = $('#name_advanced_search_distributor').val();
            data.account_number = $('#account_number_advanced_search_distributor').val();
            data.town = $('#town_advanced_search_distributor').val();
            data.user_assigned_id = $('#assigned-to-appointments').val();

            var url = $(this).data('url');
            var request = $.ajax({
                data: data,
                type: "post",
                dataType: "json",
                url: $(this).data('url')
            });
            request.done(function (data) {
                var $results = $('#print_results_list_distributor');
                $results.html('');
                $('#search_list_distributor').hide();
                $('#results_list_distributor').show();
                var counter = 0;
                for (var item in data) {
                    $results.append('<p data-id="' + data[item]['id'] + '" class="c-blanco cursor-pointer result_advanced_search_distributor">' + data[item]['name'] + ' - ' + data[item]['account_number'] + ' - ' + data[item]['town'] + '</p>');
                    counter++;
                    if (counter == 5) {
                        $results.append('<p class="c-blanco">' + $results.data("msg") + '</p>');
                    }
                }

                $('.result_advanced_search_distributor').on('click', function () {
                    var $result = $(this);
                    var url = $('#get_data_distributor_info').data('url');
                    var request = PeticionAjax.post(url + '/' + $result.data('id'));

                    request.done(function (data) {
                        $('#garage_info').html(data);
                        $('#distributor_name').val($result.data('id')).trigger('change');
                        $('#back_to_search_distributor').trigger('click');
                        $('#search').trigger('click');
                        GMaps.createSingleMapWithoutInfo();
                    });
                });
            });
        });
    };

    var loadDeleteDocuments = function () {
        $(".delete-file-js").click(function (event) {
            event.preventDefault();
            if (confirm($(this).data('confirmmsg'))) {
                data = {};
                data.id = $(this).data('id');
                var div = $(this).data('div');
                var request = PeticionAjax.post($(this).data('url'), data);
                request.done(function (data) {
                    $(div).html(data);
                    loadDeleteDocuments();
                });
            }
        });
    };


    var createTaskAppointment = function () {
        var $taskId = $('#TaskId');

        $('#create_task').on('click', function () {
            $taskId.val('');
            $('.cnt-user-garage').show();
            $('#user_assigned_id').val('');
            $('#title').val('');
            $('#distributor_name_assigned_to').select2("val", "");
            $('#distributor_name_assigned_to').val(0).trigger('change');
            $('#distributor_filter_rsm').select2("val", "");
            $('#distributor_filter_bdm').select2("val", "");
            $('#body').val('');
            $('#user_creation_id').val('');
            $('#creation-date').val('');
            $('#cnt_creation').addClass('d-none');
            $('#due-date').val('').trigger('change');
            $('#assigned-to').val('').trigger('change').prop('disabled', false);
            $('#contact_list').val('').trigger('change').prop('disabled', false);
            $('#users').val('').trigger('change');
            $('#delete_task').hide();
            $('#cnt_files').find('.dragdrop-delete-file-js').trigger('click');
            $('#modal_form_task').find('.dragdrop-delete-file-js').trigger('click');
            $('#modal_form_task').find('#file-list-js').html('');
            $('#task_type_user').prop('disabled', false);
            $('#task_type_garage').prop('disabled', false);
            $('#select-status-js').val('2').trigger('change');
        });

        loadEditTask();

        $('.save_task').off('click').on('click', function () {
            var size = false;
            var max_size = 5242880;
            $('.task-files').each(function () {
                if ($(this)[0].files[0] != undefined) {
                    if ($(this)[0].files[0].size > max_size) {
                        size = true;
                    }
                }
            });
            if (size === true) {
                swal($.i18n._('Alert.Error'), $.i18n._('General.File_size'), "error");
            } else if ($('#body').val() == '') {
                swal($.i18n._('Alert.Error'), $.i18n._('Alert.Empty_body'), "error");
            } else if ($('#title').val() == '') {
                swal($.i18n._('Alert.Error'), $.i18n._('Task.Title_is_required'), "error");
            } else if ($('#due-date').val() == '' && $('#due-date').parent().hasClass('required')) {
                swal($.i18n._('Alert.Error'), $.i18n._('Validation.Mandatory_to_choose_a_deadline_v2'), "error");
            } else if (!ValidateJS.validateCharacters($('#body').val()) || !ValidateJS.validateCharacters($('#title').val())) {
                swal($.i18n._('Alert.Error'), $.i18n._('Validation.Special_chars_not_allowed'), "error")
            } else {
                if ($('#task_type_garage').prop('checked') && $('#garage_name_assigned_to').val() == null) {
                    swal({
                        title: $.i18n._('Task.Confirm_submit'),
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: primary_color,
                        confirmButtonText: $.i18n._('General.Yes'),
                        cancelButtonText: $.i18n._('General.No'),
                    }).then(function (result) {
                        if (result.value) {
                            var url = $('#save_task_form').data('url') + '/' + $('#appointment-id').val();
                            if ($taskId.val() != undefined && $taskId.val() != '') {
                                url += '/' + $('#TaskId').val();
                            }

                            var formData = new FormData($('#task-form')[0]);
                            var $contactList = $('#contact_list');
                            var $countAlerts = $('#count-alerts');
                            var $users = $('#users');
                            var request = $.ajax({
                                url: url,
                                type: 'POST',
                                data: formData,
                                async: false,
                                cache: false,
                                contentType: false,
                                processData: false
                            });

                            request.done(function (data) {
                                $('#cnt_task').html(data);
                                completeTask();
                                unCheckTask();
                                unLockTask();
                                tooltipTask();
                                downloadTooltipTask();
                                loadBehaviour(true);
                                $('.save_task').unbind();
                                $('.icon-edit-task').unbind();
                                $('#create_task').unbind();
                                createTaskAppointment();
                                swal("", $.i18n._('Constants.Message_well_saved'), "success");
                                $('a.close-modal').trigger('click');
                                if ($('#task_type_user').prop('checked')) {
                                    $('#my_tasks').removeClass('d-none');
                                } else {
                                    $('#cnt_task_garage_parent').removeClass('d-none');
                                }
                                var notifications_flag = ($('#check_down').prop('checked') == true);

                                if (notifications_flag) {
                                    var user_flag = false;
                                    var contact_list_flag = false;

                                    if ($users.val() != null) {
                                        $users.val().forEach(function (user) {
                                            if ($('#UserId').val() == user) {
                                                user_flag = true;
                                            }
                                        });
                                    }

                                    var url = $contactList.data('url');
                                    data = {};
                                    data.contact_list = $contactList.val();

                                    var request = PeticionAjax.post(url, data);
                                    request.done(function (data) {
                                        contact_list_flag = data;

                                        if (user_flag || contact_list_flag) {
                                            $countAlerts.html(Number($countAlerts.html()) + 1);
                                        }
                                    });
                                }
                            });
                            request.fail(function () {
                                swal($.i18n._('Alert.Error'), $.i18n._("General.Error"), "error");
                            });
                        }
                        $('#garage_name').trigger('change');
                        $('#distributor_name').trigger('change');
                    });
                } else {
                    var url = $('#save_task_form').data('url') + '/' + $('#appointment-id').val();
                    if ($taskId.val() != undefined && $taskId.val() != '') {
                        url += '/' + $('#TaskId').val();
                    }
                    var formData = null;
                    if ($('#task-form').children('#task-form')[0] != undefined) {
                        formData = new FormData($('#task-form').children('#task-form')[0]);
                    } else {
                        formData = new FormData($('#task-form')[0]);
                    }

                    var $contactList = $('#contact_list');
                    var $countAlerts = $('#count-alerts');
                    var $users = $('#users');
                    var request = $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        async: false,
                        cache: false,
                        contentType: false,
                        processData: false
                    });

                    request.done(function (data) {
                        $('#cnt_task').html(data);
                        completeTask();
                        unCheckTask();
                        unLockTask();
                        tooltipTask();
                        downloadTooltipTask();
                        loadBehaviour(true);
                        $('.save_task').unbind();
                        $('.icon-edit-task').unbind();
                        $('#create_task').unbind();
                        createTaskAppointment();
                        swal("", $.i18n._('Constants.Message_well_saved'), "success");
                        $('a.close-modal').trigger('click');
                        if ($('#task_type_user').prop('checked')) {
                            $('#my_tasks').removeClass('d-none');
                        } else {
                            $('#cnt_task_garage_parent').removeClass('d-none');
                        }
                        var notifications_flag = ($('#check_down').prop('checked') == true);

                        if (notifications_flag) {
                            var user_flag = false;
                            var contact_list_flag = false;

                            if ($users.val() != null) {
                                $users.val().forEach(function (user) {
                                    if ($('#UserId').val() == user) {
                                        user_flag = true;
                                    }
                                });
                            }

                            var url = $contactList.data('url');
                            data = {};
                            data.contact_list = $contactList.val();

                            var request = PeticionAjax.post(url, data);
                            request.done(function (data) {
                                contact_list_flag = data;

                                if (user_flag || contact_list_flag) {
                                    $countAlerts.html(Number($countAlerts.html()) + 1);
                                }
                            });
                        }
                        $('#garage_name').trigger('change');
                        $('#distributor_name').trigger('change');
                    });
                    request.fail(function () {
                        swal($.i18n._('Alert.Error'), $.i18n._("General.Error"), "error");
                    });
                }
            }
        });

        $('.cancel_task').click(function () {
            $('#TaskId').val('');
            $('#cnt_create_task').show();
            $('#assigned-to').val('1').trigger('change');
            $('#select-status-js').val('2').trigger('change');
            $('#contact_list').select2("val", "");
            if ($users.length > 0) {
                $users.select2("val", "");
            }
            $('#due-date').val('');
            $('#body').val('');
            $('#title').val('');
            $('#distributor_name_assigned_to').select2("val", "");
            $('#distributor_name_assigned_to').val(0).trigger('change');
            $('#distributor_filter_rsm').select2("val", "");
            $('#distributor_filter_bdm').select2("val", "");
            $('#cnt_files').find('.dragdrop-delete-file-js').trigger('click');
            $('#cnt_form_task').find('#file-list-js').html('');
            $('#check_up').prop('checked', false);
            $('#check_down').prop('checked', false);
            $('#user_creation_id').val('');
            $('#creation-date').val('');
            $('#cnt_creation').addClass('d-none');
        });
    };

    var completeTask = function () {
        $('.complete_task').off('click').on('click', function () {
            var $task = $(this);
            var request = PeticionAjax.post($task.attr('data-url'));
            request.done(function () {
                $task.removeClass('ion-ios-checkmark-outline complete_task')
                    .addClass('ion-ios-checkmark revert_task c-exito')
                    .attr('data-url', $('#url_uncheck_task').val() + '/' + $task.data('id'));
                unCheckTask();
            });
        });
    };

    var unCheckTask = function () {
        $('.revert_task').off('click').on('click', function () {
            var $task = $(this);
            var request = PeticionAjax.post($task.attr('data-url'));
            request.done(function () {
                $task.removeClass('ion-ios-checkmark revert_task c-exito')
                    .addClass('ion-ios-checkmark-outline complete_task')
                    .attr('data-url', $('#url_check_task').val() + '/' + $task.data('id'));
                completeTask();
            });
        });
    };

    var unLockTask = function () {
        $('.unlock-task').on('click', function (event) {
            event.preventDefault();
            data = {};
            data.task_id = $(this).data('task');
            var input = $(this);
            var url = input.attr('data-url');
            var myContainer = input.closest('.task-container');
            var edit = input.siblings('.icon-edit-task');
            var myContainerHeader = myContainer.find('.fieldset-tasks-complete-header');
            var myContainerBody = myContainer.find('.fieldset-tasks-complete-body');
            var myCheck = myContainer.find('.unlock-task');
            var myLabel = $('label[for="' + myCheck.attr('id') + '"]');
            var $task = $('#confirm-uncheck-task');
            swal({
                title: $task.data('title'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $task.data('yes'),
                cancelButtonText: $task.data('no')
            }).then(function (result) {
                if (result.value) {
                    PeticionAjax.post(url, data);
                    myContainer.fadeOut(function () {
                        $('#task-container-pending').append(myContainer);
                        edit.removeClass('d-none');
                        input.removeClass('unlock-task');
                        input.addClass('check-task');
                        completeTask();
                        length_container();
                    });
                    input.attr('data-url', '/tasks/ajax_check_task/' + data.task_id);
                    myContainerHeader.removeClass('fieldset-tasks-complete-header');
                    myContainerHeader.addClass('fieldset-tasks-incomplete-header');
                    myContainerBody.removeClass('fieldset-tasks-complete-body');
                    myContainerBody.addClass('fieldset-tasks-incomplete-body');
                    myLabel.css({ transition: "all 2s" });
                    myLabel.addClass('c-informacion');
                    myLabel.removeClass('c-fallo');
                    myCheck.prop('checked', false);
                    myContainer.fadeIn();
                }
            });
        });
    };

    var tooltipTask = function () {
        $('.tooltip_view').tooltipster({
            theme: 'tooltipster-noir',
            trigger: 'click',
            interactive: true
        });

        $('.tooltip_view').each(function () {
            var paperclip = $(this);
            var tooltip_id = paperclip.data('id');
            if ($('#' + tooltip_id + ' li').length == 0) {
                var paperclip_id = paperclip.attr('id');
                $('#' + paperclip_id).remove();
            }
        });
    };

    var sendComment = function () {
        $('#send-comment').on('click', function (e) {
            e.preventDefault();
            if ($('#comment-body').val() == '') {
                swal("Oops", $.i18n._('Appointment.Add_comment_pls'), "error")
            } else if (!ValidateJS.validateCharacters($('#comment-body').val())) {
                swal("Oops", $.i18n._('Validation.Special_chars_not_allowed'), "error")
            } else {
                var url = $(this).data('url');
                data = {};
                data.appointment_id = $(this).data('appointment');
                data.body = $('#comment-body').val();
                var request = PeticionAjax.post(url, data);
                request.done(function (data) {
                    $('#comment-ajax').fadeOut(function () {
                        $('#comment-ajax').html(data);
                        $('#comment-ajax').fadeIn();
                        $('#comment-body').val('');
                        $('#span-comments').show();
                        loadMore();
                    });

                });
            }
        });
    };

    var loadMore = function () {
        $('#load-more-comments').on('click', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            data = {};
            data.appointment_id = $('#send-comment').data('appointment');
            data.body = $('#comment-body').val();
            data.start = $(this).data('start');
            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                $('#comment-ajax').fadeOut(function () {
                    $('#comment-ajax').html(data);
                    $('#comment-ajax').fadeIn();
                    loadMore();
                });
            });
        });
    };

    var downloadTooltipTask = function () {
        $('.download-task').on('click', function () {
            var url = $(this).data('url');
            var id = $(this).data('id');
            data = {};
            data.id = id;

            $.fileDownload(url, {
                type: "POST",
                data: data,
                successCallback: function (url) {

                },
                failCallback: function (responseHtml, url) {

                }
            });
        });
    };

    var searchTime = function () {
        var timer;

        $("#name_customer").keyup(function (e) {
            clearTimeout(timer);
            timer = setTimeout(function search() {
                searchAppointment();
            }, 750);
        });
        $("#search_id").keyup(function () {
            clearTimeout(timer);
            timer = setTimeout(function search() {
                searchEvent();
            }, 750);
        });
    };

    var searchAppointment = function () {
        var from_id = 'from=' + $('#from').val();
        var to_id = 'to=' + $('#to').val();
        var appointment_feeling_id = 'appointment_feeling_id=' + $('#appointment_feeling_id').val();
        var appointment_status_id = '';
        var appointment_status = $('#appointment_status_id').val();
        if (appointment_status != null) {
            appointment_status.forEach(function (appointment_status_tmp) {
                appointment_status_id += 'appointment_status_id[]=' + appointment_status_tmp + '&';
            });
        }
        var name_customer = 'name_customer=' + $('#name_customer').val();
        var user_assigned_id = 'user_assigned_id=' + $('#user_assigned_id').val()
        var appointment_type_id = 'appointment_type_id=' + $('#appointment_type_id').val();
        var feedback_fill_up_val = 0;
        if ($('#feedback_fill_up').prop('checked') == true) {
            feedback_fill_up_val = 1;
        }
        var feedback_fill_up = 'appointment_fill_up=' + feedback_fill_up_val;
        if (name_customer == "") {
            name_customer = 'all';
        }
        var url = from_id + '&' + to_id + '&' + appointment_feeling_id + '&' + appointment_status_id + user_assigned_id + '&' + name_customer + '&' +
            appointment_type_id + '&' + feedback_fill_up;

        if ($('#requires_follow_up').prop('checked') != undefined) {
            var requires_follow_up_val = 0;
            if ($('#requires_follow_up').prop('checked') == true) {
                requires_follow_up_val = 1;
            }
            var requires_follow_up = 'requires_follow_up=' + requires_follow_up_val;

            url += '&' + requires_follow_up
        }

        var request = PeticionAjax.post($('#name_customer').data('url') + '?' + url);
        request.done(function (data) {
            $('#agenda-lists').html(data);
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

    var deleteAppointment = function () {
        $('#delete_appointment').on('click', function (e) {
            e.preventDefault();
            var element = $(this);
            swal({
                title: $.i18n._('Alert.Sure?'),
                text: $.i18n._('Alert.No_revert'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#ff5648',
                cancelButtonColor: '#d33',
                confirmButtonText: $.i18n._('Alert.Confirm_delete'),
                cancelButtonText: $.i18n._('General.Cancel')
            }).then(function (result) {
                if (result.value) {
                    window.location = element.attr('href');
                }
            })
        });
    };

    var cancelAppointment = function () {
        $('#cancel_appointment').on('click', function (e) {
            e.preventDefault();
            var element = $(this);
            swal({
                title: $.i18n._('Alert.Sure?'),
                text: $.i18n._('Alert.No_revert'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#ff5648',
                cancelButtonColor: '#d33',
                confirmButtonText: $.i18n._('Alert.Confirm_cancel'),
                cancelButtonText: $.i18n._('General.Cancel')
            }).then(function (result) {
                if (result.value) {
                    window.location = element.attr('href');
                }
            })
        });
    };

    var deleteTask = function (task_id) {

        $('#delete_task').on('click', function (e) {
            e.preventDefault();
            var element = $(this);
            swal({
                title: $.i18n._('Alert.Sure?'),
                text: $.i18n._('Alert.No_revert'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#ff5648',
                cancelButtonColor: '#d33',
                confirmButtonText: $.i18n._('Alert.Confirm_delete'),
                cancelButtonText: $.i18n._('General.Cancel')
            }).then(function (result) {
                if (result.value) {
                    var url = element.data('url') + '/' + task_id + '/' + $('#appointment-id').val();
                    var request = PeticionAjax.post(url);
                    request.done(function () {
                        $('a.close-modal').trigger('click');
                        $('.cancel_task').trigger('click');
                        $('.task-container').each(function () {
                            if ($(this).data('id') == task_id) {
                                $(this).remove();
                            }
                        });
                        var url_x = '/../tasks/ajax_render_tasks';
                        var data_x = {};
                        data_x.appointment_id = $('#appointment-id').val();
                        var request_x = PeticionAjax.post(url_x, data_x);
                        request_x.done(function (results) {
                            $('#cnt_task').html(results)
                        });
                        length_container();
                    });
                }
            });
        });
    };

    var deleteEvent = function () {
        $(".delete-event-js").on('click', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            swal({
                title: $(this).data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function (result) {
                if (result.value) {
                    var search_input = $("#search_id").val();
                    if (search_input == "") {
                        search_input = 'all'
                    }
                    var request = PeticionAjax.post(url + '/' + search_input);

                    request.done(function (data) {
                        $('#ajax_table_maintenance_events').html(data);
                        reset();
                    });
                }
            });
        });
    };

    var selectDistributor = function () {

        $('.select2Dinamico').select2({
            placeholder: "",
            allowClear: true,
            tags: true
        });

        $('.cargar_distributors').select2({
            ajax: {
                url: '/distributors/get_distributors_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    var query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#distributor_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 3,
            placeholder: "Min 3 characters",
        });

    };

    var select = function () {
        $('.select_tr').on('click', function (e) {
            e.preventDefault();
            selected_id = $(this).attr('id');
            $('.dragdrop-delete-file-js').trigger('click');
            $(".select_tr").removeClass('bg-primary-i');
            $(this).addClass('bg-primary-i');
            var event_name_en = $(this).find('#language-en').text();
            var event_name_fr = $(this).find('#language-fr').text();
            var event_name_de = $(this).find('#language-de').text();
            event_name_en = $.trim(event_name_en);
            event_name_fr = $.trim(event_name_fr);
            event_name_de = $.trim(event_name_de);
            var url = $('#' + selected_id).attr('data-url');
            $('#name-event-edit-en').val(event_name_en);
            $('#name-event-edit-fr').val(event_name_fr);
            $('#name-event-edit-de').val(event_name_de);
            $('#img-event-edit').attr("src", '/img/iconos/' + url);
            $('#unselect-event').prop('disabled', false);
            $('#tab-add').prop("checked", false);
            $('#tab-edit').prop("checked", true);
            $('#form-edit-msg').hide();
            $('#form-add').hide();
            $('#form-edit').show();
        });

        $('#unselect-event').on('click', function (e) {
            e.preventDefault();
            $('#form-edit-msg').hide();
            $('#unselect-event').prop('disabled', true);
            $(".select_tr").removeClass('bg-primary-i');
            $('#img-event-add').attr("src", '/img/upload_pic.png');
            $('#form-add').show();
            $('#form-edit').hide();
            $('#name-event-add-en').val("");
            $('#name-event-add-fr').val("");
            $('#name-event-add-de').val("");
            $('#event-file-add').val("");
            $('.dragdrop-delete-file-js').trigger('click');
        });

        $('#tab-add').on('click', function () {
            $(".select_tr").removeClass('bg-primary-i');
            $('#form-add').show();
            $('#form-edit').hide();
            $('#form-edit-msg').hide();
        });

        $('#tab-edit').on('click', function () {
            $(".select_tr").removeClass('bg-primary-i');
            $('#form-edit-msg').show();
            $('#form-edit').hide();
            $('#form-add').hide();
        });

        $('#unselect-event').on('click', function () {
            $('#tab-add').trigger('click');
        });

    };

    var add = function () {
        if ($('#garage_name').val() == '' && $('#distributor_name').val() == '') {
            $('#modal-follow-up-visit').hide();
        } else {
            $('#modal-follow-up-visit').show();
        }
    };

    var edit = function () {

        if ($('#garage_name').val() == '' && $('#distributor_name').val() == '') {
            $('#modal-follow-up-visit').hide();
        } else {
            $('#modal-follow-up-visit').show();
        }

        add_follow_up_visit();

        if ($('#appointment-status').val() == $('#appointment-status').data('status-rescheduled') ||
            $('#appointment-status').val() == $('#appointment-status').data('status-accomplished')) {
            $('#start-visit').hide();
        } else {
            $('#start-visit').show();
        }

        $('#appointment-status').on('change', function () {
            if ($('#appointment-status').val() == $('#appointment-status').data('status-rescheduled') ||
                $('#appointment-status').val() == $('#appointment-status').data('status-accomplished')) {
                $('#start-visit').hide();
            } else {
                $('#start-visit').show();
            }
        });
    };

    var add_follow_up_visit = function () {

        $('#start-time-modal').val($('#start_time').val());

        $('#modal-follow-up-visit').off('click').on('click', function (e) {
            var garage_id = $('#garage_name').val();
            var distributor_id = $('#distributor_name').val();

            var url = $('#modal-follow-up-visit').data('url');

            data = {};
            data.garage_id = garage_id;
            data.distributor_id = distributor_id;
            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                $('#modal_follow_up_visit').html(data);
                $('#assigned-to-appointments-modal').select2();
                JQueryHelper.load();
                add_follow_up_visit();
            });
        });

        $('#save-follow-up-visit').off('click').on('click', function (e) {
            e.preventDefault();
            $('#appointment-date-modal').attr('name', 'data[Appointment][date]');
            $('#start-time-modal').attr('name', 'data[Appointment][start_time]');
            $('#assigned-to-appointments-modal').attr('name', 'data[Appointment][user_assigned_id]');
            $('#appointment-status-modal').attr('name', 'data[Appointment][appointment_status_id]');

            var date = $('#appointment-date-modal').val();
            var start_time = $('#start-time-modal').val();
            if (start_time == '') {
                var val = new Date();
                var hour_tmp = val.getHours();
                var minute_tmp = val.getMinutes();
                start_time = hour_tmp + ':' + minute_tmp;
            }
            var hours = start_time.split(':')[0];
            var minutes = start_time.split(':')[1];
            if (start_time.split(':')[2]) {
                var seconds = start_time.split(':')[2];
            }
            var garage_id = $('#garage_name').val();
            var distributor_id = $('#distributor_name').val();
            var status = $('#appointment-status-modal').val();
            var user_assigned_id = $('#assigned-to-appointments-modal').val();
            var url = $('#save-follow-up-visit').data('url');

            data = {};
            data.date = date;
            data.start_time = start_time;
            if (seconds) {
                if (parseInt(hours) >= 22) {
                    data.end_time = '23:59:59';
                } else {
                    data.end_time = (2 + parseInt(hours)) + ':' + minutes + ':' + seconds;
                }
            } else {
                if (parseInt(hours) >= 22) {
                    data.end_time = '23:59:59';
                } else {
                    data.end_time = (2 + parseInt(hours)) + ':' + minutes;
                }
            }
            data.garage_id = garage_id;
            data.distributor_id = distributor_id;
            data.appointment_status_id = status;
            data.user_assigned_id = user_assigned_id;

            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                $('#modal_follow_up_visit').html(data);
                $('#appointment-date-modal').attr('name', 'data[Appointment][date_modal]');
                $('#start-time-modal').attr('name', 'data[Appointment][start_time_modal]');
                $('#assigned-to-appointments-modal').attr('name', 'data[Appointment][user_assigned_id_modal]');
                $('#appointment-status-modal').attr('name', 'data[Appointment][appointment_status_id_modal]');
                $('#assigned-to-appointments-modal').select2();
                JQueryHelper.load();
                add_follow_up_visit();
            });

        });
    };

    var searchEvent = function () {
        var search_input = $("#search_id").val();
        if (search_input == "") { search_input = 'all' }
        var url = 'search_event/' + search_input;
        var request = PeticionAjax.post(url);
        request.done(function (data) {
            $('#ajax_table_maintenance_events').html(data);
            reset();
        });
    };

    var reset = function () {
        select();
        deleteEvent();
        $('#unselect-event').trigger('click');
        $(document).foundation('alert', 'reflow');
    };

    var check_send = function () {
        $(window).on('load', function () {
            $ajax = false;
            $(document).ajaxStart(function () {
                $ajax = true;
            });
            if ($('#check_send').length == 1) {
                if ($ajax) {
                    $(document).ajaxStop(function () {
                        _check_send();
                    });
                } else {
                    _check_send();
                }
            }
        });
    };

    var _check_send = function () {
        var check_send = $('#check_send');
        if (check_send.length == 1) {
            $('#feedback_area').on('change', function () {
                check_send.val(1);
            });

            var distributor_name = $('#distributor_name').val();
            var assigned = $('#assigned-to-appointments').val();
            var appointment = $('#appointment-date').val();
            var start_time = $('#start_time').val();
            var end_time = $('#end_time').val();
            var requires_follow_up = $('#requires_follow_up').is(":checked");
            var appointment_status = $('#appointment-status').val();
            var appointment_type_id = $('#appointment_type_id').val();
            var notes_area = $('#notes_area').val();
            var notify_to = $('#notify_to').val() + '';
            var notify_to_user = $('#notify_to_user').val() + '';
            var radioImage1 = $('#radioImage1').is(":checked");
            var radioImage2 = $('#radioImage2').is(":checked");
            var radioImage3 = $('#radioImage3').is(":checked");
            var radioImage4 = $('#radioImage4').is(":checked");
            var files = $('.swal-msg-ajax').length;

            $('#btn-guardar-no-exit').on('click', function (event) {
                event.preventDefault();
                var $this = $(this);
                if ($('#garage_name').val() == '' && $('#distributor_name').val() == '') {
                    var $appointent = $('#confirm-appointment');
                    swal({
                        title: $appointent.data('title'),
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: primary_color,
                        confirmButtonText: $appointent.data('yes'),
                        cancelButtonText: $appointent.data('no')
                    }).then(function (result) {
                        if (result.value) {
                            if (
                                distributor_name != $('#distributor_name').val() ||
                                assigned != $('#assigned-to-appointments').val() ||
                                appointment != $('#appointment-date').val() ||
                                start_time != $('#start_time').val() ||
                                end_time != $('#end_time').val() ||
                                requires_follow_up != $('#requires_follow_up').is(":checked") ||
                                appointment_status != $('#appointment-status').val() ||
                                appointment_type_id != $('#appointment_type_id').val() ||
                                notes_area != $('#notes_area').val() ||
                                notify_to != ($('#notify_to').val() + '') ||
                                notify_to_user != ($('#notify_to_user').val() + '') ||
                                radioImage1 != $('#radioImage1').is(":checked") ||
                                radioImage2 != $('#radioImage2').is(":checked") ||
                                radioImage3 != $('#radioImage3').is(":checked") ||
                                radioImage4 != $('#radioImage4').is(":checked") ||
                                files != $('.swal-msg-ajax').length ||
                                $('.dragdrop-delete-file-js').length > 0
                            ) {
                                check_send.val(1);
                            }
                            $('#btn-guardar-no-exit').off('click').trigger('click');
                            $this.prop('disabled', true);
                        }
                        });
                } else {
                    if (
                        distributor_name != $('#distributor_name').val() ||
                        assigned != $('#assigned-to-appointments').val() ||
                        appointment != $('#appointment-date').val() ||
                        start_time != $('#start_time').val() ||
                        end_time != $('#end_time').val() ||
                        requires_follow_up != $('#requires_follow_up').is(":checked") ||
                        appointment_status != $('#appointment-status').val() ||
                        appointment_type_id != $('#appointment_type_id').val() ||
                        notes_area != $('#notes_area').val() ||
                        notify_to != ($('#notify_to').val() + '') ||
                        notify_to_user != ($('#notify_to_user').val() + '') ||
                        radioImage1 != $('#radioImage1').is(":checked") ||
                        radioImage2 != $('#radioImage2').is(":checked") ||
                        radioImage3 != $('#radioImage3').is(":checked") ||
                        radioImage4 != $('#radioImage4').is(":checked") ||
                        files != $('.swal-msg-ajax').length ||
                        $('.dragdrop-delete-file-js').length > 0
                    ) {
                        check_send.val(1);
                    }

                    $('#btn-guardar-no-exit').off('click').trigger('click');
                    $this.prop('disabled', true);
                }
            });
        }

        $('#btn-guardar').on('click', function (event) {
            event.preventDefault();
            if ($('#feedback_area').val() == '') {
                swal($.i18n._('Appointment.Meeting_debrief_empty'), $.i18n._('Appointment.Meeting_debrief_empty_msg'), 'warning');
            } else if ($('#notify_to').val() == '' && $('#NotificationGenerateAlerts').prop('checked')) {
                swal($.i18n._('Appointment.Contact_list_empty'), $.i18n._('Appointment.Contact_list_empty_msg'), 'warning');
            } else if (!$('#radioImage1').prop('checked') && !$('#radioImage2').prop('checked') && !$('#radioImage3').prop('checked') && !$('#radioImage4').prop('checked')) {
                swal($.i18n._('Appointment.Feeling_empty'), $.i18n._('Appointment.Feedback_empty_msg_2'), 'warning');
            } else if ($('#garage_name').val() == '' && $('#distributor_name').val() == '') {
                var $appointent = $('#confirm-appointment');
                swal({
                    title: $appointent.data('title'),
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: primary_color,
                    confirmButtonText: $appointent.data('yes'),
                    cancelButtonText: $appointent.data('no')
                }).then(function (result) {
                    if (result.value) {
                        if (
                            // garage_name != $('#garage_name').val() ||
                            distributor_name != $('#distributor_name').val() ||
                            assigned != $('#assigned-to-appointments').val() ||
                            appointment != $('#appointment-date').val() ||
                            start_time != $('#start_time').val() ||
                            end_time != $('#end_time').val() ||
                            requires_follow_up != $('#requires_follow_up').is(":checked") ||
                            appointment_status != $('#appointment-status').val() ||
                            appointment_type_id != $('#appointment_type_id').val() ||
                            notes_area != $('#notes_area').val() ||
                            notify_to != ($('#notify_to').val() + '') ||
                            notify_to_user != ($('#notify_to_user').val() + '') ||
                            radioImage1 != $('#radioImage1').is(":checked") ||
                            radioImage2 != $('#radioImage2').is(":checked") ||
                            radioImage3 != $('#radioImage3').is(":checked") ||
                            radioImage4 != $('#radioImage4').is(":checked") ||
                            files != $('.swal-msg-ajax').length ||
                            $('.dragdrop-delete-file-js').length > 0
                        ) {
                            check_send.val(1);
                        }
                        $('#btn-guardar').off('click').trigger('click');
                        $this.prop('disabled', true);
                    }
                });
            } else {
                var q = d3.queue(1);
                var incomplete = false;

                q.defer(function (callback) {
                    incomplete = false;
                    $('.manaObjectiveId').each(function () {
                        var key = $(this).data('key');
                        if ($('#objectiveStatus' + key).val() == 0) {
                            incomplete = true;
                        }
                        if ($('#comment' + key).val() == '') {
                            incomplete = true;
                        }
                    });
                    callback(null);
                });

                q.defer(function (callback) {

                    $('.personalNameObjectiveId').each(function () {
                        var row = $(this);
                        var key = row.data('key');
                        if ($('#personalNameObjectiveKey' + key).val()) {
                            if ($('#personalNameObjectiveStatus' + key).val() == 0) {
                                incomplete = true;
                            }
                            if ($('#personalNameComment' + key).val() == '') {
                                incomplete = true;
                            }
                        }
                    });
                    callback(null);
                });

                q.awaitAll(function (error) {
                    var warning = $('#btn-guardar').data('warning');
                    if (error) throw error;
                    if (incomplete) {
                        swal({
                            title: warning,
                            type: "warning"
                        }).then(function (result) {

                        });
                    }
                    else {
                        if (
                            distributor_name != $('#distributor_name').val() ||
                            assigned != $('#assigned-to-appointments').val() ||
                            appointment != $('#appointment-date').val() ||
                            start_time != $('#start_time').val() ||
                            end_time != $('#end_time').val() ||
                            requires_follow_up != $('#requires_follow_up').is(":checked") ||
                            appointment_status != $('#appointment-status').val() ||
                            appointment_type_id != $('#appointment_type_id').val() ||
                            notes_area != $('#notes_area').val() ||
                            notify_to != ($('#notify_to').val() + '') ||
                            notify_to_user != ($('#notify_to_user').val() + '') ||
                            radioImage1 != $('#radioImage1').is(":checked") ||
                            radioImage2 != $('#radioImage2').is(":checked") ||
                            radioImage3 != $('#radioImage3').is(":checked") ||
                            radioImage4 != $('#radioImage4').is(":checked") ||
                            files != $('.swal-msg-ajax').length ||
                            $('.dragdrop-delete-file-js').length > 0
                        ) {
                            check_send.val(1);
                        }

                        $('#btn-guardar').off('click');
                        setTimeout(function () {
                            if ($('#garage_id').length > 0 || $('#is_form_event').length > 0) {
                                $('#btn-guardar').trigger('click');
                            }
                            $('#btn-guardar').prop('disabled', true);
                        }, 0);
                    }

                });

            }
        });
    }

    var check_send_event = function () {
        var check_send_event = $('#check_send_event');
        if (check_send_event.length == 1) {
            $('#btn-guardar').on('click', function (event) {
                event.preventDefault();
                if ($('#feedback_area').val() == '') {
                    swal($.i18n._('Appointment.Meeting_debrief_empty'), $.i18n._('Appointment.Meeting_debrief_empty_msg'), 'warning');
                } else if ($('#notify_to').val() == '') {
                    swal($.i18n._('Appointment.Contact_list_empty'), $.i18n._('Appointment.Contact_list_empty_msg'), 'warning');
                } else if (!$('#radioImage1').prop('checked') && !$('#radioImage2').prop('checked') && !$('#radioImage3').prop('checked') && !$('#radioImage4').prop('checked')) {
                    swal($.i18n._('Appointment.Feeling_empty'), $.i18n._('Appointment.Feedback_empty_msg_2'), 'warning');
                } else {
                    $('#appointment-form').submit();
                    $(window).on('load', function () {
                        $(document).ajaxComplete(function () {

                            check_send_event = $('#check_send_event');

                            if (check_send_event.length == 1) {
                                var title = $('#AppointmentTitle').val();
                                var appointment_type = $('#appointment_type_id').val();
                                var feedback = $('#feedback_area').val();
                                var start_date = $('#appointment-date').val();
                                var end_date = $('#appointment-date-end').val();
                                var start_time = $('#start_time').val() + '';
                                var end_time = $('#end_time').val() + '';
                                var notify_to = $('#notify_to').val() + '';
                                var radioImage1 = $('#radioImage1').is(":checked");
                                var radioImage2 = $('#radioImage2').is(":checked");
                                var radioImage3 = $('#radioImage3').is(":checked");
                                var radioImage4 = $('#radioImage4').is(":checked");
                                var assigned_to = $('#assigned-to-appointments').val();
                                $('#btn-guardar').on('click', function (event) {
                                    event.preventDefault();
                                    if ($('#feedback_area').val() == '') {
                                        swal($.i18n._('Appointment.Meeting_debrief_empty'), $.i18n._('Appointment.Meeting_debrief_empty_msg'), 'warning');
                                    } else if ($('#notify_to').val() == '') {
                                        swal($.i18n._('Appointment.Contact_list_empty'), $.i18n._('Appointment.Contact_list_empty_msg'), 'warning');
                                    } else if (!$('#radioImage1').prop('checked') && !$('#radioImage2').prop('checked') && !$('#radioImage3').prop('checked') && !$('#radioImage4').prop('checked')) {
                                        swal($.i18n._('Appointment.Feeling_empty'), $.i18n._('Appointment.Feedback_empty_msg_2'), 'warning');
                                    } else if (
                                        title != $('#AppointmentTitle').val() ||
                                        appointment_type != $('#appointment_type_id').val() ||
                                        feedback != $('#feedback_area').val() ||
                                        start_date != $('#appointment-date').val() ||
                                        end_date != $('#appointment-date-end').val() ||
                                        start_time != $('#start_time').val() + '' ||
                                        end_time != $('#end_time').val() + '' ||
                                        notify_to != $('#notify_to').val() + '' ||
                                        radioImage1 != $('#radioImage1').is(":checked") ||
                                        radioImage2 != $('#radioImage2').is(":checked") ||
                                        radioImage3 != $('#radioImage3').is(":checked") ||
                                        radioImage4 != $('#radioImage4').is(":checked") ||
                                        assigned_to != $('#assigned-to-appointments').val()
                                    ) {
                                        check_send_event.val(1);
                                    }
                                    $('#appointment-form').submit();
                                });

                                $('#btn-guardar-no-exit').on('click', function (event) {
                                    event.preventDefault();
                                    if (
                                        title != $('#AppointmentTitle').val() ||
                                        appointment_type != $('#appointment_type_id').val() ||
                                        feedback != $('#feedback_area').val() ||
                                        start_date != $('#appointment-date').val() ||
                                        end_date != $('#appointment-date-end').val() ||
                                        start_time != $('#start_time').val() + '' ||
                                        end_time != $('#end_time').val() + '' ||
                                        notify_to != $('#notify_to').val() + '' ||
                                        radioImage1 != $('#radioImage1').is(":checked") ||
                                        radioImage2 != $('#radioImage2').is(":checked") ||
                                        radioImage3 != $('#radioImage3').is(":checked") ||
                                        radioImage4 != $('#radioImage4').is(":checked") ||
                                        assigned_to != $('#assigned-to-appointments').val()
                                    ) {
                                        check_send_event.val(1);
                                    }
                                    $('#appointment-form').submit();
                                });
                            }
                        });
                    });
                }
            });
        }
    };

    var start_visit = function () {

        $('#start-visit').off('click').on('click', function (e) {
            e.preventDefault();

            if ($('#start_visit_location').length > 0) {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition, showError);
                } else {
                    swal({
                        title: 'Geolocation is not supported by this browser.',
                        type: "error"
                    }).then(function (result) {

                    });
                }
            } else {
                showPosition();
            }


            function showPosition(position) {
                var date = new Date();
                var day = date.getDate().toString();
                var month = (date.getMonth() + 1).toString();
                var year = date.getFullYear().toString();
                if (day.toString().length < 2) {
                    day = '0' + day.toString();
                }
                if (month.toString().length < 2) {
                    month = '0' + month.toString();
                }
                var strDate = day + "-" + month + "-" + year;

                var hours = date.getHours().toString();
                var minutes = date.getMinutes().toString();
                var seconds = date.getSeconds().toString();
                if (hours.toString().length < 2) {
                    hours = '0' + hours.toString();
                }
                if (minutes.toString().length < 2) {
                    minutes = '0' + minutes.toString();
                }
                if (seconds.toString().length < 2) {
                    seconds = '0' + seconds.toString();
                }
                var strtime = hours + ":" + minutes + ":" + seconds;

                $('#appointment-date').val(strDate);
                $('#start_time').val(strtime);

                $('#appointment-status-no-running').addClass('d-none');
                $('#appointment-status-running').removeClass('d-none');

                $('#status-appointment').prop('disabled', false);

                if ($('#start_visit_location').length > 0) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;
                } else {
                    var latitude = null;
                    var longitude = null;
                }


                url = $('#start-visit').data('url');

                data = [];
                data.latitude = latitude;
                data.longitude = longitude;
                data.user_assigned_id = $('#assigned-to-appointments').val();
                data.date = strDate;
                data.start_time = strtime;
                var query_data = serialize(data);
                window.location.replace(url + '?' + query_data);
            }

            function showError(error) {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        swal({
                            title: 'User denied the request for Geolocation.',
                            type: "error"
                        }).then(function (result) {

                        });
                        break;
                    case error.POSITION_UNAVAILABLE:
                        swal({
                            title: 'Location information is unavailable.',
                            type: "error"
                        }).then(function (result) {

                        });
                        break;
                    case error.TIMEOUT:
                        swal({
                            title: 'The request to get user location timed out.',
                            type: "error"
                        }).then(function (result) {

                        });
                        break;
                    case error.UNKNOWN_ERROR:
                        swal({
                            title: 'An unknown error occurred.',
                            type: "error"
                        }).then(function (result) {

                        });
                        break;
                }
            }
        });
    };

    var finish_visit = function () {
        $('#finish-visit').off('click').on('click', function (e) {
            e.preventDefault();
            var date = new Date();
            var day = date.getDate().toString();
            var month = (date.getMonth() + 1).toString();
            var year = date.getFullYear().toString();
            if (day.toString().length < 2) {
                day = '0' + day.toString();
            }
            if (month.toString().length < 2) {
                month = '0' + month.toString();
            }
            var strDate = day + "-" + month + "-" + year;

            var hours = date.getHours().toString();
            var minutes = date.getMinutes().toString();
            var seconds = date.getSeconds().toString();
            if (hours.toString().length < 2) {
                hours = '0' + hours.toString();
            }
            if (minutes.toString().length < 2) {
                minutes = '0' + minutes.toString();
            }
            if (seconds.toString().length < 2) {
                seconds = '0' + seconds.toString();
            }
            var strtime = hours + ":" + minutes + ":" + seconds;

            $('#appointment-date').val(strDate);
            $('#end_time').val(strtime);

            $('#appointment-status-running').addClass('d-none');
            $('#appointment-status-completed').removeClass('d-none');

            $('#status-appointment-accomplished').prop('disabled', false);
            $('#status-appointment').prop('disabled', true);

            url = $('#finish-visit').data('url');

            data = [];
            data.appointment_status_id = $('#status-appointment-accomplished').val();
            data.end_date = strDate;
            data.end_time = strtime;

            var query_data = serialize(data);
            window.location.replace(url + '?' + query_data);
        });
    };

    var loadEditTask = function () {
        $('.icon-edit-task').on('click', function () {
            $('#cnt_creation').removeClass('d-none');
            $('#delete_task').show();
            $('.cnt-user-garage').hide();
            var $drag_and_drop = $('.dragdrop-js');
            $drag_and_drop.addClass('dragdrop-js-tmp');
            $drag_and_drop.removeClass('dragdrop-js');
            var element = $(this);
            var url = element.data('url') + '/' + element.data('id');
            var request = PeticionAjax.post(url);
            request.done(function (data) {
                $('#task-form').html(data);
                $('#title').val('');
                selectUserBDM();
                selectDistributor();
                $('.tasks_types').on('change', function () {
                    propCheckedConditions();
                });
                $('.select2-multiple').select2();
                $('#create_task').unbind('click');
                $('.icon-edit-task').unbind('click');
                $('.cancel_task').unbind('click');
                createTaskAppointment();
                deleteTask(element.data('id'));
                Tasks.load();
                FormHelper.load();
                JQueryHelper.load();
                Appointments.loadInputGarage();
                $drag_and_drop.addClass('dragdrop-js');
                $drag_and_drop.removeClass('dragdrop-js-tmp');
            });
        });
    };

    var hideShowComments = function () {
        $('#span-comments').on('click', function () {
            $('#comment-ajax').slideToggle();
            if ($(this).hasClass('ion-chevron-down')) {
                $(this)
                    .removeClass('ion-chevron-down')
                    .addClass('ion-chevron-up');
            } else {
                $(this)
                    .removeClass('ion-chevron-up')
                    .addClass('ion-chevron-down');
            }
        });
    };

    var hideShowSaveAndSend = function () {
        if ($('#appointment-status').val() != $('#appointment-status').data('pending')) {
            $('#cnt_save_and_send').addClass('d-none');
            $('#cnt_save_and_send').removeClass('d-inline');
        } else {
            $('#cnt_save_and_send').removeClass('d-none');
            $('#cnt_save_and_send').addClass('d-inline');
        }
        $('#appointment-status').on('change', function () {
            if ($(this).val() != $(this).data('pending')) {
                $('#cnt_save_and_send').addClass('d-none');
                $('#cnt_save_and_send').removeClass('d-inline');
            } else {
                $('#cnt_save_and_send').removeClass('d-none');
                $('#cnt_save_and_send').addClass('d-inline');
            }
        });
    };

    var loadInputGarage = function () {
        var garage_select = $('#garage_name');
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
                        page: params.page || 1,
                        user_assigned_id: $('#assigned-to-appointments').val()
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
        $('#distributor_name').select2({
            minimumInputLength: 2,
            language: {
                inputTooShort: function (args) {
                    return $.i18n._('General.Enter_at_least_two_characters');
                },
            },
        });
        visitContactSelect2();
        garage_select.val($('#garage_id').val()).trigger('change');

    };

    var visitContact = function () {
        if ($('#visit_contact').val() != '' && $('#visit_contact').val() != undefined) {
            $('#visit_contact_text').val('').attr('disabled', true);
        }

        $('#visit_contact').on('change', function () {
            if ($('#visit_contact').val() != '' && $('#visit_contact').val() != undefined) {
                $('#visit_contact_text').val('').attr('disabled', true);
            } else {
                $('#visit_contact_text').attr('disabled', false);
            }
        });
    };

    var disabledFields = function () {
        if ($('#completed_appointment').length > 0) {
            $('.' + $('#completed_appointment').data('class')).each(function () {
                $(this).datepicker('destroy');
                $(this).off('click');
                $(this).prop('readonly', true);
                if ($(this).is(':radio:not(:checked)')) {
                    $(this).attr('disabled', true);
                } else if ($(this).hasClass('select2-multiple')) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: $(this).attr('name'),
                        class: 'disabled_fields_hidden',
                        value: $(this).val()
                    }).appendTo('#appointment-form');
                    $(this).attr('disabled', true);
                }
            });
            if ($('#appointment-date').prop('readonly')) {
                $('#appointment-date').removeAttr('data-open');
            }
        }
    }

    var visitContactSelect2 = function () {
        $('#garage_name').on('change', function () {
            if ($('#garage_name').val() != '' && $('#garage_name').val() != undefined) {
                data = {};
                url = $('#cnt_contact_visits').data('url');
                data.garage_id = $(this).val();
                data.distributor_id = false;
                data.appointment_id = $('#appointment-id').val();
                var request = PeticionAjax.post(url, data);
                request.done(function (res) {
                    $('#distributor_objectives').addClass('d-none');
                    $('#cnt_contact_visits').html(res);
                });
            }
        });

        $('#distributors_selector_id-js').on('change', function () {
            if ($('#distributors_selector_id-js').val() != '' && $('#distributors_selector_id-js').val() != undefined) {
                data = {};
                url = $('#cnt_contact_visits').data('url');
                data.garage_id = false;
                data.distributor_id = $(this).val();
                data.appointment_id = $('#appointment-id').val();
                $("#distributor-inputs-js").removeAttr('hidden');
                var request = PeticionAjax.post(url, data);
                request.done(function (res) {
                    $('#cnt_contact_visits').html(res);
                    data2 = {};
                    data2.garage_id = false;
                    data2.distributor_id = data.distributor_id;

                    url2 = $('#get_distributor_objectives').data('url');
                    var request2 = PeticionAjax.post(url2, data2);
                    request2.done(function (data) {
                        $('#distributor_objectives').removeClass('d-none');
                        $('#distributor_management_objectives').html(data);
                    });
                });
            }
        });
    }

    var onClickNextAppointment = function () {
        $('#next_appointment_create').on('click', function () {
            let nextAppointmentModal = new Foundation.Reveal($('#nextAppointmentModal'));
            nextAppointmentModal.open();
            $('#start_time_next').val('00:00');
            $('#end_time_next').val('00:00');
            $('#appointment-date_next').val('');
            $('#reminder_next').prop('checked', false);
        });

        $('#create_next_appointment').on('click', function () {

            if (
                !$('#start_time_next').val() ||
                !$('#end_time_next').val() ||
                !$('#appointment-date_next').val()
            ) {
                swal(
                    'Error',
                    $('#create_next_appointment').data('error'),
                    "error"
                );
            }
            else {
                PeticionAjax.mostrarCargando();
                data = {};
                url = $('#create_next_appointment').data('url');
                data.start_time = $('#start_time_next').val();
                data.end_time = $('#end_time_next').val();
                data.appointment_date = $('#appointment-date_next').val();
                data.reminder = $('#reminder_next').prop('checked');
                data.user_assigned_id = $('#create_next_appointment').data('user');
                data.user_creation_id = $('#create_next_appointment').data('user');
                data.garage_id = $('#garage_id').val() ? $('#garage_id').val() : $('#garage_name').val();
                data.distributor_id = $('#distributor_id').val() ? $('#distributor_id').val() : $('#distributor_name').val();
                var request = PeticionAjax.post(url, data);
                request.done(function (res) {
                    swal(
                        $.i18n._('General.Changes_saved'),
                        $('#create_next_appointment').data('success'),
                        "success"
                    );
                    $('a.close-modal').trigger('click');
                    PeticionAjax.ocultarCargando();
                    location.reload();
                });
            }
        });
    }

    var addBDMContact = function () {
        $('#btn_add_bdm').on('click', function (event) {
            event.preventDefault();
            if ($('#distributor_name2').val() != null) {
                var url = $('#btn_add_bdm').data('url');
                var data = {};
                data.objective_id = $('#distributor_name2').val();
                data.appointment_id = $('#appointment-id').val();
                data.type = 1;
                var request = PeticionAjax.post(url, data);
                request.done(function () {
                    location.reload();
                });
            }
        });
    };

    var addVisualObjective = function () {
        $('#btn_add_visual_objective').on('click', function (event) {
            event.preventDefault();
            let selectedObjectiveId = $('#distributor_name2').val();
            if (!isNaN($('#distributor_name2').val())) {
                selectedObjectiveId = $('#distributor_name2').val();
                $('.objective-hidden[data-objective-id="' + selectedObjectiveId + '"]').removeAttr('hidden');
                $('#distributor_name2').find(':selected').remove();
                $('#distributor_name2').trigger('change');
            } else {
                let selectedObjectiveId = $('#distributor_name2').val();
                let existingRow = $('.item-distributor[data-objective-id="' + selectedObjectiveId + '"]');
                if (existingRow.length > 0) {
                    return;
                }

                let row = document.createElement('tr');
                row.classList.add('item-distributor', 'objective-hidden');
                row.setAttribute('data-objective-id', selectedObjectiveId);

                let td1 = document.createElement('td');
                td1.classList.add('medium-4');
                td1.textContent = selectedObjectiveId;

                let hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'PersonalObjective[' + selectedObjectiveId + '][id]');
                hiddenInput.setAttribute('value', selectedObjectiveId);
                hiddenInput.setAttribute('id', 'personalNameObjectiveKey' + selectedObjectiveId);
                td1.appendChild(hiddenInput);

                let td2 = document.createElement('td');
                td2.classList.add('c-defecto', 'medium-4');
                let selectInput = document.createElement('select');
                selectInput.setAttribute('name', 'PersonalObjective[' + selectedObjectiveId + '][status]');
                selectInput.setAttribute('id', 'personalNameObjectiveStatus' + selectedObjectiveId);
                selectInput.setAttribute('disabled', 'disabled');
                selectInput.classList.add('select2-multiple');
                let option = document.createElement('option');
                option.setAttribute('value', '0');
                option.setAttribute('selected', 'selected');
                option.textContent = 'Pending';
                selectInput.appendChild(option);
                td2.appendChild(selectInput);

                let td3 = document.createElement('td');
                td3.classList.add('c-defecto', 'medium-4');
                let textarea = document.createElement('textarea');
                textarea.setAttribute('name', 'PersonalObjective[' + selectedObjectiveId + '][comment]');
                textarea.setAttribute('id', 'personalNameComment' + selectedObjectiveId);
                textarea.setAttribute('rows', 1);
                textarea.setAttribute('disabled', 'disabled');
                td3.appendChild(textarea);

                row.appendChild(td1);
                row.appendChild(td2);
                row.appendChild(td3);
                let table = document.getElementById('sort-contacts');
                table.appendChild(row);
            }

        });
    };

    var distributorsDropdown = function () {
        $('#distributors_selector_id-js').select2({
            ajax: {
                url: $('#distributors_selector_id-js').data('url'),
                delay: 200,
                tags: true,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        term: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
            },
            minimumInputLength: 3,
            multiple: true,
        });

        if ($('#distributors_selector_id-js').length && $('#distributors_selector_id-js').data('selected-distributors').length) {
            $('#distributors_selector_id-js').data('selected-distributors').forEach(distributor => {
                var newOption = new Option(distributor.text, distributor.id, true, true);
                $('#distributors_selector_id-js').append(newOption).trigger('change');
            });
            $('#distributors_selector_id-js').removeAttr('data-selected-distributors');
        }
    }

    var submitCheckHiddenObjectives = function () {
        $('#appointment-form').on('submit', function () {
            $('.objective-hidden').each(function () {
                var hiddenInputs = $(this).find('input, select, textarea');
                if ($(this).is(':hidden')) {
                    hiddenInputs.prop('disabled', true);
                } else {
                    hiddenInputs.prop('disabled', false);
                }
            });
        });
    };

    var selectUserBDM = function () {
        $('.select2Dinamico_user_bdm').select2({
            placeholder: "",
            allowClear: true,
            tags: true
        });

        $('.update_users_bdm').select2({
            ajax: {
                url: '/appointments/get_users_bdm_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#user_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 3,
            allowClear: true,
        });
    };

    var propCheckedConditions = function () {
        if ($('#task_type_user').prop('checked')) {
            $('#garage_name_assigned_to').empty().trigger('change');
            $('#distributor_name_assigned_to').empty().trigger('change');
            $('#send_to').val(0);
        } else if ($('#task_type_garage').prop('checked')) {
            $('#contact_list').val('').trigger('change');
            $('#assigned-to').val('').trigger('change');
            $('#distributor_name_assigned_to').empty().trigger('change');
            $('#send_to').val(1);
        } else if ($('#task_type_distributor').prop('checked')) {
            $('#contact_list').val('').trigger('change');
            $('#assigned-to').val('').trigger('change');
            $('#garage_name_assigned_to').empty().trigger('change');
            $('#send_to').val(2);
        }
    };

    serialize = function (obj) {
        var str = [];
        for (var p in obj)
            if (obj.hasOwnProperty(p)) {
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            }
        return str.join("&");
    }

    return {
        load: function () {
            hide_cnt_feedback();
            select();
            selectDistributor();
            searchTime();
            loadBehaviour();
            loadNotesFeedback();
            length_container();
            loadDeleteDocuments();
            appointments();
            createTaskAppointment();
            completeTask();
            unCheckTask();
            unLockTask();
            tooltipTask();
            downloadTooltipTask();
            sendComment();
            loadMore();
            deleteAppointment();
            cancelAppointment();
            deleteEvent();
            add();
            edit();
            check_send();
            check_send_event();
            start_visit();
            finish_visit();
            add_follow_up_visit();
            hideShowComments();
            hideShowSaveAndSend();
            onClickNextAppointment();
            addBDMContact();
            addVisualObjective();
            submitCheckHiddenObjectives();
        },
        assigned_to: function () {
            assigned_to();
        },
        loadButtons: function () {
            loadButtons();
        },
        loadEditTask: function () {
            loadEditTask();
        },
        loadInputGarage: function () {
            loadInputGarage();
        },
        completeTask: function () {
            completeTask();
        },
        unCheckTask: function () {
            unCheckTask();
        },
        visitContact: function () {
            visitContact();
        },
        disabledFields: function () {
            disabledFields();
        },
        distributorsDropdown: function () {
            distributorsDropdown();
        }
    }
})();
