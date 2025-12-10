$(document).ready(function(){
    Service.load();
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

    $('#img-service-add').on('click', function(){
        $('#service-file-add').trigger('click');
    });

    $('#img-service-edit').on('click', function(){
        $('#service-file-edit').trigger('click');
    });
});

var Service = (function(){

    var select = function(){

        $('.select_tr').on('click', function(e){
            e.preventDefault();
            selected_id = $(this).attr('id');
            $('.dragdrop-delete-file-js').trigger('click');
            $(".select_tr").removeClass('bg-primary-i');
            $(this).addClass('bg-primary-i');
			PeticionAjax.mostrarCargando();
			$.ajax({
				url: $(this).data('url'),
				type: 'GET',
				data: {
					'id': $(this).data('id')
				},
				dataType: 'json',
				success: function(data) {
					if (data && data.service) {
						$.each(data.service, function(index, service){
							$('#name-service-edit-' + index).val(service);
						});
					}
					PeticionAjax.ocultarCargando();
				},
				error: function() {
					swal($.i18n._('Constants.Error_alert_general'), '', 'error');
				}
			});
            $('#img-service-edit').attr("src", $('#' + selected_id).find('img').attr('src'));
            $('#unselect-service').prop('disabled', false);
            $('#tab-add').prop("checked", false);
            $('#tab-edit').prop("checked", true);
            $('#form-edit-msg').hide();
            $('#form-add').hide();
            $('#form-edit').show();
        });

        $('#unselect-service').on('click', function(e){
            e.preventDefault();
            if ($(this).data('super_admin')) {
                $('#tab-add').prop("checked", true);
                $('#form-edit-msg').hide();
                $('#form-add').show();
                $('#form-edit').hide();
            } else {
                $('#tab-edit').prop("checked", true);
                $('#form-edit-msg').show();
                $('#form-add').hide();
                $('#form-edit').hide();
                $('#form-add').find('input:text').each(function () {
                    $(this).val('');
                });
            }
            $('#unselect-service').prop('disabled', true);
            $('#img-service-add').attr("src", '/img/upload_pic.png');
            $(".select_tr").removeClass('bg-primary-i');
            $('.dragdrop-delete-file-js').trigger('click');
        });

    };

    var add = function(){
        $("#FormAddService").submit(function(e){
            e.preventDefault();
            var request = $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false
            });

            request.done(function(data){
                const cleanData = DOMPurify.sanitize(data);
                $('#ajax_table_services').html(cleanData);
                reset();
            });
        });
    };

    var edit = function(){
        $("#FormEditService").submit(function(e){
            e.preventDefault();
            var request = $.ajax({
                type: "POST",
                url: $(this).attr('action') + '/' + $('#' + selected_id).data('id'),
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false
            });

            request.done(function(data){
                const cleanData = DOMPurify.sanitize(data);
                $('#ajax_table_services').html(cleanData);
                reset();
            });
        });
    };

    var deleteService = function(){
        $(".delete-service-js").on('click', function (e) {
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
                    var request = PeticionAjax.post(url);
                    request.done(function (data) {
                        const cleanData = DOMPurify.sanitize(data);
                        $('#ajax_table_services').html(cleanData);
                        reset();
                    });
                }
            });
        });
    };

    var img = function(){
        $("#service-file-add").change(function(){
            Service.previewImgAdd(this);
            $('.dragdrop-delete-file-js').on('click', function(){
                $('#img-service-add').attr("src", '/img/upload_pic.png');
            });
        });
        $("#service-file-edit").change(function(){
            Service.previewImgEdit(this);
            $('.dragdrop-delete-file-js').on('click', function(){
                $('#img-service-edit').attr("src", '/img/iconos/' + $('#' + selected_id).attr('data-url'));
            });
        });
    };

    var reset = function(){
        select();
        deleteService();
        $('#unselect-service').trigger('click');
        $(document).foundation('alert', 'reflow');
        $('#session-msg').prependTo('#contenido');
    };

    var crop = function () {
        var $image_crop = $('#image-to-crop');

        var mime_type = null;
        $('.crop-file-js').change(function () {
            element_id = $(this).data('element_id');
            cropper_options = $(this).data('cropper_options');
            var width = $(this).data('width');
            var height = $(this).data('height');
            var size = width / height;
            if (this.files.length > 0) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $image_crop.cropper('destroy');
                    $image_crop.cropper({
                        aspectRatio: size,
                        dragCrop: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        minContainerHeight: 600,
                        responsive: true,
                        restore: true
                    });
                    $image_crop.cropper('replace', e.target.result);
                    $('#cropImageModal').foundation('open');

                    $image_crop.cropper('setCanvasData', ({left: 0, width: $('h4#modalTitle').width()}));
                    $image_crop.cropper('setCropBoxData', ({left: 0, top: 0, height: 100, width: 100}));
                    $image_crop.cropper('setData', ({x: 0, y: 0, width: $('h4#modalTitle').width()}));
                };
                mime_type = this.files[0].type;
                reader.readAsDataURL(this.files[0]);
            }
        });

        $('#crop-image').click(function () {
            var image = $('#image-to-crop').cropper('getCroppedCanvas', cropper_options);
            if(image != null){
                var image_data = image.toDataURL(mime_type, 1);
                $('#' + element_id).val(image_data);
                $('#cropImageModal').foundation('close');
            } else {
                $('#' + element_id).val(false);
            }
        });
    };

    return {
        load: function(){
            select();
            add();
            edit();
            deleteService();
            img();
            crop();
        },

        previewImgAdd: function(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#img-service-add').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        },
        previewImgEdit: function(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#img-service-edit').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    }

})();
