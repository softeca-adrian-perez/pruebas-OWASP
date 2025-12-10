$(document).ready(function(){
    Association.load();
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

    $('#unselect-association').on('click', function(){
        $('#tab-add').trigger('click');
    });

    $('#img-association-add').on('click', function(){
        $('#association-file-add').trigger('click');
    });

    $('#img-association-edit').on('click', function(){
        $('#association-file-edit').trigger('click');
    });
});

var Association = (function(){

    var select = function(){

        $('.select_tr').on('click', function(e){
            e.preventDefault();
            selected_id = $(this).attr('id');
            $('.dragdrop-delete-file-js').trigger('click');
            $(".select_tr").removeClass('bg-primary-i');
            $(this).addClass('bg-primary-i');
            var association_name = $(this).find('#language').text();
            association_name = $.trim(association_name);
            var url = $('#' + selected_id).attr('data-url');
            $('#name-association-edit').val(association_name);
            $('#img-association-edit').attr("src", '/img/iconos/' + url);
            $('#unselect-association').prop('disabled', false);
            $('#tab-add').prop("checked", false);
            $('#tab-edit').prop("checked", true);
            $('#form-edit-msg').hide();
            $('#form-add').hide();
            $('#form-edit').show();
        });

        $('#unselect-association').on('click', function(e){
            e.preventDefault();
            $('#form-edit-msg').hide();
            $('#unselect-association').prop('disabled', true);
            $(".select_tr").removeClass('bg-primary-i');
            $('#img-association-add').attr("src", '/img/upload_pic.png');
            $('#form-add').show();
            $('#form-edit').hide();
            $('#name-association-add').val("");
            $('#association-file-add').val("");
            $('.dragdrop-delete-file-js').trigger('click');
        });

    };

    var add = function(){
        $("#FormAddAssociation").submit(function(e){
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
                $('#ajax_table_associations').html(cleanData);
                reset();
            });
        });
    };

    var edit = function(){
        $("#FormEditAssociation").submit(function(e){
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
                $('#ajax_table_associations').html(cleanData);
                reset();
            });
        });
    };

    var deleteAssociation = function(){
        $(".delete-association-js").on('click', function(e){
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
                        $('#ajax_table_associations').html(cleanData);
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
                searchAssociation();
            }, 750);
        });
    };

    var searchAssociation = function(){
        var search_input = $("#search_id").val();
        if(search_input == ""){ search_input = 'all' }
        var url = 'search_association/' + search_input;
        var request = PeticionAjax.post(url);
        request.done(function(data){
            $('#ajax_table_associations').html(data);
            reset();
        });
    };

    var img = function(){
        $("#association-file-add").change(function(){
            Association.previewImgAdd(this);
            $('.dragdrop-delete-file-js').on('click', function(){
                $('#img-association-add').attr("src", '/img/upload_pic.png');
            });
        });
        $("#association-file-edit").change(function(){
            Association.previewImgEdit(this);
            $('.dragdrop-delete-file-js').on('click', function(){
                $('#img-association-edit').attr("src", '/img/iconos/' + $('#' + selected_id).attr('data-url'));
            });
        });
    };

    var reset = function(){
        select();
        deleteAssociation();
        $('#unselect-association').trigger('click');
        $(document).foundation('alert', 'reflow');
        $('#session-msg').prependTo('#contenido');
    };

    return {
        load: function(){
            select();
            add();
            edit();
            deleteAssociation();
            searchTime();
            img();
        },

        previewImgAdd: function(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#img-association-add').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        },
        previewImgEdit: function(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#img-association-edit').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    }

})();