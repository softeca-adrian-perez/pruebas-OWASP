$(document).ready(function () {
    DistributorNetwork.load();
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
});

var DistributorNetwork = (function () {

    var select = function () {
        data = {};
        if ($('#network-type-select').val() == 1) {
            data.network_type = 'LV';
        } else if ($('#network-type-select').val() == 2) {
            data.network_type = 'CV';
        }
        data.distributor_network_id = $('#network-type-select').data('distributor_network_id');
        var request = PeticionAjax.post($('#network-type-select').data('url'), data);
        request.done(function (data) {
            $('#trading-groups-networks').html(data);
        });
        $('#network-type-select').on('change', function () {
            data = {};
            if ($(this).val() == 1) {
                data.network_type = 'LV';
            } else if ($(this).val() == 2) {
                data.network_type = 'CV';
            }

            var request = PeticionAjax.post($(this).data('url'), data);
            request.done(function (data) {
                $('#trading-groups-networks').html(data);
            });
        });
    };

    var custom_default_pin = function () {
        if ($('.presets').prop('checked') == true) {
            $('#custom-container').hide();
        }
        if ($('#Radio_Custom').prop('checked') == true) {
            $('#custom-container').show();
        }
        $('.presets').on('change', function () {
            if (this.checked) {
                $('#custom-container').slideUp();
            }
        });
        $('#Radio_Custom').on('change', function () {
            if (this.checked) {
                $('#custom-container').slideDown();
            }
        });

        // Allows to unselect pins from radio group
        var lastChecked = null;
        if ($('input[name="RadioGroup"]:checked').val()) {
            lastChecked = $('input[name="RadioGroup"]:checked').val();
        }

        $('input[name="RadioGroup"]').on('click', function () {
            var radio = $(this);
            if (lastChecked === $(this).val()) {
                radio.prop('checked', false);
                lastChecked = null;
                if ($(this).val() == $('#Radio_Custom').val()) {
                    $('#custom-container').slideUp();
                }
            } else {
                lastChecked = $(this).val();
            }
        });
    };

    var crop = function () {
        var $image_crop = $('#image-to-crop');
        var mime_type = null;
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

                    $image_crop.cropper('setCanvasData', ({ left: 0, width: $('h4#modalTitle').width() }));
                    $image_crop.cropper('setCropBoxData', ({ left: 0, top: 0, height: 100, width: 100 }));
                    $image_crop.cropper('setData', ({ x: 0, y: 0, width: $('h4#modalTitle').width() }));
                };
                mime_type = this.files[0].type;
                reader.readAsDataURL(this.files[0]);
            }
        });

        $('#crop-image').click(function () {
            var image = $('#image-to-crop').cropper('getCroppedCanvas');
            if (image != null) {
                var image_data = image.toDataURL(mime_type, 1);

                $('#new-image-input').val(image_data);
                $('#cropImageModal').foundation('close');
            } else {
                $('#new-image-input').val(false);
            }
        });
    };

    var reset_values = function () {
        var primary_color = $('#DistributorNetworkPrimaryColor').val();
        var font_color_primary = $('#DistributorNetworkFontColorPrimary').val();
        var secondary_color = $('#DistributorNetworkSecondaryColor').val();
        var font_color_secondary = $('#DistributorNetworkFontColorSecondary').val();
        var tertiary_color = $('#DistributorNetworkTertiaryColor').val();
        var font_color_tertiary = $('#DistributorNetworkFontColorTertiary').val();
        var menu_background = $('#DistributorNetworkMenuBackground').val();
        var menu_color = $('#DistributorNetworkMenuColor').val();
        var menu_color_active = $('#DistributorNetworkMenuColorActive').val();
        var background_primary_color = $('#DistributorNetworkBackgroundPrimaryColor').val();
        var background_secondary_color = $('#DistributorNetworkBackgroundSecondaryColor').val();
        var font_color = $('#DistributorNetworkFontColor').val();

        $('#btn-reset').on('click', function (e) {
            e.preventDefault();

            $('#DistributorNetworkPrimaryColor')
                .val(primary_color)
                .css({ backgroundColor: primary_color });
            $('#DistributorNetworkFontColorPrimary')
                .val(font_color_primary)
                .css({ backgroundColor: font_color_primary });
            $('#DistributorNetworkSecondaryColor')
                .val(secondary_color)
                .css({ backgroundColor: secondary_color });
            $('#DistributorNetworkFontColorSecondary')
                .val(font_color_secondary)
                .css({ backgroundColor: font_color_secondary });
            $('#DistributorNetworkTertiaryColor')
                .val(tertiary_color)
                .css({ backgroundColor: tertiary_color });
            $('#DistributorNetworkFontColorTertiary')
                .val(font_color_tertiary)
                .css({ backgroundColor: font_color_tertiary });
            $('#DistributorNetworkMenuBackground').val(menu_background)
                .css({ backgroundColor: menu_background });
            $('#DistributorNetworkMenuColor')
                .val(menu_color)
                .css({ backgroundColor: menu_color });
            $('#DistributorNetworkMenuColorActive')
                .val(menu_color_active)
                .css({ backgroundColor: menu_color_active });
            $('#DistributorNetworkBackgroundPrimaryColor')
                .val(background_primary_color)
                .css({ backgroundColor: background_primary_color });
            $('#DistributorNetworkBackgroundSecondaryColor')
                .val(background_secondary_color)
                .css({ backgroundColor: background_secondary_color });

            $('div.live .MenuColor').css({ color: menu_color });
            $('div.live .MenuColorActive').css({ color: menu_color_active });
            $('div.live .MenuBackground').css({ backgroundColor: menu_background });
            $('div.live .BackgroundPrimaryColor').css({ backgroundColor: background_primary_color });
            $('div.live .BackgroundSecondaryColor').css({ backgroundColor: background_secondary_color });
            $('div.live .TertiaryColor').css({ backgroundColor: tertiary_color });
            $('div.live .DistributorNetworkPrimaryColorFont').css({ color: primary_color });
            $('div.live .DistributorNetworkPrimaryColorBack').css({ backgroundColor: primary_color });
            $('div.live .DistributorNetworkSecondaryColorFont').css({ color: secondary_color });
            $('div.live .DistributorNetworkFontColorPrimary').css({ color: font_color_primary });
            $('div.live .DistributorNetworkFontColorSecondary').css({ color: font_color_secondary });
            $('div.live .DistributorNetworkFontColorTertiary').css({ color: font_color_tertiary });
        })
    };

    var checkTradingGroups = function () {
		$("#btn-guardar").on("click", function () {
			event.preventDefault();
            // check if the trading group check is selected or if the network type has value
            if ($('#trading-groups-networks').find('input[type="checkbox"]:checked').length > 0 || $('#network-type-select').val() == "") {
				$("#distributor-network-form-id").submit();
			} else {
				swal({
					title: $.i18n._("Constants.Error_trading_group_required"),
					type: "error",
				});
			}
		});
	};

    return {
        load: function () {
            select();
            custom_default_pin();
            reset_values();
            crop();
            checkTradingGroups();
        }
    }

})();