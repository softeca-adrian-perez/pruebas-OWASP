$(document).ready(function () {
    Debrief.load();

});

var Debrief = (function () {

    var taskTypes = function () {
        $('.tasks_types').on('change', function () {
            if ($('#task_type_user').prop('checked')) {
                $('#assigned_to_contact').show();
                $('#assigned_to_garage').hide();
                $('#assigned_to_distributor').hide();
                $('#garage_name_assigned_to').val('0').select2();
                $('#distributor_name_assigned_to').val('0').select2();
                $('#send_to').val(0);
            } else if ($('#task_type_garage').prop('checked')) {
                $('#assigned_to_contact').hide();
                $('#assigned_to_garage').show();
                $('#assigned_to_distributor').hide();
                $('#contact_list').val('').trigger('change');
                $('#assigned-to').val('').trigger('change');
                $('#distributor_name_assigned_to').val('0').select2();
                $('#send_to').val(1);
                selectGarages();
            } else if ($('#task_type_distributor').prop('checked')) {
                $('#assigned_to_contact').hide();
                $('#assigned_to_garage').hide();
                $('#assigned_to_distributor').show();
                $('#contact_list').val('').trigger('change');
                $('#assigned-to').val('').trigger('change');
                $('#garage_name_assigned_to').val('0').select2();
                $('#send_to').val(2);
                selectDistributors();
            }
        });
    };

    var assignedTo = function () {
        $('#contact_list').on('change', function () {
            var contact_list = $('#contact_list');
            var url = contact_list.data('url');
            var data = {};
            data.contact_list_id = contact_list.val();
            data.user_assigned_id = $('#user_assigned_id').val();
            data.action = 'debrief';
            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                var assigned = $('#assigned_to');
                assigned.html(data);
                $('#assigned-to').select2();
                assigned.find('label').css('top', '0px');
            });
        });
    };

    var garageSelect = function () {
        var garage_select = $('#garage_name_assigned_to');
        garage_select.select2({
            minimumInputLength: 2,
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
    };

    var distributorSelect = function () {
        var distributor_select = $('#distributor_name_assigned_to');
        distributor_select.select2({
            minimumInputLength: 2,
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
    };

    var loadTab = function () {
        if ($('#garage_name_assigned_to').val() != null && $('#garage_name_assigned_to').val().length > 0) {
            $("label[for='task_type_garage']").trigger('click');
        } else if ($('#distributor_name_assigned_to').val() != null && $('#distributor_name_assigned_to').val().length > 0) {
            $("label[for='task_type_distributor']").trigger('click');
        } else {
            if ($('#SpecificUser').val() == '0') { //Nobody assign
                $('#assigned_to').hide();
                $('#contact_list_cnt').hide();
                $('#radioDebriefType0').prop('checked', true);
            } else if ($('#SpecificUser').val() == '1') { //Assign_specific_entity
                $('#radioDebriefType1').prop('checked', true);
            } else if ($('#SpecificUser').val() == '2') { //Assign_logged
                $('#assigned_to').hide();
                $('#contact_list_cnt').hide();
                $('#radioDebriefType2').prop('checked', true);
            }
        }
        $('#radioDebriefType0,#radioDebriefType2').on('change', function () {
            if ($(this).prop('checked')) {
                $('#assigned_to').hide();
                $('#contact_list_cnt').hide();
                $('#assigned-to').val('').trigger('change');
                $('#contact_list').val('').trigger('change');
            }
        });
        $('#radioDebriefType1').on('change', function () {
            if ($(this).prop('checked')) {
                $('#assigned_to').show();
                $('#contact_list_cnt').show();
            }
        });
        //check if our debrief task is user type
        if ($('#DebriefAction').val() == 'edit_debrief_task' &&
            $('#garage_name_assigned_to').val() != null && $('#garage_name_assigned_to').val().length > 0 &&
            $('#distributor_name_assigned_to').val() != null && $('#distributor_name_assigned_to').val().length > 0
        ) {
            if ($('#SpecificUser').val() == '0') { //Nobody assign
                $('#radioDebriefType0').prop('checked', true);
            } else if ($('#SpecificUser').val() == '1') { //Assign_specific_entity
                $('#radioDebriefType1').prop('checked', true);
            } else if ($('#SpecificUser').val() == '2') { //Assign_logged
                $('#radioDebriefType2').prop('checked', true);
            }
        }
    };

    var deleteTask = function () {
        $('.delete-task-js').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var element = $(this);
            swal({
                title: element.data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function (result) {
                if (result.value) {
                    window.location = element.attr('href');
                }
            });
        })
    };

    var selectGarages = function () {
        var garage_select = $('#garage_name_assigned_to');
        setTimeout(function () {
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
                minimumInputLength: 2,
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

    return {
        load: function () {
            taskTypes();
            garageSelect();
            distributorSelect();
            loadTab();
            assignedTo();
            deleteTask();
            selectGarages();
            selectDistributors();
        }
    }

})();