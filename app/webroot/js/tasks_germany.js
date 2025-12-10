$(document).ready(function () {
    data_filters = {};

    Tasks.load();
    Tasks.checkActiveTasks();
    Tasks.checkActiveTopics();
    Tasks.hideCnt();
    if (typeof tasks_list_array_js !== 'undefined') {
        Tasks.addNewTask();
    }
    if (typeof topics_list_array_js !== 'undefined') {
        Tasks.addNewTopic();
    }

});

var Tasks = (function () {

    var loadAssignedTo = function () {
        // if($('#contact_list').val() != '' && $('#contact_list').val() != undefined) {
        //     $('#task_type_user').prop('checked', true);
        //     $('#task_type_garage').prop('disabled', true);
        //     $('#contact_list').prop('disabled',true);
        //     $('#send_to').val(0);
        // } else if($('#garage_name_assigned_to').val() != null){
        //     $('#garage_name_assigned_to').prop('disabled',true);
        //     $('#task_type_user').prop('disabled',true);
        //     $('#task_type_distributor').prop('disabled',true);
        //     $('#garage_filter_branch').prop('disabled', true);
        //     $('#garage_filter_bdm').prop('disabled', true);
        //     $('#garage_filter_rsm').prop('disabled', true);
        //     $('#garage_filter_customer_status').prop('disabled', true);
        //     $('#assigned_to_garage').removeClass('d-none');
        //     $('#assigned_to_contact').addClass('d-none');
        //     $('#assigned_to_distributor').addClass('d-none');
        //     $("label[for='task_type_garage']").trigger('click');
        //     $('#send_to').val(1);
        // } else if($('#distributor_name_assigned_to').val() != null){
        //     $('#distributor_name_assigned_to').prop('disabled',true);
        //     $('#task_type_user').prop('disabled',true);
        //     $('#task_type_garage').prop('disabled',true);
        //     $('#distributor_filter_bdm').prop('disabled', true);
        //     $('#distributor_filter_rsm').prop('disabled', true);
        //     $('#assigned_to_distributor').removeClass('d-none');
        //     $('#assigned_to_garage').addClass('d-none');
        //     $('#assigned_to_contact').addClass('d-none');
        //     $("label[for='task_type_distributor']").trigger('click');
        //     $('#send_to').val(2);
        // }

        // assignedTo();
        $('.tasks_types').on('change', function () {
            propCheckedConditions();
        });

        // $('#contact_list').on('change',function(){
        //     assignedTo();
        // });
    };

    var assignedTo = function () {
        var contact_list = $('#contact_list');
        if (contact_list.length > 0) {
            var url = contact_list.data('url');
            var data = {};
            data.contact_list_id = contact_list.val();
            data.user_assigned_id = $('#user_assigned_id').val();
            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                var assigned = $('#assigned_to');
                assigned.html(data);
                $('#assigned-to').select2();
                assigned.find('label').css('top', '0px');
                if ($('#assigned-to').val() != '') {
                    $('#assigned-to').prop('disabled', true);
                    $('#contact_list').prop('disabled', true);
                }
            });
        }
    };

    var debriefTaskAndTopics = function () {
        $('.word-cloud').off('click').on('click', function () {
            var $this = $(this);
            var url = $(this).data('url');
            var data = {};
            data.debrief_task_id = $(this).data('task-id');
            data.appointment_id = $('#appointment-id').val();
            data.deadline = null;
            if ($('#task_deadline_mandatory').length > 0) {
                swal({
                    title: $.i18n._('CRM.Deadline'),
                    text: $.i18n._('Validation.Mandatory_to_choose_a_deadline_v2'),
                    input: 'text',
                    inputPlaceholder: moment(new Date()).format("DD-MM-YYYY"),
                    showCancelButton: true,
                    showCloseButton: true,
                    inputValidator: function (value) {
                        var due_date = value;
                        var dd = due_date.split('-')[0];
                        //dd = parseInt(dd) + 1;
                        //dd = dd.toString();
                        var mm = due_date.split('-')[1];
                        var yyyy = due_date.split('-')[2];
                        due_date = yyyy + '-' + mm + '-' + dd;

                        return new Promise(function (resolve) {
                            if (value === "") {
                                resolve($.i18n._('CRM.Enter_valid_date'));
                                return false
                            } else {
                                var date_tmp = moment(value, 'DD-MM-YYYY', true);
                                if (date_tmp.isValid()) {
                                    data.deadline = value;
                                    resolve();
                                    if ($this.data('no-promt') == '' && $this.find('.word-cloud-title').data('garage') != '') {
                                        setTimeout(function () {
                                            _assignToAPerson($this, url, data);
                                        }, 1000);
                                    } else {
                                        _debriefTaskAndTopics($this, url, data)
                                    }
                                } else {
                                    resolve($.i18n._('CRM.Enter_valid_date'));
                                    return false
                                }
                            }
                        })
                    }
                });
            } else {
                if ($this.data('no-promt') == '' && $this.find('.word-cloud-title').data('garage') == '') {
                    _assignToAPerson($this, url, data);
                } else {
                    _debriefTaskAndTopics($this, url, data)
                }
            }
        });
    };

    var _assignToAPerson = function ($this, url, data) {
        var users = {};
        $("#users_list").find("option").each(function () {
            if ($(this).val() != '') {
                users[$(this).val()] = $(this).text();
            }
        });
        swal({
            title: $.i18n._('Visit.Assign_to_user'),
            text: $.i18n._('Visit.Assign_to_user'),
            input: 'select',
            inputOptions: users,
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
                        data.user_assigned_id = value;
                        resolve();
                        _debriefTaskAndTopics($this, url, data);
                    }
                })
            }
        });
    }

    var _debriefTaskAndTopics = function (word_cloud, url, data) {
        var request = PeticionAjax.post(url, data);
        request.done(function (data) {
            $('#cnt_task').html(data);
            $('#my_tasks').removeClass('d-none');
            if (word_cloud.find('.word-cloud-title').data('garage') > 0) {
                $('#cnt_task_garage_parent').removeClass('d-none');
            } else if (
                word_cloud.find('.word-cloud-title').data('garage') == '' &&
                word_cloud.find('.word-cloud-title').data('contact_list') == '' &&
                word_cloud.find('.word-cloud-title').data('user_assigned') == ''
            ) {
                $('#cnt_task_garage_parent').removeClass('d-none');
            }
            $('#garage_name').trigger('change');
            $('#distributor_name').trigger('change');
            swal({
                type: 'success',
                title: $.i18n._('General.Well_added')
            });
            if (typeof Appointments === "object") {
                Appointments.loadEditTask();
                Appointments.completeTask();
                Appointments.unCheckTask();
            }
            checkActiveTasks();
        });
    }

    var addTopic = function () {
        $('.word-cloud-topic').off('click').on('click', function () {
            var created_item = $(this);
            var create = true;
            $('.topic-item-input').each(function () {
                if ($(this).data('topic-id') == created_item.data('topic-id')) {
                    create = false;
                }
            });
            if (create) {
                var content =
                    '<input type="hidden" name="data[AppointmentTopic][]" class="topic-item-input" data-topic-id="' + created_item.data('topic-id') + '" value="' + created_item.data('topic-id') + '">' +
                    '<div class="topic-item" data-topic-id="' + created_item.data('topic-id') + '">' +
                    '<span class="topic-value" data-topic-id="' + created_item.data('topic-id') + '">' + created_item.data('topic-title') + '</span>' +
                    '<i class="ion-close-circled"></i>' +
                    '</div>';
                $('#cnt-topics').append(content);
                swal({
                    type: 'success',
                    title: $.i18n._('General.Well_added')
                });
                deleteTopic();
                Tasks.checkActiveTopics();
            } else {
                $(this).removeClass('active');
                $('.topic-item[data-topic-id="' + $(this).data('topic-id') + '"]').find('i').trigger('click');
            }
            var elems = $('.cnt-word-cloud-topic').find('.word-cloud-topic').sort(sortMe);
            var elems_clone = elems.clone();
            $('.cnt-word-cloud-topic').html('');
            elems.each(function (key) {
                concat_string = '';
                concat_string += '<div class="columns medium-3 end">';
                concat_string += elems_clone[key].outerHTML;
                concat_string += '</div>';
                $('.cnt-word-cloud-topic').append(concat_string);
                Tasks.checkActiveTopics();
            });
        });
    };

    var addNewTask = function () {
        var cnt_word_cloud_task = $('.cnt-word-cloud-task');
        var tasks_list_array_js_tmp = tasks_list_array_js;
        var total_task = $('.cnt-word-cloud-task').find('.word-cloud').length;
        $('.cnt-word-cloud-task').find('.word-cloud').each(function (index) {
            if (!isNaN($(this).data('task-id'))) {
                var task_id_remove = $(this).data('task-id');
                delete tasks_list_array_js_tmp[task_id_remove];
            }
        });

        tasks_id_array_js_tmp = $.map(tasks_list_array_js_tmp, function (value, index) {
            return [index];
        });
        tasks_list_array_js_tmp = $.map(tasks_list_array_js_tmp, function (value, index) {
            return [value];
        });

        $('#add-word-cloud-task').off('click').on('click', function () {
            swal({
                title: $.i18n._('Task.New_task'),
                input: 'select',
                inputOptions: tasks_list_array_js_tmp,
                showCancelButton: true,
                onOpen: function () {
                    var $swal_select = $('.swal2-select');
                    $swal_select.select2();
                    //$('.select2').addClass("m-vertical-swal");
                    $swal_select.on('select2:open', function () {
                        $('.swal2-container').next('.select2-container').addClass('z-index-swal2');
                    });
                    $swal_select.on('select2:close', function () {
                        $('.swal2-container').next('.select2-container').removeClass('z-index-swal2');
                    });
                }
            }).then(function (result) {
                var url = '/tasks/ajax_get_data_debrief_task';
                var data = {};
                data.debrief_task_id = tasks_id_array_js_tmp[Number(result.value)];
                PeticionAjax.postJSON(url, data).done(function (data) {
                    var icon_type = '';
                    if (data.garage > 0) {
                        icon_type = '<span class="ion-android-car" style="padding-right: 5px; color: #de7b39;"></span>';
                    } else if (data.contact_list != null || data.user_assigned != null) {
                        icon_type = '<span class="ion-android-person" style="padding-right: 5px; color: #de7b39;"></span>';
                    }
                    if (result.dismiss == undefined) {
                        var last_word_cloud = cnt_word_cloud_task.find('.word-cloud').last();
                        tasks_list_array_js[last_word_cloud.data('task-id')] = last_word_cloud.find('span').text();
                        last_word_cloud.remove();
                        for (var task in tasks_array_js) {
                            if (tasks_array_js[task].DebriefTask.id == tasks_id_array_js_tmp[Number(result.value)]) {
                                var title = tasks_array_js[task].DebriefTask['title_' + language_code];
                            }
                        }
                        cnt_word_cloud_task.prepend(
                            '<div class="columns medium-3">' +
                            '<div  class="word-cloud" data-task-id="' + tasks_id_array_js_tmp[Number(result.value)] + '" data-url="/tasks/ajax_add_debrief_task">' +
                            '<div class="word-cloud-title" style="color: #de7b39;margin-right: 2px;" data-garage="' + data.garage + '" data-contact_list="' + data.contact_list + '" data-user_assigned="' + data.user_assigned + '"' + ' >' + icon_type + '    ' + title + ' </div>' +
                            '<i class="ion-checkmark-circled"></i>' +
                            '</div>' +
                            '</div>');
                        debriefTaskAndTopics();
                        addNewTask();
                        addTopic();
                        swal({
                            type: 'success',
                            title: $.i18n._('General.Well_added')
                        });
                    } else {
                        swal({
                            type: 'warning',
                            title: $.i18n._('General.Action_cancelled')
                        });
                    }
                });
            });
        });
    };

    var addNewTopic = function () {
        var cnt_word_cloud_topic = $('.cnt-word-cloud-topic');
        var topics_list_array_js_tmp = topics_list_array_js;
        var total_topic = $('.cnt-word-cloud-topic').find('.word-cloud').length;
        $('.cnt-word-cloud-topic').find('.word-cloud').each(function (index) {
            if (!isNaN($(this).data('topic-id'))) {
                var topic_id_remove = $(this).data('topic-id');
                delete topics_list_array_js_tmp[topic_id_remove];
            }
        });

        topics_id_array_js_tmp = $.map(topics_list_array_js_tmp, function (value, index) {
            return [index];
        });
        topics_list_array_js_tmp = $.map(topics_list_array_js_tmp, function (value, index) {
            return [value];
        });

        $('#add-word-cloud-topic').off('click').on('click', function () {
            var url = $(this).data('url');
            swal({
                title: $.i18n._('Task.New_topic'),
                input: 'text',
                // inputOptions: topics_list_array_js_tmp,
                inputValidator: function (value) {
                    return !value && $.i18n._('Constants.Error_topic_name_empty')
                },
                showCancelButton: true,
            }).then(function (result) {
                if (result.dismiss == undefined) {
                    var data = {};
                    data.DebriefTopic = {};
                    data.DebriefTopic.name_en = result.value;
                    PeticionAjax.postJSON(url, data).done(function (data) {
                        if (data){
                            cnt_word_cloud_topic.append(
                                '<div class="columns medium-3">' +
                                '<div class="word-cloud word-cloud-topic" data-topic-id="' + data.DebriefTopic.id + '" data-topic-title="' + data.DebriefTopic.name_en + '">' +
                                '<div class="word-cloud-title">' + data.DebriefTopic.name_en + '</div>' +
                                '<i class="ion-checkmark-circled"></i>' +
                                '</div>' +
                                '</div>');
                            addTopic();
                            addNewTopic();
                            $('*[data-topic-id="' + data.DebriefTopic.id + '"]').trigger('click');
                            swal({
                                type: 'success',
                                title: $.i18n._('General.Well_added')
                            });
                        } else {
                            swal({
                                type: 'warning',
                                title: $.i18n._('General.Action_cancelled')
                            });
                        }
                    });
                } else {
                    swal({
                        type: 'warning',
                        title: $.i18n._('General.Action_cancelled')
                    });
                }
                Tasks.checkActiveTopics();
            });
        });
    };

    var deleteTopic = function () {
        $('.topic-item').off('click', '.ion-close-circled').on('click', '.ion-close-circled', function () {
            var $this = $(this);
            $('.topic-item-input').each(function () {
                if ($(this).data('topic-id') == $this.parent().find('.topic-value').data('topic-id')) {
                    $(this).remove();
                }
            });
            $(this).parent().remove();
            Tasks.checkActiveTopics();
        });
    };

    var mandatory = function () {
        var due_date = $('#due-date');
        var mandatory = $('#mandatory');
        var cnt_mandatory = $('#cnt-mandatory');
        if (due_date.val() != '') {
            cnt_mandatory.show();
        } else {
            mandatory.prop('checked', false);
            cnt_mandatory.hide();
        }

        due_date.on('change', function () {
            if (due_date.val() != '') {
                mandatory.prop('checked', true);
                cnt_mandatory.show();
            } else {
                mandatory.prop('checked', false);
                cnt_mandatory.hide();
            }
        });
    };

    var taskStatus = function () {

        $("#select-status-js").on('change', function () {
            $('#task-status-hidden').val($("#select-status-js").val());
        });
        var separator = '-';

        $('#due-date').on('change', function () {
            var due_date = $('#due-date').val();
            var dd = due_date.split(separator)[0];
            var mm = due_date.split(separator)[1];
            var yyyy = due_date.split(separator)[2];
            due_date = yyyy + separator + mm + separator + dd;

            var date = new Date(due_date).getTime();
            var today_date = Date.now();

            var expired = document.getElementById($('#task-status-expired').attr('id'));
            var notExpired = document.getElementById($('#task-status-no-expired').attr('id'));
            if (date < today_date) {
                expired.style.display = 'inline';
                notExpired.style.display = "none";
            } else {
                notExpired.style.display = 'inline';
                expired.style.display = 'none';
            }
        });
    };

    var selectGarages = function () {
        var garage_select = $('#garage_name_assigned_to');
        setTimeout(function () {
            garage_select.select2({
                // placeholder: $.i18n._('Garage.Garage'),
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
                    url: garage_select.data('url'),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1,
                            garage_filter_branch: $('#garage_filter_branch').val(),
                            garage_filter_bdm: $('#garage_filter_bdm').val(),
                            garage_filter_rsm: $('#garage_filter_rsm').val(),
                            garage_filter_customer_status: $('#garage_filter_customer_status').val()
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
        }, 50);
    };

    var selectDistributors = function () {
        var distributor_select = $('#distributor_name_assigned_to');
        setTimeout(function () {
            distributor_select.select2({
                // placeholder: $.i18n._('Distributor.Distributor'),
                language: {
                    inputTooShort: function (args) {
                        return $.i18n._('General.Enter_at_least_two_characters');
                    },
                },
                ajax: {
                    url: distributor_select.data('url'),
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1,
                            distributor_filter_bdm: $('#distributor_filter_bdm').val(),
                            distributor_filter_rsm: $('#distributor_filter_rsm').val(),
                            trading_group_id: $('#trading_group_id').val(),
                            activity_id: $('#activity_id').val(),
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
            });
        }, 50);
    };

    var garageTask = function () {
        loadGarageTask();
        $('#garage_name').off('change').on('change', function (e, change) {
            if (change != 'no-task') {
                if ($(this).val() != '' && $(this).val() != null) {
                    $('#modal-follow-up-visit').show();
                    var $result = $(this);
                    var url = $('#get_data_garage_info').data('url');
                    var request = PeticionAjax.post(url + '/' + $result.val());
                    //var garage_name = $('#garage_name');
                    request.done(function (data) {
                        $('#garage_info').html(data);
                        GMaps.createSingleMapWithoutInfo();
                        Appointments.loadButtons();
                        Appointments.assigned_to();
                    });
                } else {
                    if ($('#distributor_name').val() == '') {
                        $('#go-to-garage').remove();
                    }
                }
                loadGarageTask();
            }
        });

        $('#save_task_form').on('click', function () {
            loadGarageTask();
        });
    };


    var loadGarageTask = function () {
        var garage_id = $('#garage_name').val();
        if (garage_id != undefined && garage_id != '') {
            var cnt_task_garage = $('#cnt_task_garage');
            var url = $('#ajax_garage_tasks_url').data('url');
            var data = {};
            data.garage_id = garage_id;
            if (url != undefined) {
                var request = PeticionAjax.post(url, data);
                request.done(function (data) {
                    cnt_task_garage.html(data);
                    modal_completed_garage_task();
                    countTasks();
                    // $('.radio_task_garage').on('change',function(){
                    //     var url = $(this).data('url');
                    //     var data = {};
                    //     data.task_id = $(this).data('task_id');
                    //     data.garage_id = $('#garage_name').val();
                    //     data.action = $(this).data('action');
                    //     PeticionAjax.post(url,data);
                    // });
                    $('.complete_task_garage').off('click').on('click', function () {
                        var $task = $(this);
                        var url = $task.attr('data-url');
                        var data = {};
                        data.task_id = $task.attr('data-id');
                        data.garage_id = $('#garage_name').val();
                        data.action = $task.attr('data-action');
                        var request = PeticionAjax.post(url, data);
                        request.done(function () {
                            if ($task.attr('data-action') == 'complete') {
                                $task.removeClass('ion-ios-checkmark-outline').addClass('ion-ios-checkmark c-exito');
                                $task.attr('data-action', 'revert');
                            } else {
                                $task.attr('data-action', 'complete');
                                $task.removeClass('ion-ios-checkmark c-exito').addClass('ion-ios-checkmark-outline');
                            }
                        });
                    });
                })
            }
        }
    };
    var distributorTask = function () {
        loadDistributorTask();
        $('#distributor_name').off('change').on('change', function (e, change) {
            if (change != 'no-task') {
                if ($(this).val() != '' && $(this).val() != null) {
                    $('#modal-follow-up-visit').show();

                    var $result = $(this);
                    var url = $('#get_data_distributor_info').data('url');
                    var request = PeticionAjax.post(url + '/' + $result.val());
                    //var distributor_name = $('#distributor_name');
                    request.done(function (data) {
                        $('#garage_info').html(data);
                        GMaps.createSingleMapWithoutInfo();
                        Appointments.loadButtons();
                        Appointments.assigned_to();
                    });
                } else {
                    if ($('#distributor_name').val() == '') {
                        $('#go-to-distributor').remove();
                    }
                }
                loadDistributorTask();
            }
        });


        $('#save_task_form').on('click', function () {
            loadDistributorTask();
        });
    };


    var loadDistributorTask = function () {
        var distributor_id = $('#distributor_name').val();
        if (distributor_id != undefined && distributor_id != '') {
            var cnt_task_garage = $('#cnt_task_garage');
            var url = $('#ajax_distributor_tasks_url').data('url');
            var data = {};
            data.distributor_id = distributor_id;
            if (url != undefined) {
                var request = PeticionAjax.post(url, data);
                request.done(function (data) {
                    cnt_task_garage.html(data);
                    modal_completed_distributor_task();
                    countTasks();
                    // $('.radio_task_distributor').on('change',function(){
                    //     var url = $(this).data('url');
                    //     var data = {};
                    //     data.task_id = $(this).data('task_id');
                    //     data.distributor_id = $('#distributor_name').val();
                    //     data.action = $(this).data('action');
                    //     PeticionAjax.post(url,data);
                    // });
                    $('.complete_task_distributor').off('click').on('click', function () {
                        var $task = $(this);
                        var url = $task.attr('data-url');
                        var data = {};
                        data.task_id = $task.attr('data-id');
                        data.distributor_id = $('#distributor_name').val();
                        data.action = $task.attr('data-action');
                        var request = PeticionAjax.post(url, data);
                        request.done(function () {
                            if ($task.attr('data-action') == 'complete') {
                                $task.removeClass('ion-ios-checkmark-outline').addClass('ion-ios-checkmark c-exito');
                                $task.attr('data-action', 'revert');
                            } else {
                                $task.attr('data-action', 'complete');
                                $task.removeClass('ion-ios-checkmark c-exito').addClass('ion-ios-checkmark-outline');
                            }
                        });
                    });
                })
            }
        }
    };

    var modal_completed_garage_task = function () {
        $('#modal-completed-garage-task').off('click').on('click', function (e) {
            var garage_id = $('#garage_name').val();
            var url = $('#modal-completed-garage-task').data('url');

            data = {};
            data.garage_id = garage_id;
            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                $('#modal_completed_garage_task').html(data);
                modal_completed_garage_task();
            });
        });
    };

    var modal_completed_distributor_task = function () {
    };

    var tabsTask = function () {

        $('#existing_view-js').on('change', function () {
            var view_index = $('#existing_view-js').val();

            $('#selected_tab').val(view_index);
            var $action = $('#existing_view-js');
            var url = $(this).data('url_' + view_index);
            var data = {};
            data.title = $('#task_title').val();
            data.task_status_id = $('#status').val();
            data.limit_date_from = $('#dead_line_from').val();
            data.limit_date_to = $('#dead_line_to').val();

            if (view_index != 5) {
                remove_filters();
                if (view_index == 2) {
                    $('#filter_assigned_to').show();
                    data.user_assigned_id = $('#assigned_to').val();
                }
                if (view_index == 1 || view_index == 3 || view_index == 4) {
                    $('#filter_created_by').show();
                    data.user_creation_id = $('#created_by').val();
                }
            } else {
                $('#filter_created_by').show();
                $('#filter_assigned_to').show();
                // $('#filter_limit_date').addClass('clear');
                data.user_assigned_id = $('#assigned_to').val();
                data.user_creation_id = $('#created_by').val();
            }

            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                $('#results_table_ajax').html(data);
                checkTask($action);
                uncheckTask($action);
                deleteTaskAppointment($action);
                paginateTasks($action);
                setParamsFilters();

                $('.table-tracking').basictable('destroy');
                $('.table-tracking').basictable();
            })
        });

        $('#existing_view-js').trigger('change');

    };

    var setParamsFilters = function () {
        data_filters.tab = $('#selected_tab').val();
        data_filters.title = $('#task_title').val();
        data_filters.user_creation_id = $('#created_by').val();
        data_filters.user_assigned_id = $('#assigned_to').val();
        data_filters.task_status_id = $('#status').val();
        data_filters.limit_date_from = $('#dead_line_from').val();
        data_filters.limit_date_to = $('#dead_line_to').val();
    };

    var paginateTasks = function ($action) {
        $('#PaginatorPaginatorSizeForm').find('a').off('click').on('click', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var request = PeticionAjax.post(url, data_filters);
            request.done(function (data) {
                $('#results_table_ajax').html(data);
                paginateTasks();
                deleteTaskAppointment($action);
                checkTask($action);
                uncheckTask($action);
            });
        });
        $('#PaginatorPaginatorSizeForm').find('input[type=submit]').off('click').on('click', function (e) {
            e.preventDefault();
            var url = $('#existing_view-js').data('url_' + $('#existing_view-js').val());
            data_filters.paginator_size = $('#PaginatorPaginationSize').val();
            var request = PeticionAjax.post(url, data_filters);
            request.done(function (data) {
                $('#results_table_ajax').html(data);
                paginateTasks();
                deleteTaskAppointment($action);
                checkTask($action);
                uncheckTask($action);
            });
        });
    }

    var paginateSubTasks = function () {
        $('#PaginatorPaginatorSizeForm').find('a').off('click').on('click', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var request = PeticionAjax.post(url, data_filters);
            request.done(function (data) {
                $('#results_table_ajax').html(data);
                paginateSubTasks();
                deleteSubTask();
                checkTask();
                uncheckTask();
            });
        });
    }

    var remove_filters = function () {
        $('#filter_created_by').hide();
        $('#filter_assigned_to').hide();
        $('#filter_limit_date').removeClass('clear');
    }

    var checkTask = function ($action) {
        $(".check-task-js").click(function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            var element = $(this);
            var urlHref = this.href;

            var $this = $(this);
            var task_id = element.data('id');
            var n_garages_task = $('#garages-task-' + task_id).val();

            if (typeof (n_garages_task) == "undefined" || n_garages_task < 2) {
                swal({
                    title: $(this).data('confirmmsg'),
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: primary_color,
                    confirmButtonText: $(this).data('yes'),
                    cancelButtonText: $(this).data('no')
                }).then(function (result) {
                    if (result.value) {
                        var request = PeticionAjax.post(urlHref);
                        request.done(function () {
                            var garage_id = element.siblings('.garage-data-uncheck').data('garage-id');

                            if ($('.subtask-type').length > 0 || $('#home').length > 0) {
                                $('.check-task-js').each(function () {
                                    if ($(this).data('id') == element.data('id')) {
                                        $(this).find('span').removeClass('c-informacion');
                                        $(this).find('span').addClass('c-exito');
                                        $(this).data('confirmmsg', $('#confirm-uncheck').data('msg'));
                                        $(this).removeClass('check-task-js');
                                        $(this).addClass('uncheck-task-js');
                                        $(this).attr("href", $('#confirm-uncheck').data('url') + '/' + element.data('id'));
                                    }
                                });
                            } else {
                                element.find('span').removeClass('c-informacion');
                                element.find('span').addClass('c-exito');
                                element.data('confirmmsg', $('#confirm-uncheck').data('msg'));
                                element.removeClass('check-task-js');
                                element.addClass('uncheck-task-js');
                                element.attr("href", $('.garage-data-uncheck').data('url') + '/' + task_id + '/' + garage_id);
                            }

                            $(".uncheck-task-js").unbind();
                            uncheckTask($action);
                            deleteTaskAppointment($action);
                            var delete_trash = element.next();
                            var actions_column = element.parent();
                            var check_completed = element.find('span');
                            var edit_completed = element.parent().find('a').first();
                            element.closest('tr').find('.status_task').html($.i18n._('Task.Complete'));
                            element.closest('tr').find('.c-fallo').removeClass('c-fallo')
                            element.remove();
                            delete_trash.remove();
                            check_completed.remove();
                            check_completed.removeClass('c-informacion').addClass('c-exito');
                            actions_column.html('');
                            actions_column.append(edit_completed);
                            actions_column.append(check_completed);
                        });
                    }
                });
            } else {
                var url = $('#garages-task-' + task_id).data('url');
                window.location.href = url;
            }
        });
    };

    var uncheckTask = function ($action) {
        $(".uncheck-task-js").click(function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            var element = $(this);
            var urlHref = this.href;

            var task_id = element.data('id');
            var n_garages_task = $('#garages-task-' + task_id).val();
            $('.uncheck-task-js').first();

            if (typeof (n_garages_task) == "undefined" || n_garages_task < 2) {
                swal({
                    title: $(this).data('confirmmsg'),
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: primary_color,
                    confirmButtonText: $(this).data('yes'),
                    cancelButtonText: $(this).data('no')
                }).then(function (result) {
                    if (result.value) {
                        var request = PeticionAjax.post(urlHref);
                        request.done(function () {
                            var garage_id = element.siblings('.garage-data-check').data('garage-id');

                            if ($('.subtask-type').length > 0 || $('#home').length > 0) {
                                $('.uncheck-task-js').each(function () {
                                    if ($(this).data('id') == element.data('id')) {
                                        $(this).find('span').removeClass('c-exito');
                                        $(this).find('span').addClass('c-informacion');
                                        $(this).data('confirmmsg', $('#confirm-check').data('msg'));
                                        $(this).removeClass('uncheck-task-js');
                                        $(this).addClass('check-task-js');
                                        $(this).attr("href", $('#confirm-check').data('url') + '/' + element.data('id'));
                                    }
                                });
                            } else {
                                element.find('span').removeClass('c-exito');
                                element.find('span').addClass('c-informacion');
                                element.data('confirmmsg', $('#confirm-check').data('msg'));
                                element.removeClass('uncheck-task-js');
                                element.addClass('check-task-js');
                                element.attr("href", $('.garage-data-check').data('url') + '/' + task_id + '/' + garage_id);
                            }

                            $(".check-task-js").unbind();
                            checkTask($action);
                            deleteTaskAppointment($action);
                        });
                    }

                });
            } else {
                var url = $('#garages-task-' + task_id).data('url');
                window.location.href = url;
            }
        });
    };
    var deleteTaskAppointment = function ($action) {
        $(".delete-task-js").click(function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            var element = $(this);
            var urlHref = this.href;
            var url_redirect = element.data('url_redirect');
            swal({
                title: element.data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(urlHref);

                    request.done(function (data) {

                        if (result) {
                            swal({
                                title: $.i18n._("Constants.Message_well_deleted"),
                                type: "success",
                            }).then(function (result) {
                                window.location.replace(url_redirect);
                            });
                        } else {
                            swal({
                                title: $.i18n._('Constants.Message_bad_deleted'),
                                type: "error"
                            });
                        }
                        $('#results_table_ajax').html(data);
                        element.data('confirmmsg', $('#confirm-delete').data('msg'));
                        element.attr("href", $('#confirm-check').data('url') + '/' + element.data('id'));
                        $(".delete-task-js").unbind('click');
                        deleteTaskAppointment($action);
                        uncheckTask($action);
                        checkTask($action);
                        if ($action != undefined) {
                            if ($action.attr('id') == $('#tab-assigned-group-task').attr('id')) {
                                $('#check-group').prop('checked', false);
                            } else if ($action.attr('id') == $('#tab-assigned-customers-task').attr('id')) {
                                $('#check-customers').prop('checked', false);
                            }
                            $action.trigger('click');
                        }
                        $('#existing_view-js').trigger('change');
                    });
                }
            });
        });
    };

    var checkActiveTopics = function () {
        $('.word-cloud-topic').each(function () {
            var cloud_topic = $(this);
            cloud_topic.removeClass('active');
            $('#cnt-topics').find('.topic-item').each(function () {
                var topic_tmp = $(this);
                if (cloud_topic.data('topic-id') == topic_tmp.data('topic-id')) {
                    cloud_topic.addClass('active');
                }
            });
        });
        addTopic();
    };

    var checkActiveTasks = function () {
        $('.world-cloud-task').each(function () {
            var cloud_task = $(this);
            cloud_task.removeClass('active');
            $('#cnt-tasks').find('.task-item').each(function () {
                var task_tmp = $(this);
                if (cloud_task.data('task-id') == task_tmp.data('task-id')) {
                    cloud_task.addClass('active');
                }
            });
        });
    };

    var countTasks = function () {
        if ($('.task_garage').length > 0) {
            $('#cnt_task_garage_parent').removeClass('d-none');
        } else {
            $('#cnt_task_garage_parent').addClass('d-none');
        }
    };

    var submitForm = function () {
        if ($('#form-controller').val() == 'tasks') {
            $('#btn-guardar').on('click', function (e) {
                e.preventDefault(e);
                var form = $('#task-form');
                var sent_to = $('#send_to').val();
                if (sent_to == 1) {
                    if ($('#garage_name_assigned_to').val() == null) {
                        swal({
                            title: $.i18n._('Task.Confirm_submit'),
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: primary_color,
                            confirmButtonText: $.i18n._('General.Yes'),
                            cancelButtonText: $.i18n._('General.No')
                        }).then(function (result) {
                            if (result.value) {
                                form.submit();
                            }
                        });
                    } else {
                        form.submit();
                    }
                } else if (sent_to == 2) {
                    if ($('#distributor_name_assigned_to').val() == null) {
                        swal({
                            title: $.i18n._('Task.Confirm_submit_distributor'),
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: primary_color,
                            confirmButtonText: $.i18n._('General.Yes'),
                            cancelButtonText: $.i18n._('General.No')
                        }).then(function (result) {
                            if (result.value) {
                                form.submit();
                            }
                        });
                    } else {
                        form.submit();
                    }
                } else {
                    form.submit();
                }
            });
        }
    };

    var deleteSubTask = function () {
        $(".delete-subtask-js").off('click').on('click', function (event) {
            event.preventDefault();
            var element = $(this);
            var task_garage_id = $(this).data('task_garage_id');
            var task_distributor_id = $(this).data('task_distributor_id');
            var urlHref = this.href;
            swal({
                title: element.data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(urlHref + '?id=' + $('#subtask_id_query').val() + '&task_garage_id=' + task_garage_id + '&task_distributor_id=' + task_distributor_id);
                    request.done(function (data) {
                        $('#results_table_ajax').html(data);
                        deleteSubTask();
                        checkTask();
                        uncheckTask();
                        paginateSubTasks();
                    });
                }
            });
        });
    };

    var createModalTask = function () {
        $('.modal-task').off('click').on('click', function (e) {
            var url = $(this).data('url');
            var bdm_code = $(this).data('bdm-code');

            data = {};
            data.bdm_code = bdm_code;

            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                $('#modal_task').html(data);
                createModalTask();
                FormHelper.load();
                Select2.load();
                JQueryHelper.load();
            });
        });

        $('#save_submit_create_task').on('click', function () {

            var url = $(this).data('url');
            var data = {};

            if ($('#title').val() === '') {
                swal($.i18n._('Alert.Error'), $.i18n._('Task.Title_is_required'), "error");
            }
            else if ($('#body').val() === '') {
                swal($.i18n._('Alert.Error'), $.i18n._('Task.Body_is_required'), "error");
            }
            else {
                var formData = new FormData($('#task-form')[0]);
                var request = $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }

            request.done(function (data) {
                if (data === false) {
                    swal($.i18n._('Alert.Error'), $.i18n._('General.Error'), "error");
                }
                else if (data === 'error_file') {
                    swal($.i18n._('Alert.Error'), $.i18n._('Alert.Error_file'), "error");
                }
                else {
                    createModalTask();
                    $('a.close-modal').trigger('click');
                }

            })

        });

    };

    var deleteTask = function () {
        $('#task_delete').on('click', function (e) {
            e.preventDefault();
            var element = $(this);
            swal({
                title: $.i18n._('Alert.Sure?'),
                text: $.i18n._('Alert.No_revert'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#ff5648',
                cancelButtonColor: '#d33',
                confirmButtonText: $.i18n._('General.Yes'),
            }).then(function (result) {
                if (result.value) {
                    window.location = element.attr('href');
                }
            });
        });
    };

    var hideCnt = function () {
        if ($('.cnt-user-garage').data('action') == 'edit') {
            if (
                ($('#contact_list').val() != '' && $('#contact_list').val() != null) ||
                ($('#assigned-to').val() != '' && $('#assigned-to').val() != null) ||
                ($('#garage_name_assigned_to').val() != '' && $('#garage_name_assigned_to').val() != null) ||
                ($('#distributor_name_assigned_to').val() != '' && $('#distributor_name_assigned_to').val() != null)
            ) {
                $('.cnt-user-garage').hide();
                $('.filters_cnt').hide();
            }
        }
    };

    var sortMe = function (a, b) {
        return a.className < b.className ? 0 : a.className < b.className;
    }

    var outerHTML = function (node) {
        return node.outerHTML || new XMLSerializer().serializeToString(node);
    }

    var propCheckedConditions = function () {
        if ($('#task_type_user').prop('checked')) {
            $('#assigned_to_contact').show();
            $('#assigned_to_garage').hide();
            $('#assigned_to_distributor').hide();
            $('#garage_name_assigned_to').empty().trigger('change');
            $('#distributor_name_assigned_to').empty().trigger('change');
            $('#send_to').val(0);
        } else if ($('#task_type_garage').prop('checked')) {
            $('#assigned_to_contact').hide();
            $('#assigned_to_garage').show();
            $('#assigned_to_distributor').hide();
            $('#contact_list').val('').trigger('change');
            $('#assigned-to').val('').trigger('change');
            $('#distributor_name_assigned_to').empty().trigger('change');
            $('#send_to').val(1);
        } else if ($('#task_type_distributor').prop('checked')) {
            $('#assigned_to_contact').hide();
            $('#assigned_to_garage').hide();
            $('#assigned_to_distributor').show();
            $('#contact_list').val('').trigger('change');
            $('#assigned-to').val('').trigger('change');
            $('#garage_name_assigned_to').empty().trigger('change');
            $('#send_to').val(2);
        }
    };

    return {
        load: function () {
            loadAssignedTo();
            debriefTaskAndTopics();
            deleteTopic();
            selectGarages();
            selectDistributors();
            mandatory();
            addTopic();
            garageTask();
            distributorTask();
            taskStatus();
            tabsTask();
            checkTask();
            uncheckTask();
            submitForm();
            deleteTaskAppointment();
            deleteSubTask();
            createModalTask();
            deleteTask();
            propCheckedConditions();
        },
        addNewTask: function () {
            addNewTask();
        },
        addNewTopic: function () {
            addNewTopic();
        },
        checkActiveTopics: function () {
            checkActiveTopics();
        },
        checkActiveTasks: function () {
            checkActiveTasks();
        },
        hideCnt: function () {
            hideCnt();
        }
    }
})();
