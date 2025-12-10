$(document).ready(function () {
    Tasks.load();
});



var Tasks = (function () {

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

    var showGenerateNewAlerts = function () {
        $('#due-date').on('change',function(){
            $('#check_alert').fadeIn();
        });
        $('#body').on('change',function(){
            $('#check_alert').fadeIn();
        });
        $('#users').on('change',function(){
            $('#check_alert').fadeIn();
        });
        $('#files').on('change',function(){
            $('#check_alert').fadeIn();
        });
        $('#contact_list').on('change',function(){
            $('#check_alert').fadeIn();
        });
        $('.swal-msg-ajax').on('click',function(){
            $('#check_alert').fadeIn();
        });
    };

    var checkTask = function(){
        $(".check-task-js").click(function(event){
            event.preventDefault();
            event.stopImmediatePropagation();
            var element = $(this);
            var urlHref = this.href;

            var task_id = element.data('id');
            var n_garages_task = $('#garages-task-' + task_id).val();

            if(typeof(n_garages_task) == "undefined" || n_garages_task < 2) {
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

                            if($('.subtask-type').length > 0 || $('#home').length > 0){
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
                            }else{
                                element.find('span').removeClass('c-informacion');
                                element.find('span').addClass('c-exito');
                                element.data('confirmmsg', $('#confirm-uncheck').data('msg'));
                                element.removeClass('check-task-js');
                                element.addClass('uncheck-task-js');
                                element.attr("href", $('.garage-data-uncheck').data('url') + '/' + task_id + '/' + garage_id);
                            }

                            $(".uncheck-task-js").unbind();
                            uncheckTask();
                            deleteTaskAppointment();
                        });
                    }
                });
            }else{
                var url = $('#garages-task-' + task_id).data('url');
                window.location.href = url;
            }
        });
    };

    var uncheckTask = function(){
        $(".uncheck-task-js").click(function(event){
            event.preventDefault();
            event.stopImmediatePropagation();
            var element = $(this);
            var urlHref = this.href;

            var task_id = element.data('id');
            var n_garages_task = $('#garages-task-' + task_id).val();
            $('.uncheck-task-js').first()

            if (typeof(n_garages_task) == "undefined" || n_garages_task < 2) {
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

                            if($('.subtask-type').length > 0 || $('#home').length > 0){
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
                            checkTask();
                            deleteTaskAppointment();
                        });
                    }

                });
            } else {
                var url = $('#garages-task-' + task_id).data('url');
                window.location.href = url;
            }
        });
    };

    var deleteTaskAppointment = function () {
        $(".delete-task-js").click(function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            var element = $(this);
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
                    var request = PeticionAjax.post(urlHref);

                    request.done(function (data) {
                        $('#results_table_ajax').html(data);
                        element.data('confirmmsg', $('#confirm-delete').data('msg'));
                        element.attr("href", $('#confirm-check').data('url') + '/' + element.data('id'));
                        $(".delete-task-js").unbind('click');
                        deleteTaskAppointment();
                        uncheckTask();
                        checkTask();
                    });
                }
            });
        });
    };

    var deleteTask = function(){
        $('#task_delete').on('click',function(e){
            e.preventDefault();
            var element = $(this);
            swal({
                title: $.i18n._('Alert.Sure?'),
                text: $.i18n._('Alert.No_revert'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#ff5648',
                cancelButtonColor: '#d33',
                confirmButtonText: $.i18n._('General.Yes')
            }).then(function (result) {
                if (result.value) {
                    window.location = element.attr('href');
                }
            });
        });
    };

    var appearReason = function(){
        if( $("#select-status-js").val() == 1 || $("#select-status-js").val() == 2 ){
            $('#reason-js').hide();
        }else{
            $('#reason-js').show();
        }
        $("#select-status-js").on('change',function(){
            if( $("#select-status-js").val() == 1 || $("#select-status-js").val() == 2 ){
                $('#reason-js').fadeOut();
            }else{
                $('#reason-js').fadeIn();
            }
        })
    };

    var loadBehaivour = function(){
        $('.ui-datepicker-trigger').remove();
        $('#users').on('change',function(){
            $('#check_down').prop('checked',true);
            $('#check_up').prop('checked',true);
        });
        $('#contact_list').on('change',function(){
            $('#check_down').prop('checked',true);
            $('#check_up').prop('checked',true);
        });
        $('#generate_up').on('click',function(){
            if($('#check_up').prop('checked') == true){
                $('#check_down').prop('checked', true);
            } else {
                $('#check_down').prop('checked', false);
            }
        });
        $('#generate_down').on('click',function(){
            if($('#check_down').prop('checked') == true){
                $('#check_up').prop('checked', true);
            } else {
                $('#check_up').prop('checked', false);
            }
        });
    };

    var check_send = function(){
        if( $('#form-controller').val() == 'tasks') {
            var check_send = $('#check_send');
            var assigned_to = $('#assigned-to').val();
            var status = $('#select-status-js').val();
            var due_date = $('#due-date').val();
            var body = $('#body').val();
            var users = $('#users').val() + '';
            var contact_list = $('#contact_list').val() + '';
            if ($('#cnt_form_task').length == 0) {
                $('.btn-guardar').each(function () {
                    if ($(this).attr('id') == 'btn-guardar') {
                        $(this).on('click', function (e) {
                            $(this).prop('disabled', true);
                            e.preventDefault();
                            if (
                                assigned_to != $('#assigned-to').val() ||
                                status != $('#select-status-js').val() ||
                                due_date != $('#due-date').val() ||
                                body != $('#body').val() ||
                                users != ($('#users').val() + '') ||
                                contact_list != ($('#contact_list').val() + '')
                            ) {
                                check_send.val(1);
                            }
                            $('#task-form').submit();
                        });
                    }
                });
            }
        }
    };


    return {
        load: function () {
            loadDeleteDocuments();
            showGenerateNewAlerts();
            checkTask();
            uncheckTask();
            deleteTaskAppointment();
            deleteTask();
            appearReason();
            loadBehaivour();
            check_send();
        }
    }
})();