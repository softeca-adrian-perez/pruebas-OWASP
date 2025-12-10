$(document).ready(function () {
    Trading.load();
});

var Trading = (function () {
    var addNetwork = function () {
        $('#btn_add_network').click(function (e) {
            e.preventDefault();
            if ($('#selected_network').val() != "") {
                var finalUrl = $(this).data('url') + "/" + $(this).data('trading-group') + "/" + $('#selected_network').val();

                var request = $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: finalUrl
                    })
                    .done(function (data) {
                        var type_network = "";
                        if (data.Network.network_type == 1) {
                            type_network = "LV";
                        } else {
                            type_network = "CV";
                        }
                        $('#table_networks').append(
                            "<tr id='"+ data.Network.id +"'>" +
                                "<td class='ta-center'><img src='"+ data.Network.image+ "'class='img_table'></td>" +
                                "<td>" + data.Network.name + "</td>" +
                                "<td>" + data.Network.web + "</td>" +
                                "<td class='ta-center'>" + type_network + "</td>" +
                                "<td class='ta-center'>" +
                                    "<a href='javascript:;'" +
                                            "class='delete-network-js'" +
                                            "data-confirmmsg='" + $.i18n._('General.Delete_file?') + "'" +
                                            "data-url='/trading_groups/ajax_delete_network/1/" + data.Network.id + "'" +
                                            "data-id='" + data.Network.id + "'" +
                                            "data-name='" + data.Network.name + "'" +
                                            "title='" + $.i18n._('General.Delete') +  "'>" +
                                        "<span class='ion-trash-b c-fallo'></span>" +
                                    "</a>" +
                                "</td>" +
                            "</tr>"
                        );
                        $('#selected_network option[value="'+ data.Network.id +'"]').remove();
                        $('#select2-selected_network-container').html('');

                        Alertas.show($('#alert_trading_group'),'exito', $.i18n._('Network.Network_added'));

                        $(".delete-network-js").unbind();

                        deleteNetwork();
                    });
            }
        })
    };

    var deleteNetwork = function(){
        $(".delete-network-js").click(function(e){
            e.preventDefault();
            data = {};
            var url = $(this).data('url');
            var msg = $(this).data('confirmmsg');
            var network_id = $(this).data('id');
            var network_name = $(this).data('name');
            swal({
                title: msg,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(url, data);
                    request.done(function () {
                        $('#' + network_id).remove();
                        $('#selected_network').append($('<option>', {
                            value: network_id,
                            text: network_name
                        }));
                    });
                }

            });

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

                    $image_crop.cropper('setCanvasData', ({left: 0, width: $('h4#modalTitle').width()}));
                    $image_crop.cropper('setCropBoxData', ({left: 0, top: 0, height: 100, width: 100}));
                    $image_crop.cropper('setData', ({x: 0, y: 0, width: $('h4#modalTitle').width()}));
                };
                mime_type = this.files[0].type;
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
    };

    var reset_values = function(){
        var primary_color = $('#PrimaryColor').val();
        var font_color_primary = $('#PrimaryFontColor').val();
        var secondary_color = $('#SecondaryColor').val();
        var font_color_secondary = $('#SecondaryFontColor').val();
        var tertiary_color = $('#TertiaryColor').val();
        var font_color_tertiary = '#' + $('#TertiaryFontColor').data('default');
        var quaternary_color = '#' + $('#QuaternaryColor').data('default');
        var font_color_quaternary = '#' + $('#QuaternaryFontColor').data('default');
        var menu_background = $('#MenuBackground').val();
        var font_color = '#' + $('#FontColor').data('default');
        var menu_color = $('#MenuColor').val();
        var menu_color_active = $('#ColorActive').val();
        var background_primary_color = $('#PrimaryBackgroundColor').val();
        var background_secondary_color = $('#SecondaryBackgroundColor').val();

        var color_exito = $('#ColorExito').val();
        var color_fallo = $('#ColorFallo').val();
        var color_information = $('#ColorInformacion').val();
        var color_disabled = $('#ColorDisabled').val();

        $('#btn-reset').on('click', function (e) {
            e.preventDefault();
            $('#PrimaryColor').val(primary_color).css({background: primary_color});
            $('#PrimaryFontColor').val(font_color_primary).css({background: font_color_primary});
            $('#SecondaryColor').val(secondary_color).css({background: secondary_color});
            $('#SecondaryFontColor').val(font_color_secondary).css({background: font_color_secondary});
            $('#TertiaryColor').val(tertiary_color).css({background: tertiary_color});
            $('#TertiaryFontColor').val(font_color_tertiary).css({backgroundColor: font_color_tertiary});
            $('#QuaternaryColor').val(quaternary_color).css({backgroundColor: quaternary_color});
            $('#QuaternaryFontColor').val(font_color_quaternary).css({backgroundColor: font_color_quaternary});
            $('#MenuBackground').val(menu_background).css({background: menu_background});
            $('#FontColor').val(font_color).css({color: font_color});
            $('#MenuColor').val(menu_color).css({background: menu_color});
            $('#ColorActive').val(menu_color_active).css({background: menu_color_active});
            $('#PrimaryBackground').val(background_primary_color).css({background: background_primary_color});
            $('#SecondaryBackground').val(background_secondary_color).css({background: background_secondary_color});
            $('#ColorExito').val(color_exito).css({background: color_exito});
            $('#ColorFallo').val(color_fallo).css({background: color_fallo});
            $('#ColorInformacion').val(color_information).css({background: color_information});
            $('#ColorDisabled').val(color_disabled).css({background: color_disabled});


            $('div.live .MenuColor').css({color: menu_color});
            $('div.live .ColorActive').css({color: menu_color_active});
            $('div.live .MenuBackground').css({backgroundColor: menu_background});
            $('div.live .FontColor').css({color: font_color});
            $('div.live .BackgroundPrimaryColor').css({backgroundColor: background_primary_color});
            $('div.live .bcfp').css({borderColor: background_primary_color});
            $('div.live .BackgroundSecondaryColor').css({backgroundColor: background_secondary_color});
            $('div.live .PrimaryColor').css({backgroundColor: 'red', borderColor: primary_color, color: font_color_primary});
            $('div.live .PrimaryColor').css({backgroundColor: primary_color, borderColor: primary_color, color: font_color_primary});
            $('div.live .PrimaryColorForLetter').css({color: primary_color});
            $('div.live .PrimaryFontColor').css({color: font_color_primary});
            $('div.live .SecondaryColor').css({backgroundColor: secondary_color, border_color: secondary_color, color: font_color_secondary});
            $('div.live .SecondaryColorForLetter').css({color: secondary_color});
            $('div.live .SecondaryFontColor').css({color: font_color_secondary});
            $('div.live .TertiaryColor').css({backgroundColor: tertiary_color});
            $('div.live .TertiaryFontColor').css({color: font_color_tertiary});
            $('div.live .TertiaryColorForLetter').css({color: tertiary_color});
            $('div.live .QuaternaryColor').css({backgroundColor: quaternary_color});
            $('div.live .QuaternaryFontColor').css({color: font_color_quaternary});
            $('div.live .QuaternaryInputColor').css({backgroundColor: font_color_quaternary, borderColor: quaternary_color});

            $('div.live .ColorExito').css({color: color_exito});
            $('div.live .ColorFallo').css({color: color_fallo});
            $('div.live .ColorInformacion').css({color: color_information});
            // $('div.live .ColorDisabled').css({color: color_disabled});
            setTimeout(function(){
                setContrast();
            }, 400);
        })
    };

    var load = function(){
        $('div.live .MenuColor').css({color: '#'+$('#MenuColor').val()});
        $('div.live .ColorActive').css({color: '#'+$('#ColorActive').val()});
        $('div.live .MenuBackground').css({backgroundColor: '#'+$('#MenuBackground').val()});
        $('div.live .FontColor').css({color: '#'+$('#FontColor').val()});
        $('div.live .BackgroundPrimaryColor').css({backgroundColor: '#'+$('#PrimaryBackgroundColor').val()});
        $('div.live .bcfp').css({borderColor: '#'+$('#PrimaryBackgroundColor').val()});
        $('div.live .BackgroundSecondaryColor').css({backgroundColor: '#'+$('#SecondaryBackgroundColor').val()});
        $('div.live .PrimaryColor').css({backgroundColor: '#'+$('#PrimaryColor').val(), borderColor: '#'+$('#PrimaryColor').val(), color: '#'+$('#PrimaryFontColor').val()});
        $('div.live .PrimaryColorForLetter').css({color: '#'+$('#PrimaryColor').val()});
        $('div.live .PrimaryFontColor').css({color: '#'+$('#PrimaryFontColor').val()});
        $('div.live .SecondaryColor').css({backgroundColor: '#'+$('#SecondaryColor').val(), borderColor: '#'+$('#SecondaryColor').val(), color: '#'+$('#SecondaryFontColor').val()});
        $('div.live .SecondaryColorForLetter').css({color: '#'+$('#SecondaryColor').val()});
        $('div.live .SecondaryFontColor').css({color: '#'+$('#SecondaryFontColor').val()});
        $('div.live .TertiaryColor').css({backgroundColor: '#'+$('#TertiaryColor').val()});
        $('div.live .TertiaryFontColor').css({color: '#'+$('#TertiaryFontColor').val()});
        $('div.live .TertiaryColorForLetter').css({color: '#'+$('#TertiaryColor').val()});
        $('div.live .QuaternaryColor').css({backgroundColor: '#'+$('#QuaternaryColor').val()});
        $('div.live .QuaternaryFontColor').css({color: '#'+$('#QuaternaryFontColor').val()});
        $('div.live .QuaternaryInputColor').css({backgroundColor: '#'+$('#QuaternaryFontColor').val(), borderColor: '#'+$('#QuaternaryColor').val()});
        $('div.live .ColorExito').css({backgroundColor: '#'+$('#ColorExito').val(), borderColor: '#'+$('#ColorExito').val()});
        $('div.live .ColorFallo').css({backgroundColor: '#'+$('#ColorFallo').val(), borderColor: '#'+$('#ColorFallo').val()});
        $('div.live .ColorInformacion').css({backgroundColor: '#'+$('#ColorInformacion').val(), borderColor: '#'+$('#ColorInformacion').val()});

        $('input#MenuColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .MenuColor').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#ColorActive').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .ColorActive').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#MenuBackground').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .MenuBackground').css({backgroundColor: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#FontColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .FontColor').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#PrimaryBackgroundColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .BackgroundPrimaryColor').css({backgroundColor: '#'+$this.val()});
                $('div.live .PrimaryColor').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#SecondaryBackgroundColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .BackgroundSecondaryColor').css({backgroundColor: '#'+$this.val()});
                $('div.live .SecondaryColor').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#NetworkFontColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .NetworkFontColor').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#PrimaryColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .PrimaryColor').css({backgroundColor: '#'+$this.val()});
                $('div.live .PrimaryColor').css({borderColor: '#'+$this.val()});
                $('div.live .PrimaryColorForLetter').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#PrimaryFontColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .PrimaryFontColor').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#SecondaryColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .SecondaryColor').css({backgroundColor: '#'+$this.val()});
                $('div.live .SecondaryColor').css({borderColor: '#'+$this.val()});
                $('div.live .SecondaryColorForLetter').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#SecondaryFontColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .SecondaryFontColor').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#TertiaryColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .TertiaryColor').css({backgroundColor: '#'+$this.val()});
                $('div.live .TertiaryColorForLetter').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#TertiaryFontColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .TertiaryFontColor').css({color: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#QuaternaryColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .QuaternaryColor').css({backgroundColor: '#'+$this.val()});
                $('div.live .QuaternaryInputColor').css({borderColor: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#QuaternaryFontColor').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .QuaternaryFontColor').css({color: '#'+$this.val()});
                $('div.live .QuaternaryInputColor').css({backgroundColor: '#'+$this.val()});
                setContrast();
            }, 100);
        });

        $('input#ColorExito').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .ColorExito').css({backgroundColor: '#'+$this.val()});
                $('div.live .ColorExito').css({borderColor: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#ColorFallo').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .ColorFallo').css({backgroundColor: '#'+$this.val()});
                $('div.live .ColorFallo').css({borderColor: '#'+$this.val()});
                setContrast();
            }, 100);
        });
        $('input#ColorInformacion').change(function(){
            var $this = $(this);
            setTimeout(function(){
                $('div.live .ColorInformacion').css({backgroundColor: '#'+$this.val()});
                $('div.live .ColorInformacion').css({borderColor: '#'+$this.val()});
                setContrast();
            }, 100);
        });

        $('.cnt-color-choose input').on('blur',function(){
            setContrast();
        });

    }

    var setContrast = function(){
        setTimeout(function(){
            $('.cnt-color-choose input').each(function(){
                var rgb = $(this).css('backgroundColor');
                var colors = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
                var brightness = 1;

                var r = colors[1];
                var g = colors[2];
                var b = colors[3];

                var ir = Math.floor((255-r)*brightness);
                var ig = Math.floor((255-g)*brightness);
                var ib = Math.floor((255-b)*brightness);
                $(this).attr('style', 'background-color: ' + $(this).css("background-color") + '; color: rgb('+ir+','+ig+','+ib+') !important;');
            });
        }, 300);
    }

    var sendData = function() {
        $("#btn-guardar").on('click',function(e){
            if($('#aag-region-select').length > 0){
                e.preventDefault();
                $('#aag-region-select').attr('disabled', false);
                $('#form').submit();
            }
        });
    }

    return {
        load: function () {
            addNetwork();
            deleteNetwork();
            // reset_values();
            crop();
            load();
            setContrast();
            sendData();
        }
    }


})();