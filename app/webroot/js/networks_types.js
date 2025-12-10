$(document).ready(function () {
    Network.load();
    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
});

var Network = (function () {

    var select = function () {
        data = {};
        if ($('#network-type-select').val() == 1) {
            data.network_type = 'LV';
        } else if ($('#network-type-select').val() == 2) {
            data.network_type = 'CV';
        }
        data.network_id = $('#network-type-select').data('network_id');
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
        var primary_color = '#' + $('#PrimaryColor').data('default');
        var font_color_primary = '#' + $('#PrimaryFontColor').data('default');
        var secondary_color = '#' + $('#SecondaryColor').data('default');
        var font_color_secondary = '#' + $('#SecondaryFontColor').data('default');
        var tertiary_color = '#' + $('#TertiaryColor').data('default');
        var font_color_tertiary = '#' + $('#TertiaryFontColor').data('default');
        var quaternary_color = '#' + $('#QuaternaryColor').data('default');
        var font_color_quaternary = '#' + $('#QuaternaryFontColor').data('default');
        var menu_background = '#' + $('#MenuBackground').data('default');
        var font_color = '#' + $('#FontColor').data('default');
        var menu_color = '#' + $('#MenuColor').data('default');
        var menu_color_active = '#' + $('#ColorActive').data('default');
        var background_primary_color = '#' + $('#PrimaryBackgroundColor').data('default');
        var background_secondary_color = '#' + $('#SecondaryBackgroundColor').data('default');

        var color_exito = '#' + $('#ColorExito').data('default');
        var color_fallo = '#' + $('#ColorFallo').data('default');
        var color_information = '#' + $('#ColorInformacion').data('default');
        var color_disabled = '#' + $('#ColorDisabled').data('default');

        $('#btn-reset').on('click', function (e) {
            e.preventDefault();
            $('#PrimaryColor').val(primary_color).css({ backgroundColor: primary_color });
            $('#PrimaryFontColor').val(font_color_primary).css({ backgroundColor: font_color_primary });
            $('#SecondaryColor').val(secondary_color).css({ backgroundColor: secondary_color, color: font_color_secondary });
            $('#SecondaryFontColor').val(font_color_secondary).css({ backgroundColor: font_color_secondary });
            $('#TertiaryColor').val(tertiary_color).css({ backgroundColor: tertiary_color });
            $('#TertiaryFontColor').val(font_color_tertiary).css({ backgroundColor: font_color_tertiary });
            $('#QuaternaryColor').val(quaternary_color).css({ backgroundColor: quaternary_color });
            $('#QuaternaryFontColor').val(font_color_quaternary).css({ backgroundColor: font_color_quaternary });
            $('#MenuBackground').val(menu_background).css({ backgroundColor: menu_background });
            $('#FontColor').val(font_color).css({ color: font_color });
            $('#MenuColor').val(menu_color).css({ backgroundColor: menu_color });
            $('#ColorActive').val(menu_color_active).css({ backgroundColor: menu_color_active });
            $('#PrimaryBackgroundColor').val(background_primary_color).css({ backgroundColor: background_primary_color });
            $('#SecondaryBackgroundColor').val(background_secondary_color).css({ backgroundColor: background_secondary_color });
            $('#ColorExito').val(color_exito).css({ backgroundColor: color_exito });
            $('#ColorFallo').val(color_fallo).css({ backgroundColor: color_fallo });
            $('#ColorInformacion').val(color_information).css({ backgroundColor: color_information });
            $('#ColorDisabled').val(color_disabled).css({ backgroundColor: color_disabled });


            $('div.live .MenuColor').css({ color: menu_color });
            $('div.live .ColorActive').css({ color: menu_color_active });
            $('div.live .MenuBackground').css({ backgroundColor: menu_background });
            $('div.live .FontColor').css({ color: font_color });
            $('div.live .BackgroundPrimaryColor').css({ backgroundColor: background_primary_color });
            $('div.live .bcfp').css({ borderColor: background_primary_color });
            $('div.live .BackgroundSecondaryColor').css({ backgroundColor: background_secondary_color });
            $('div.live .PrimaryColor').css({ backgroundColor: 'red', borderColor: primary_color, color: font_color_primary });
            $('div.live .PrimaryColor').css({ backgroundColor: primary_color, borderColor: primary_color, color: font_color_primary });
            $('div.live .PrimaryColorForLetter').css({ color: primary_color });
            $('div.live .PrimaryFontColor').css({ color: font_color_primary });
            $('div.live .SecondaryColor').css({ backgroundColor: secondary_color, border_color: secondary_color, color: font_color_secondary });
            $('div.live .SecondaryColorForLetter').css({ color: secondary_color });
            $('div.live .SecondaryFontColor').css({ color: font_color_secondary });
            $('div.live .TertiaryColor').css({ backgroundColor: tertiary_color });
            $('div.live .TertiaryFontColor').css({ color: font_color_tertiary });
            $('div.live .TertiaryColorForLetter').css({ color: tertiary_color });
            $('div.live .QuaternaryColor').css({ backgroundColor: quaternary_color });
            $('div.live .QuaternaryFontColor').css({ color: font_color_quaternary });
            $('div.live .QuaternaryInputColor').css({ backgroundColor: font_color_quaternary, borderColor: quaternary_color });

            $('div.live .ColorExito').css({ backgroundColor: color_exito, borderColor: color_exito });
            $('div.live .ColorFallo').css({ backgroundColor: color_fallo, borderColor: color_fallo });
            $('div.live .ColorInformacion').css({ backgroundColor: color_information, borderColor: color_information });



            // $('div.live .MenuColor').css({color: menu_color});
            // $('div.live .ColorActive').css({color: menu_color_active});
            // $('div.live .MenuBackground').css({backgroundColor: menu_background});
            // $('div.live .FontColor').css({color: font_color});
            // $('div.live .BackgroundPrimaryColor').css({backgroundColor: background_primary_color});
            // $('div.live .BackgroundSecondaryColor').css({backgroundColor: background_secondary_color});
            // $('div.live .PrimaryColorForLetter').css({color: primary_color});
            // $('div.live .PrimaryFontColor').css({color: font_color_primary});
            // $('div.live .SecondaryColor').css({backgroundColor: secondary_color,color: font_color_secondary});
            // $('div.live .SecondaryColorForLetter').css({color: secondary_color});
            // $('div.live .SecondaryFontColor').css({color: font_color_secondary});
            // $('div.live .TertiaryColor').css({backgroundColor: tertiary_color});
            // $('div.live .TertiaryFontColor').css({color: font_color_tertiary});
            // $('div.live .QuaternaryColor').css({backgroundColor: quaternary_color});
            // $('div.live .QuaternaryFontColor').css({color: font_color_quaternary});
            // $('div.live .QuaternaryInputColor').css({backgroundColor: font_color_quaternary, borderColor: quaternary_color});
            // $('div.live .ColorExito').css({color: color_exito});
            // $('div.live .ColorFallo').css({color: color_fallo});
            // $('div.live .ColorInformacion').css({color: color_information});
            // $('div.live .ColorDisabled').css({color: color_disabled});
            setTimeout(function () {
                setContrast();
            }, 200);
        })
    };

    var load = function () {
        $('div.live .MenuColor').css({ color: '#' + $('#MenuColor').val() });
        $('div.live .ColorActive').css({ color: '#' + $('#ColorActive').val() });
        $('div.live .MenuBackground').css({ backgroundColor: '#' + $('#MenuBackground').val() });
        $('div.live .FontColor').css({ color: '#' + $('#FontColor').val() });
        $('div.live .BackgroundPrimaryColor').css({ backgroundColor: '#' + $('#PrimaryBackgroundColor').val() });
        $('div.live .bcfp').css({ borderColor: '#' + $('#PrimaryBackgroundColor').val() });
        $('div.live .BackgroundSecondaryColor').css({ backgroundColor: '#' + $('#SecondaryBackgroundColor').val() });
        $('div.live .PrimaryColor').css({ backgroundColor: '#' + $('#PrimaryColor').val(), borderColor: '#' + $('#PrimaryColor').val(), color: '#' + $('#PrimaryFontColor').val() });
        $('div.live .PrimaryColorForLetter').css({ color: '#' + $('#PrimaryColor').val() });
        $('div.live .PrimaryFontColor').css({ color: '#' + $('#PrimaryFontColor').val() });
        $('div.live .SecondaryColor').css({ backgroundColor: '#' + $('#SecondaryColor').val(), borderColor: '#' + $('#SecondaryColor').val(), color: '#' + $('#SecondaryFontColor').val() });
        $('div.live .SecondaryColorForLetter').css({ color: '#' + $('#SecondaryColor').val() });
        $('div.live .SecondaryFontColor').css({ color: '#' + $('#SecondaryFontColor').val() });
        $('div.live .TertiaryColor').css({ backgroundColor: '#' + $('#TertiaryColor').val() });
        $('div.live .TertiaryFontColor').css({ color: '#' + $('#TertiaryFontColor').val() });
        $('div.live .TertiaryColorForLetter').css({ color: '#' + $('#TertiaryColor').val() });
        $('div.live .QuaternaryColor').css({ backgroundColor: '#' + $('#QuaternaryColor').val() });
        $('div.live .QuaternaryFontColor').css({ color: '#' + $('#QuaternaryFontColor').val() });
        $('div.live .QuaternaryInputColor').css({ backgroundColor: '#' + $('#QuaternaryFontColor').val(), borderColor: '#' + $('#QuaternaryColor').val() });
        $('div.live .ColorExito').css({ backgroundColor: '#' + $('#ColorExito').val(), borderColor: '#' + $('#ColorExito').val() });
        $('div.live .ColorFallo').css({ backgroundColor: '#' + $('#ColorFallo').val(), borderColor: '#' + $('#ColorFallo').val() });
        $('div.live .ColorInformacion').css({ backgroundColor: '#' + $('#ColorInformacion').val(), borderColor: '#' + $('#ColorInformacion').val() });

        $('input#MenuColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .MenuColor').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#ColorActive').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .ColorActive').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#MenuBackground').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .MenuBackground').css({ backgroundColor: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#FontColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .FontColor').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#PrimaryBackgroundColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .BackgroundPrimaryColor').css({ backgroundColor: '#' + $this.val() });
                $('div.live .PrimaryColor').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#SecondaryBackgroundColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .BackgroundSecondaryColor').css({ backgroundColor: '#' + $this.val() });
                $('div.live .SecondaryColor').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#NetworkFontColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .NetworkFontColor').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#PrimaryColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .PrimaryColor').css({ backgroundColor: '#' + $this.val() });
                $('div.live .PrimaryColor').css({ borderColor: '#' + $this.val() });
                $('div.live .PrimaryColorForLetter').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#PrimaryFontColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .PrimaryFontColor').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#SecondaryColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .SecondaryColor').css({ backgroundColor: '#' + $this.val() });
                $('div.live .SecondaryColor').css({ borderColor: '#' + $this.val() });
                $('div.live .SecondaryColorForLetter').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#SecondaryFontColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .SecondaryFontColor').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#TertiaryColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .TertiaryColor').css({ backgroundColor: '#' + $this.val() });
                $('div.live .TertiaryColorForLetter').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#TertiaryFontColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .TertiaryFontColor').css({ color: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#QuaternaryColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .QuaternaryColor').css({ backgroundColor: '#' + $this.val() });
                $('div.live .QuaternaryInputColor').css({ borderColor: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#QuaternaryFontColor').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .QuaternaryFontColor').css({ color: '#' + $this.val() });
                $('div.live .QuaternaryInputColor').css({ backgroundColor: '#' + $this.val() });
                setContrast();
            }, 100);
        });

        $('input#ColorExito').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .ColorExito').css({ backgroundColor: '#' + $this.val() });
                $('div.live .ColorExito').css({ borderColor: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#ColorFallo').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .ColorFallo').css({ backgroundColor: '#' + $this.val() });
                $('div.live .ColorFallo').css({ borderColor: '#' + $this.val() });
                setContrast();
            }, 100);
        });
        $('input#ColorInformacion').change(function () {
            var $this = $(this);
            setTimeout(function () {
                $('div.live .ColorInformacion').css({ backgroundColor: '#' + $this.val() });
                $('div.live .ColorInformacion').css({ borderColor: '#' + $this.val() });
                setContrast();
            }, 100);
        });

        $('.cnt-color-choose input').on('blur', function () {
            setContrast();
        });

    }

    var setContrast = function () {
        setTimeout(function () {
            $('.cnt-color-choose input').each(function () {
                var rgb = $(this).css('backgroundColor');
                var colors = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
                var brightness = 1;

                var r = colors[1];
                var g = colors[2];
                var b = colors[3];

                var ir = Math.floor((255 - r) * brightness);
                var ig = Math.floor((255 - g) * brightness);
                var ib = Math.floor((255 - b) * brightness);
                $(this).attr('style', 'background-color: ' + $(this).css("background-color") + '; color: rgb(' + ir + ',' + ig + ',' + ib + ') !important;');
            });
        }, 300);
    }

    var sendData = function () {
        $("#btn-guardar").on('click', function (e) {
            if ($('#aag-region-select').length > 0) {
                e.preventDefault();
                $('#aag-region-select').attr('disabled', false);
                $('#form_network').submit();
            }
        });
    }

    var toggleTraining = function () {
        $("#training-boolean").on("click", function () {
            if ($("#training-boolean").is(':checked')) {
                $(".training-div").show();
                $('#credit').prop('false', false);
                $('#date_restarting_credit').prop('false', false);
            } else {
                $(".training-div").hide();
                $('#credit').prop('true', false);
                $('#date_restarting_credit').prop('true', false);
            }
        });
    };

    return {
        load: function () {
            select();
            custom_default_pin();
            reset_values();
            crop();
            setContrast();
            load();
            sendData();
            toggleTraining();
        }
    }

})();