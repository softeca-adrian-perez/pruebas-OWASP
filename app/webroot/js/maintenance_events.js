$(document).ready(function () {
    MaintenanceEvents.load();
});
var MaintenanceEvents = (function () {
    
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
                        const cleanData = DOMPurify.sanitize(data);
                        $('#ajax_table_maintenance_events').html(cleanData);
                        reset();
                    });
                    request.fail(function(){
                        swal($.i18n._('EventType.Error_in_use'), '', 'error');
                    })
                }
            });
        });
    };

    var reset = function(){
        select();
        deleteEvent();
        $('#unselect-event').trigger('click');
        $(document).foundation('alert', 'reflow');
        $('#session-msg').prependTo('#contenido');
    };

    var add = function () {
        if ($('#garage_name').val() == '' && $('#distributor_name').val() == '') {
            $('#modal-follow-up-visit').hide();
        } else {
            $('#modal-follow-up-visit').show();
        }

        $("#FormAddEvent").submit(function (e) {

            e.preventDefault();
            var search_input = $("#search_id").val();
            if (search_input == "") {
                search_input = 'all'
            }
            var request = $.ajax({
                type: "POST",
                url: $(this).attr('action') + '/' + search_input,
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false
            });

            request.done(function (data) {
                const cleanData = DOMPurify.sanitize(data);
                $('#ajax_table_maintenance_events').html(cleanData);
                reset();
            });
        });
    };

    var edit = function () {

        $("#FormEditEvent").submit(function (e) {
            e.preventDefault();
            var id = $('#' + selected_id).data('id');
            var search_input = $("#search_id").val();
            if (search_input == "") {
                search_input = 'all'
            }
            var request = $.ajax({
                type: "POST",
                url: $(this).attr('action') + '/' + id + '/' + search_input,
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false
            });

            request.done(function (data) {
                const cleanData = DOMPurify.sanitize(data);
                $('#ajax_table_maintenance_events').html(cleanData);
                reset();
            });
        });

    };

    return {
        load: function () {
            add();
            edit();
            deleteEvent();
        }
    }
})();
