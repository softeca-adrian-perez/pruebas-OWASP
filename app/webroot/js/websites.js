$(document).ready(function(){
    Website.load();
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

    $('#unselect-website').on('click', function(){
        $('#tab-add').trigger('click');
    });

    $('#img-website-add').on('click', function(){
        $('#website-file-add').trigger('click');
    });

    $('#img-website-edit').on('click', function(){
        $('#website-file-edit').trigger('click');
    });
});

var Website = (function(){

    var select = function(){

        $('.select_tr').on('click', function(e){
            e.preventDefault();
            selected_id = $(this).attr('id');
            $('.dragdrop-delete-file-js').trigger('click');
            $(".select_tr").removeClass('bg-primary-i');
            $(this).addClass('bg-primary-i');
            var website_name = $(this).find('#language').text();
            website_name = $.trim(website_name);
            var url = $('#' + selected_id).attr('data-url');
            $('#name-website-edit').val(website_name);
            $('#img-website-edit').attr("src", '/img/iconos/' + url);
            $('#unselect-website').prop('disabled', false);
            $('#tab-add').prop("checked", false);
            $('#tab-edit').prop("checked", true);
            $('#form-edit-msg').hide();
            $('#form-add').hide();
            $('#form-edit').show();
        });

        $('#unselect-website').on('click', function(e){
            e.preventDefault();
            $('#form-edit-msg').hide();
            $('#unselect-website').prop('disabled', true);
            $(".select_tr").removeClass('bg-primary-i');
            $('#img-website-add').attr("src", '/img/upload_pic.png');
            $('#form-add').show();
            $('#form-edit').hide();
            $('#name-website-add').val("");
            $('#website-file-add').val("");
            $('.dragdrop-delete-file-js').trigger('click');
        });

    };

    var add = function(){
        $("#FormAddWebsite").submit(function(e){
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
                $('#ajax_table_websites').html(cleanData);
                reset();
            });
        });
    };

    var edit = function(){
        $("#FormEditWebsite").submit(function(e){
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
                $('#ajax_table_websites').html(cleanData);
                reset();
            });
        });
    };

    var deleteWebsite = function(){
        $(".delete-website-js").on('click', function(e){
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
                        $('#ajax_table_websites').html(cleanData);
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
                searchWebsite();
            }, 750);
        });
    };

    var searchWebsite = function(){
        var search_input = $("#search_id").val();
        if(search_input == ""){ search_input = 'all' }
        var url = 'search_website/' + search_input;
        var request = PeticionAjax.post(url);
        request.done(function(data){
            $('#ajax_table_websites').html(data);
            reset();
        });
    };

    var img = function(){
        $("#website-file-add").change(function(){
            Website.previewImgAdd(this);
            $('.dragdrop-delete-file-js').on('click', function(){
                $('#img-website-add').attr("src", '/img/upload_pic.png');
            });
        });
        $("#website-file-edit").change(function(){
            Website.previewImgEdit(this);
            $('.dragdrop-delete-file-js').on('click', function(){
                $('#img-website-edit').attr("src", '/img/iconos/' + $('#' + selected_id).attr('data-url'));
            });
        });
    };

    var reset = function(){
        select();
        deleteWebsite();
        $('#unselect-website').trigger('click');
        $(document).foundation('alert', 'reflow');
        $('#session-msg').prependTo('#contenido');
    };

    return {
        load: function(){
            select();
            add();
            edit();
            deleteWebsite();
            searchTime();
            img();
        },

        previewImgAdd: function(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#img-website-add').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        },
        previewImgEdit: function(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#img-website-edit').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    }

})();