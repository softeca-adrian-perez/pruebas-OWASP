$(document).ready(function () {
	RecomendedNetworks.load();
});

var RecomendedNetworks = (function () {
    var recommendedEdit = function () {
        $('.edit_button-js').each(function () {
            var networkId = $(this).data('network_id-id');
            var isChecked = $('#recommended-' + networkId).is(':checked');
            $(this).toggle(isChecked);
        });
        $('[id^="recommended-"]').change(function () {
            var networkId = $(this).attr('id').replace('recommended-', '');
            var $editButton = $('.edit_button-js[data-network_id="' + networkId + '"]');
            $editButton.toggle(this.checked);
            if (!this.checked) {
                $editButton.hide();
            }
        });
        $('[id^="recommended-"]:checked').each(function () {
            var networkId = $(this).attr('id').replace('recommended-', '');
            var $editButton = $('.edit_button-js[data-network_id="' + networkId + '"]');
            $editButton.show();
        });
    }

    function openModal() {
        $('.edit_button-js').on('click', function () {
            var modalUrl = $(this).data('url');
            $.ajax({
                url: modalUrl,
                method: 'GET',
                dataType: 'html',
                success: function (response) {
                    $('#modalRecommended-js').html(response);
                    $('#edit-modal-js').show();
                    $('.close-js').on('click', function () {
                        $('#edit-modal-js').hide();
                    });
                    var $image_crop = $('#image-to-crop');
                    var $image_list_crop = $('#image-list-to-crop');
                    var mime_type = null;
                    var mime_type_list = null;
                    $('#service-file-add').change(function () {
                        if (this.files.length > 0) {
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                $image_crop.cropper('destroy');
                                $image_crop.cropper({
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
                    $('#service-file-list-add').change(function () {
                        if (this.files.length > 0) {
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                $image_list_crop.cropper('destroy');
                                $image_list_crop.cropper({
                                    dragCrop: false,
                                    cropBoxMovable: true,
                                    minContainerHeight: 600,
                                    responsive: true,
                                    restore: true
                                });
                                $image_list_crop.cropper('replace', e.target.result);
                                $('#cropImageListModal').foundation('open');

                                $image_list_crop.cropper('setCanvasData', ({left: 0, width: $('h4#modalTitle').width()}));
                                $image_list_crop.cropper('setCropBoxData', ({left: 0, top: 0, height: 100, width: 100}));
                                $image_list_crop.cropper('setData', ({x: 0, y: 0, width: $('h4#modalTitle').width()}));
                            };
                            mime_type_list = this.files[0].type;
                            reader.readAsDataURL(this.files[0]);
                        }
                    });
                    $('#crop-image').click(function () {
                        var image = $('#image-to-crop').cropper('getCroppedCanvas');
                        if(image != null){
                            var image_data = image.toDataURL(mime_type, 1);
                            $('#new-image-input').val(image_data);
                            $('#cropImageModal').foundation('close');
                        } else {
                            $('#new-image-input').val(false);
                        }
                    });
                    $('#crop-image-list').click(function () {
                        var imageList = $('#image-list-to-crop').cropper('getCroppedCanvas');
                        if(imageList != null){
                            var image_list_data = imageList.toDataURL(mime_type_list, 1);
                            $('#new-image-list-input').val(image_list_data);
                            $('#cropImageListModal').foundation('close');
                        } else {
                            $('#new-image-list-input').val(false);
                        }
                    });
                    deleteImageRecommended();
                    FormHelper.load();
                },
                error: function (error) {
                    console.error('Error to edit:', error);
                }
            });
        });
    }

    var deleteImageRecommended = function () {
        $(".delete-image-js").on('click', function () {
            let url = $(this).data('delete-url');
            let recommendedId = $(this).data('recommended-id');
            swal({
                title: $.i18n._('User.Delete_image'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(url, { recommendedId: recommendedId });
                    request.done(function (result) {
						if(result) {
							swal({
								title: $.i18n._("Constants.Message_well_deleted"),
								type: "success",
							}).then(function (result) {
								location.reload();
							});
						} else {
                            swal({
                                title: $.i18n._('Constants.Message_bad_deleted'),
                                type: "error"
                            });
                        }
                    });
                    request.fail(function () {
                        swal({
                            title: $.i18n._('Constants.Message_bad_deleted'),
                            type: "error"
                        });
                    });
                };
            });
        });
    };

    let conditions_toggles = function () {
		$('.all-options-js').on('click', function (e) {
			let checkbox = $(this);
			swal({
				title: $('#config_recommended_networks-js').data('confirmmsg'),
				type: $('#config_recommended_networks-js').data('type'),
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $('#config_recommended_networks-js').data('yes'),
				cancelButtonText: $('#config_recommended_networks-js').data('no')
			}).then(function(result){
				if(result.value) {
					let url = new URL(window.location.href);
					let tpye = 'recommended';
					url.searchParams.append(tpye, !checkbox.hasClass('ion-toggle-filled'));
					window.location.href = url;
				}
			})
		});
        $('.ico-toggle-garage-network-recommend-js').on('click', function (e) {
			PeticionAjax.mostrarCargando();
            let element = $(this);
			let url = $('#config_recommended_networks-js').data('url');
			let data = {};
			data['garage_network_id'] = element.data('garage_id');
			data['value'] = element.hasClass('ion-toggle');

			let request = PeticionAjax.post(url, data);
			request.done(function (result) {
				if (result !== 'false') {
                    change_toggle(element);
					PeticionAjax.ocultarCargando();
					swal($.i18n._('Constants.Message_well_saved'), '', 'success');
				} else {
					swal($.i18n._('Constants.Error_alert_general'), '', 'error');
				}
				setTimeout(function () {}, 500);
			});
        });
    };

    let change_toggle = function (toggle, activate = null) {
        if (toggle.hasClass('ion-toggle-filled') || (activate !== null && !activate)) {
            toggle.removeClass('ion-toggle-filled c-exito').addClass('ion-toggle c-fallo');
        } else if (toggle.hasClass('ion-toggle') || (activate !== null && activate)) {
            toggle.removeClass('ion-toggle c-fallo').addClass('ion-toggle-filled c-exito');
        }
    }

    return {
		load: function () {
			recommendedEdit();
            openModal();
            conditions_toggles();
		},
	};
})();