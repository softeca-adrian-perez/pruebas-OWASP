$(document).ready(function () {
    Mailbox.load();
});

var Mailbox = (function () {

    var hideShowReadDates = function () {
        $('#sent').on('change', function (e) {
            if($(this).val() === "" || $(this).val() == 0){
                $('#sent-from').prop('disabled', true);
                $('#sent-to').prop('disabled', true);
            }else{
                $('#sent-from').prop('disabled', false);
                $('#sent-to').prop('disabled', false);
            }
        });
        $('#sent').trigger('change');
    };

    var selectGarages = function(){
        var garage_select = $('#garage_name_assigned_to');
        setTimeout(function(){
            garage_select.select2({
                minimumInputLength: 2,
                language: {
                    inputTooShort: function(args) {
                        return $.i18n._('General.Enter_at_least_two_characters');
                    },
                    noResults:function(args) {
                        return $.i18n._('General.No_results_found');
                    },
                    searching:function(args) {
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

    var selectDistributors = function(){
        var distributor_select = $('#distributor_name_assigned_to');
        setTimeout(function(){
            distributor_select.select2({
                minimumInputLength: 2,
                language: {
                    inputTooShort: function(args) {
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
            hideShowReadDates();
            selectGarages();
            selectDistributors();
        },
    }
})();