$(document).ready(function () {
    Reassignment.load();
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    $('#btn-submit').prop('disabled', true);
    selected_origin = 0;
    selected_destination = 0;
});

var Reassignment = (function () {
    var init = function () {
        var btn_submit = $('#btn-submit');
        $('.ui-state-highlight').off('click').on('click', function () {
            var users = $(this).parent().children('.ui-state-highlight');
            if ($(this).parent().is("#List1")) {
                selected_origin = $(this).data('id');
                if($('#tmp-origin-selected').find('.ui-state-highlight').length > 0){
                    $('#tmp-origin-selected').find('.ui-state-highlight').appendTo('#List2');
                }
                $('#List2').find('[data-id="' + selected_origin + '"]').appendTo("#tmp-origin-selected");
                sortItems($('#List2'));

                users.each(function () {
                    $(this).removeClass('selected_item');
                });
                $('#origin_name').html($(this).html());
                if (btn_submit.prop('disabled') && selected_destination != 0) {
                    btn_submit.addClass('tres').prop('disabled', false);
                }
                $('#user-origin').val(selected_origin);
            } else if ($(this).parent().is("#List2")) {
                selected_destination = $(this).data('id');
                if($('#tmp-destination-selected').find('.ui-state-highlight').length > 0){
                    $('#tmp-destination-selected').find('.ui-state-highlight').appendTo('#List1');
                }
                $('#List1').find('[data-id="' + selected_destination + '"]').appendTo("#tmp-destination-selected");
                sortItems($('#List1'));

                users.each(function () {
                    $(this).removeClass('selected_item');
                });
                $('#destination_name').html($(this).html());
                if (btn_submit.prop('disabled') && selected_origin != 0) {
                    btn_submit.addClass('tres').prop('disabled', false);
                }
                $('#user-destination').val(selected_destination);
            }
            $(this).addClass('selected_item');
        });
    };

    var sortItems = function(list){
        var listitems = list.find('.ui-state-highlight').get();
        listitems.sort(function(a, b) {
        var compA = $(a).text().toUpperCase();
        var compB = $(b).text().toUpperCase();
        return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
        })
        $.each(listitems, function(idx, itm) { list.append(itm); });
    }

    var searchTime = function () {
        var timer;

        $("#search_origin").keyup(function () {
            clearTimeout(timer);
            timer = setTimeout(function search() {
                searchUsers($("#search_origin"));
            }, 500);
        });
        $("#search_destination").keyup(function () {
            clearTimeout(timer);
            timer = setTimeout(function search() {
                searchUsers($("#search_destination"));
            }, 500);
        });
    };

    var searchUsers = function (field) {
        var url = field.data('url');
        var data = {};
        data.name = field.val();
        data.id = field.data('id');
        data.list = field.data('list');
        var request = PeticionAjax.post(url, data);
        request.done(function (data) {
            $('#' + field.data('list')).html(data);
            if(selected_origin != 0){
                $('#List2')
            }
            $('#List1').find('.ui-state-highlight').each(function () {
                if(selected_destination == $(this).data('id')){
                    $(this).remove();
                }
                if ($(this).data('id') == selected_origin) {
                    $(this).addClass('selected_item');
                }
            });
            $('#List2').find('.ui-state-highlight').each(function () {
                if(selected_origin == $(this).data('id')){
                    $(this).remove();
                }
                if ($(this).data('id') == selected_destination) {
                    $(this).addClass('selected_item');
                }
            });
            init();
        });
    };

    var permanentReassignment = function(){
        $('#msg_disabled').hide();
        $('#permanent-check').on('click', function (e) {
            if($('#permanent-check').prop('checked') == true){
                $('#div-reassignment-from-date').hide();
                $('#start_date').val('');
                $('#div-reassignment-to-date').hide();
                $('#end_date').val('');
                $('#msg_disabled').show();
            }else{
                $('#div-reassignment-from-date').show();
                $('#div-reassignment-to-date').show();
                $('#msg_disabled').hide();
            }
        });
    };

    var reassignUser = function () {
        $('#btn-submit').on('click', function (e) {
            e.preventDefault();
            if (selected_origin == selected_destination) {
                swal($.i18n._('Alert.Error'), $.i18n._('User.Same_user'), "error");
            } else {
                if($('#permanent-check').prop('checked') == false && ($('#start_date').val() == '' || $('#end_date').val() == '' )){
                    swal($.i18n._('Alert.Error'), $.i18n._('Constants.Date_field_not_empty'), "error");
                }else if(($('#start_date').val() != '' || $('#end_date').val() != '' ) && $('#start_date').val() == $('#end_date').val() ){
                    swal($.i18n._('Alert.Error'), $.i18n._('Constants.Date_field_not_equals'), "error");
                }else {
                    var url_check = $('#btn-submit').data('url_check');
                    data = {};
                    data.user_origin_id = selected_origin;
                    data.user_destination_id = selected_destination;
                    data.start_date = $('#start_date').val();
                    data.end_date = $('#end_date').val();
                    data.permanent = $('#permanent-check').prop('checked');

                    var request = PeticionAjax.postJSON(url_check, data);
                    request.done(function (data) {
                        if (data.success_origin) {
                            $('#List1').find('.ui-state-highlight').each(
                                function () {
                                    if ($(this).hasClass('selected_item')){
                                        origin = $(this).data('name');
                                    }
                                }
                            );
                            $('#List2').find('.ui-state-highlight').each(
                                function () {
                                    if ($(this).hasClass('selected_item')){
                                        destination = $(this).data('name');
                                    }
                                }
                            );
                            swal({
                                title: $.i18n._('User.Reassign_user?'),
                                text: $.i18n._('User.Conflict_reassigment', origin, destination),
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: primary_color,
                                confirmButtonText: $.i18n._('General.Yes'),
                                cancelButtonText: $.i18n._('General.No')
                            }).then(function (result) {
                                if (result.value) {
                                    var data_reestructure = {};
                                    data_reestructure.user_origin_id = selected_origin;
                                    data_reestructure.user_destination_id = selected_destination;
                                    data_reestructure.start_date = $('#start_date').val();
                                    data_reestructure.end_date = $('#end_date').val();
                                    data_reestructure.permanent = $('#permanent-check').prop('checked');

                                    var url_restructure = $('#btn-submit').data('url_restructure');

                                    var request = PeticionAjax.post(url_restructure, data_reestructure);
                                    PeticionAjax.mostrarCargando();
                                    request.done(function (data) {
                                        $('#reassignment-form').submit();
                                    });
                                }
                            });
                        } else {
                            var text = '';
                            if( $('#permanent-check').prop('checked') == true){
                                text = 'User.Origin_user_disabled';
                            }
                            swal({
                                title: $.i18n._('User.Reassign_user?'),
                                text: $.i18n._(text),
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: primary_color,
                                confirmButtonText: $.i18n._('General.Yes'),
                                cancelButtonText: $.i18n._('General.No')
                            }).then(function (result) {
                                if (result.value) {
                                    PeticionAjax.mostrarCargando();
                                    $('#reassignment-form').submit();
                                }
                            });
                        }

                    });
                }
            }
        });
    };

    var reassignmentDataTable = function(){
        $('#table-reassignments').DataTable({
            oLanguage: {
                sUrl: "../js/lib/dataTables/locale/" + language_code + ".json"
            },
            autoWidth: false,
            deferRender: true,
            order: [[ 2, "asc" ]]
        });
    };

    return {
        load: function () {
            init();
            searchTime();
            reassignUser();
            permanentReassignment();
            reassignmentDataTable();
        }
    }
})();