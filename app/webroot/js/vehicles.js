$(document).ready(function(){
    Vehicle.load();
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

    $('#unselect-vehicle').on('click', function(){
        $('#tab-add').trigger('click');
    });

    $('#img-vehicle-add').on('click', function(){
        $('#vehicle-file-add').trigger('click');
    });

    $('#img-vehicle-edit').on('click', function(){
        $('#vehicle-file-edit').trigger('click');
    });
});

var Vehicle = (function(){

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
					if (data && data.vehicle) {
						$.each(data.vehicle, function(index, vehicle){
							$('#name-vehicle-edit-' + index).val(vehicle);
						});
					}
					PeticionAjax.ocultarCargando();
				},
				error: function() {
					swal($.i18n._('Constants.Error_alert_general'), '', 'error');
				}
			});
            $('#img-vehicle-edit').attr("src", '/img/iconos/' + $('#' + selected_id).attr('data-url'));
            $('#unselect-vehicle').prop('disabled', false);
            $('#tab-add').prop("checked", false);
            $('#tab-edit').prop("checked", true);
            $('#form-edit-msg').hide();
            $('#form-add').hide();
            $('#form-edit').show();
        });

        $('#unselect-vehicle').on('click', function(e){
            e.preventDefault();
            $('#form-edit-msg').hide();
            $('#unselect-vehicle').prop('disabled', true);
            $(".select_tr").removeClass('bg-primary-i');
            $('#img-vehicle-add').attr("src", '/img/upload_pic.png');
            $('#form-add').show();
            $('#form-edit').hide();
			$('#form-add').find('input:text').each(function () {
                $(this).val('');
            });
            $('.dragdrop-delete-file-js').trigger('click');
        });

    };

    var add = function(){
        $("#FormAddVehicle").submit(function(e){
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
                $('#ajax_table_vehicles').html(cleanData);
                reset();
            });
        });
    };

    var edit = function(){
        $("#FormEditVehicle").submit(function(e){
            e.preventDefault();
            var id = $('#' + selected_id).data('id');
            var request = $.ajax({
                type: "POST",
                url: $(this).attr('action') + '/' + id,
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false
            });

            request.done(function(data){
                const cleanData = DOMPurify.sanitize(data);
                $('#ajax_table_vehicles').html(cleanData);
                reset();
            });
        });
    };

    var deleteVehicle = function(){
        $(".delete-vehicle-js").on('click', function(e){
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
                        $('#ajax_table_vehicles').html(cleanData);
                        reset();
                    });
                }
            });
        });
    };

    var img = function(){
        $("#vehicle-file-add").change(function(){
            Vehicle.previewImgAdd(this);
            $('.dragdrop-delete-file-js').on('click', function(){
                $('#img-vehicle-add').attr("src", '/img/upload_pic.png');
            });
        });
        $("#vehicle-file-edit").change(function(){
            Vehicle.previewImgEdit(this);
            $('.dragdrop-delete-file-js').on('click', function(){
                $('#img-vehicle-edit').attr("src", '/img/iconos/' + $('#' + selected_id).attr('data-url'));
            });
        });
    };

    var reset = function(){
        select();
        deleteVehicle();
        $('#unselect-vehicle').trigger('click');
        $('#session-msg').prependTo('#contenido');
    };

    return {
        load: function(){
            select();
            add();
            edit();
            deleteVehicle();
            img();
        },

        previewImgAdd: function(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#img-vehicle-add').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        },
        previewImgEdit: function(input){
            if(input.files && input.files[0]){
                var reader = new FileReader();
                reader.onload = function(e){
                    $('#img-vehicle-edit').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    }

})();
