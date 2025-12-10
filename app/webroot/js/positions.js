$(document).ready(function(){
    Position.load();
    selected_id = "";

    $('#tab-add').on('click', function(){
        $(".select_tr").removeClass('bg-primary-i');
        $('#form-add').show();
        $('#form-edit').hide();
        $('#form-edit-msg').hide();
    });

    $('#tab-edit').on('click', function(){
        $(".select_tr").removeClass('bg-primary-i');
        $('#form-edit-msg').show();
        $('#form-edit').hide();
        $('#form-add').hide();
    });

    $('#unselect-position').on('click', function(){
        $('#tab-add').trigger('click');
    });

    $('#img-position-add').on('click', function(){
        $('#position-file-add').trigger('click');
    });

    $('#img-position-edit').on('click', function(){
        $('#position-file-edit').trigger('click');
    });
});

var Position = (function(){

    var select = function(){

        $('.select_tr').on('click', function(e){
            e.preventDefault();
            selected_id = $(this).attr('id');
            var role_id = $(this).data('role-id');
            $('.dragdrop-delete-file-js').trigger('click');
            $(".select_tr").removeClass('bg-primary-i');
            $(this).addClass('bg-primary-i');
            var position_name_en = $(this).find('#language-en').text();
            var position_name_fr = $(this).find('#language-fr').text();
            var position_name_de = $(this).find('#language-de').text();
            position_name_en = $.trim(position_name_en);
            position_name_fr = $.trim(position_name_fr);
            position_name_de = $.trim(position_name_de);
            var url = $('#' + selected_id).attr('data-url');
            $('#name-position-edit-en').val(position_name_en);
            $('#name-position-edit-fr').val(position_name_fr);
            $('#name-position-edit-de').val(position_name_de);
            $('#name-position-edit-role').val(role_id);
            $('#img-position-edit').attr("src", '/img/iconos/' + url);
            $('#unselect-position').prop('disabled', false);
            $('#tab-add').prop("checked", false);
            $('#tab-edit').prop("checked", true);
            $('#form-edit-msg').hide();
            $('#form-add').hide();
            $('#form-edit').show();
        });

        $('#unselect-position').on('click', function(e){
            e.preventDefault();
            $('#form-edit-msg').hide();
            $('#unselect-position').prop('disabled', true);
            $(".select_tr").removeClass('bg-primary-i');
            $('#img-position-add').attr("src", '/img/upload_pic.png');
            $('#form-add').show();
            $('#form-edit').hide();
            $('#name-position-add-en').val("");
            $('#name-position-add-fr').val("");
            $('#name-position-add-de').val("");
            $('#name-position-add-role').val(0);
            $('#position-file-add').val("");
            $('.dragdrop-delete-file-js').trigger('click');
        });

    };

    var add = function(){
        $("#FormAddPosition").submit(function(e){
            e.preventDefault();
            var search_input = $("#search_id").val();
            if(search_input == ""){ search_input = 'all' }
            var request = $.ajax({
                type: "POST",
                url: $(this).attr('action') + '/' + search_input,
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false
            });

            request.done(function(data){
                const cleanData = DOMPurify.sanitize(data);
                $('#ajax_table_positions').html(cleanData);
                reset();
            });
        });
    };

    var edit = function(){
        $("#FormEditPosition").submit(function(e){
            e.preventDefault();
            var id = $('#' + selected_id).data('id');
            var search_input = $("#search_id").val();
            if(search_input == ""){ search_input = 'all' }
            var request = $.ajax({
                type: "POST",
                url: $(this).attr('action') + '/' + id + '/' + search_input,
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false
            });

            request.done(function(data){
                const cleanData = DOMPurify.sanitize(data);
                $('#ajax_table_positions').html(cleanData);
                reset();
            });
        });
    };

    var deletePosition = function(){
        $(".delete-position-js").on('click', function(e){
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
                        $('#ajax_table_positions').html(cleanData);
                        reset();
                    });
                }
            });
        });
    };

    var searchTime = function(){
        var timer;

        $("#search_id").keyup(function(){
            clearTimeout(timer);
            timer = setTimeout(function search(){
                searchPosition();
            }, 750);
        });
    };

    var searchPosition = function(){
        var search_input = $("#search_id").val();
        if(search_input == ""){ search_input = 'all' }
        var url = 'search_position/' + search_input;
        var request = PeticionAjax.post(url);
        request.done(function(data){
            $('#ajax_table_positions').html(data);
            reset();
        });
    };

    var img = function(){
        $("#position-file-add").change(function(){
            Position.previewImgAdd(this);
            $('.dragdrop-delete-file-js').on('click', function(){
                $('#img-position-add').attr("src", '/img/upload_pic.png');
            });
        });
        $("#position-file-edit").change(function(){
            Position.previewImgEdit(this);
            $('.dragdrop-delete-file-js').on('click', function(){
                $('#img-position-edit').attr("src", '/img/iconos/' + $('#' + selected_id).attr('data-url'));
            });
        });
    };

    var reset = function(){
        select();
        deletePosition();
        $('#unselect-position').trigger('click');
        $(document).foundation('alert', 'reflow');
        $('#session-msg').prependTo('#contenido');
    };

    return {
        load: function(){
            select();
            add();
            edit();
            deletePosition();
            searchTime();
            img();
        },

        previewImgAdd: function(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#img-position-add').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        },
        previewImgEdit: function(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#img-position-edit').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    }

})();