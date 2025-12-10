$(document).ready(function () {
    JQueryHelper.load();
    FormHelper.load();
    DivHelper.load();
    Tools.load();
    Loader.load();
    CommonActions.load();
});


var JQueryHelper = (function () {

    var translateAdditionalTerms = function () {
        var translations = {
            "buttonText": {
                "en-GB": "Select a date",
                "es": "Seleccionar una fecha"
            }
        };
        $.each(translations, function (key, languages) {
            $.each(languages, function (locale, message) {
                if (typeof $.datepicker.regional[locale] != "undefined") {
                    $.datepicker.regional[locale][key] = message;
                }
            });
        });

        for (var locale in $.datepicker.regional) {
            if ($.datepicker.regional.hasOwnProperty(locale) && locale !== "") {
                $.datepicker.setDefaults($.datepicker.regional[locale]);
            }
        }
    };

    var cargarAlertWebExterna = function () {
        $('body').on('click', 'a', function (e) {
            var url = this.href;
            if (url.indexOf(document.location.hostname) < 0 && url.indexOf('javascript:') < 0 && url != '') {
                e.preventDefault();
                swal({
                    title: $.i18n._('General.Leave?'),
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonText: $.i18n._('General.Yes'),
                    cancelButtonText: $.i18n._('General.No'),
                }).then(function (result) {
                    if (result.value) {
                        var win = window.open(url);
                        win.focus();
                    }
                });
            }
        });
    };

    var cargarTimepicker = function () {
        $('.timepicker').each(function () {
            $(this).timepicker();
        })
    };

    var cargarDatepicker = function () {
        for (var locale in $.datepicker.regional) {
            $.datepicker.regional[locale]["firstDay"] = 1;
        }

        translateAdditionalTerms();

        $('.fecha-js').each(function () {
            $(this).datepicker({
                dateFormat: 'dd-mm-yy'
            });
        });

        $(".fecha-js.min-js").each(function () {
            $(this).datepicker("option", "minDate", $(this).data('mindate'));
        });

        $(".fecha-js.max-js").each(function () {
            $(this).datepicker("option", "maxDate", $(this).data('maxdate'));
        });

        $(".fecha-js.from-js").datepicker("option", "onClose", function (selectedDate) {
            $(".fecha-js" + $(this).data('to')).datepicker("option", "minDate", selectedDate);
        });

        $(".fecha-js.from-js-plus-one").datepicker("option", "onClose", function (selectedDate) {
            $(".fecha-js" + $(this).data('to')).datepicker("option", "minDate", moment(selectedDate, "DD-MM-YYYY").add(1, 'days').format('DD-MM-YYYY'));
        });

        $(".fecha-js.to-js").datepicker("option", "onClose", function (selectedDate) {
            $(".fecha-js" + $(this).data('from')).datepicker("option", "maxDate", selectedDate);
        });

    };

    var cargarDatepickerModified = function () {
        $(".fecha-js.to-js").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
        });
        $(".fecha-js.from-js").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
        });
    };

    var cargarSweetsAlerts = function () {
        $(".swal-msg").off('click').on('click', function (e) {
            e.preventDefault();
            var url = $(this).data('url');
            swal({
                title: $(this).data('confirmmsg'),
                type: $(this).data('type'),
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function (result) {
                if (result.value) {
                    window.location.href = url;
                    cargarSweetsAlerts();
                }
            })
        });

        $(".swal-msg-ajax").off('click').on('click', function (e) {
            e.preventDefault();
            data = {};
            data.id = $(this).data('id');
            var div = $(this).data('div');
            var url = $(this).data('url');
            var form = $(this).data('form');
            swal({
                title: $(this).data('confirmmsg'),
                type: $(this).data('type'),
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(url, data);
                    request.done(function (data) {
                        if (form == 'task') {
                            $('#modal_form_task').find(div).html(data);
                            var url_x = '/../tasks/ajax_render_tasks';
                            var data_x = {};
                            data_x.appointment_id = $('#appointment-id').val();
                            var request_x = PeticionAjax.post(url_x, data_x);
                            request_x.done(function (results) {
                                $('#cnt_task').html(results)
                            });
                        } else {
                            $(div).html(data);
                        }
                        cargarSweetsAlerts();
                    });
                }
            })
        });
    };

    var cargarLinks = function () {
        $(".anchor_nav_link").on('click', function () {
            var link = $(this).attr('href');
            $('html,body').animate({ scrollTop: ($(link).offset().top - 165) }, 'slow');
            return false;
        });
        $(".anchor_nav_link_crm").on('click', function () {
            var link = $(this).attr('href');
            $('html,body').animate({ scrollTop: ($(link).offset().top - 206) }, 'slow');
            return false;
        });
    };

    return {
        load: function ($context) {
            cargarAlertWebExterna();
            cargarDatepickerModified();
            cargarDatepicker();
            cargarSweetsAlerts();
            cargarTimepicker();
            cargarLinks();
        }
    }
})();


var DivHelper = (function () {

    var onClickShowDiv = function ($item, $div) {
        $item.click(function () {
            _mostrar($div);
        });
    };

    var onClickHideDiv = function ($item, $div) {
        $item.click(function () {
            _ocultar($div);
        });
    };

    var onClickShowHide = function () {
        $('.on-click-show-hide-js').click(function (event) {
            event.preventDefault();
            var $div = $($(this).data('div'));
            if ($div.is(':visible')) {
                _ocultar($div);
            } else {
                _mostrar($div);
            }
        });
    };

    var _mostrar = function ($item) {
        $item.show();
    };

    var _ocultar = function ($item) {
        $item.hide();
    };

    return {
        load: function () {
            onClickShowHide();
        },
        onClickShowDiv: function ($item, $div) {
            onClickShowDiv($item, $div);
        },
        onClickHideDiv: function ($item, $div) {
            onClickHideDiv($item, $div);
        }
    }
})();

var FormHelper = (function () {

    var cargarLinkConfirm = function () {
        $('.link-confirm-js').click(function (event) {
            if (!confirm($(this).data('confirmmsg'))) {
                event.preventDefault();
            }
        });
    };

    var deshabilitarIntro = function () {
        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                return false;
            }
        });
    };

    var habilitarCampos = function (form, habilitar) {
        $(form + " input," + form + " select," + form + " textarea").attr('disabled', habilitar);
    };

    var cargarSubmitFormulario = function () {
        $('.submit-form').click(function () {
            $(this).parents('form').each(function () {
                $(this).submit();
            });
        });
    };

    var cargarTodosCheckBox = function () {
        $('input[type="checkbox"].checkBoxTodos').change(function () {
            $parent = $(this).closest(".contain-all-checkboxs-js");
            if ($(this).is(':checked')) {
                $parent.find('input[type="checkbox"]').prop('checked', true);
            } else {
                $parent.find('input[type="checkbox"]').prop('checked', false);
            }
        });
    };

    var cargarLimpiarInputs = function () {
        $('.limpiar-form-js').click(function (event) {
            event.preventDefault();
            $parent = $(this).closest(".contain-limiar-form-js");
            $parent.find('input, select, textarea').val('');
        });
    };

    var cargarDragAndDrop = function () {
        $('.dragdrop-js:not(.dragdrop-v2-js)').each(function () {
            $(this).niceFileInput();
            var fileWrapperParent = $(this).parents('.fileWrapper:not(.fileWrapperList)');
            if ($(this).hasClass('dragdrop-multiple-js')) {
                $(this).next().hide();
                fileWrapperParent.addClass('fileWrapperMultiple');
            }

            $(this).change(function () {
                fileWrapperParent = $(this).parents('.fileWrapper:not(.fileWrapperList)');
                var fileWrapperDragDropDeleteFile = '<span class="dragdrop-delete-file-js aag-icon-papelera"></span>';
                if ($(this).hasClass('dragdrop-multiple-js')) {
                    if (isFilePdf() == true) {
                        var fileWrapperClone = fileWrapperParent.clone(true, true);
                        var fileWrapperParentInputText = fileWrapperParent.find('.fileInputText');
                        fileWrapperParentInputText.show();
                        fileWrapperParentInputText.after(fileWrapperDragDropDeleteFile);
                        fileWrapperParent.addClass('fileWrapperList');
                        $(this).parent().before(fileWrapperClone);
                        fileWrapperClone.children('input[type="file"]').val('');
                        fileWrapperClone.children('.fileInputText').val('');
                    }
                } else {
                    if (fileWrapperParent.find('.dragdrop-delete-file-js').html() == undefined) {
                        fileWrapperParent.find('.fileInputText').after(fileWrapperDragDropDeleteFile);
                    }
                }
                cargarEliminarDragAndDrop();
            });
        });
    };

    var cargarEliminarDragAndDrop = function () {
        $('.dragdrop-delete-file-js').click(function () {
            $fileWrapperParent = $(this).parents('.fileWrapper');
            if ($fileWrapperParent.hasClass('fileWrapperList')) {
                $fileWrapperParent.remove();
            } else {
                $fileWrapperParent.find('input[type="file"]').val('');
                $fileWrapperParent.find('.fileInputText').val('');
                $(this).remove();
            }
            if ($('#image-input') !== undefined && $('#image-input').data('images_upload') !== undefined) {
                $('#image-input').data('images_upload', $('#image-input').data('images_upload') - 1);
            }
        });
    };

    var isFilePdf = function () {
        var fileNameAppointment = null;
        var fileName = null;

        if ($('#appointmen_files').val() != undefined) {
            fileNameAppointment = $('#appointmen_files').val().toLowerCase();
        }
        if ($('#files').val() != undefined) {
            fileName = $('#files').val().toLowerCase();
        }
        if ((fileNameAppointment !== null && !fileNameAppointment.endsWith('.pdf')) && (fileName !== null && !fileName.endsWith('.pdf'))) {
            return false;
        }
        return true;
    }

    // var selectMaterialDesign = function(){
    //     $('.select2-multiple').each(function(){
    //         var select = $(this);

    //         var attr = select.attr('multiple');

    //         if (attr != undefined) {
    //             // var label = select.parent().find('label');

    //             // changeSelectMultiple(select, label);
    //             // select.on('change', function(){

    //             //     changeSelectMultiple(select, label);
    //             // });
    //         }else{
    //             var label = select.prev();
    //             var span = select.next();
    //             changeSelect(select, label, span);
    //             select.on('change', function(){
    //                 var span = select.next();
    //                 changeSelect(select, label, span);
    //             });
    //         }
    //     });
    // };

    // var changeSelect = function(select, label, span){
    //     if( select.val() != ''){
    //         label.animate({
    //             top: -2,
    //             color: primary_color
    //         }, 50 );
    //         span.animate({
    //             "border-bottom-width": "1px"
    //         }, 50);
    //     } else {
    //         label.animate({
    //             top: -2,
    //             color: '#999'
    //         }, 50 );
    //         span.animate({
    //             "border-bottom-width": "0"
    //         }, 50);
    //     }
    // };

    // var changeSelectMultiple = function(select, label){
    //     if( select.val() != ''){
    //         label.animate({
    //             top: -12,
    //             color: primary_color
    //         }, 50 );
    //     } else {
    //         label.animate({
    //             top: -12,
    //             color: '#999'
    //         }, 50 );
    //     }
    // };

    var clearFields = function () {
        $('#clear_field').click(function (e) {
            e.preventDefault();
            $('.clear_field').each(function () {
                if ($(this).attr('type') == 'checkbox') {
                    $(this).prop('checked', false);
                } else if (typeof ($(this).attr('multiple')) != 'undefined') {
                    $(this).select2('val', '-1');
                } else if ($(this).attr('type') == 'range') {
                    $(this).val($(this).attr('max')).trigger('change');
                    $("#rangeValue2").text($("#input_cost_training").val());
                    $("#rangeValue").text($("#input_price_credit").val());
                    $("#rangeValue").text($("#training_course_price_credit").val());
                } else {
                    $(this).val('').trigger('change');
                }
            });
        });
    };

    var resolveBugChromeScrollPageUp = function () {
        $('textarea').on('keydown', function (e) {
            textAreaValue = $(this).val();
            if (e.keyCode === 33) {
                e.preventDefault();
                e.target.setSelectionRange(0, 0);
            }
            if (e.keyCode === 34 && textAreaValue) {
                e.preventDefault();
                e.target.setSelectionRange(textAreaValue.length, textAreaValue.length);
            }
        });
    };

    return {
        load: function () {
            cargarSubmitFormulario();
            cargarTodosCheckBox();
            cargarLimpiarInputs();
            cargarLinkConfirm();
            cargarDragAndDrop();
            // selectMaterialDesign();
            clearFields();
            resolveBugChromeScrollPageUp();
        },
        deshabilitarIntro: function () {
            deshabilitarIntro();
        },
        habilitarCampos: function (form, habilitar) {
            habilitarCampos(form, habilitar);
        },
        cargarEliminarDragAndDrop: function () {
            cargarEliminarDragAndDrop();
        }
    }
})();


var Tools = (function () {

    var openCloseMenu = function () {
        $('#oc-menu').click(function () {
            $('body > div#container').toggleClass('menuToggle');
        });
    }

    var menuDropdown = function () {
        $('.oc-dropdown').click(function (e) {
            e.preventDefault();
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            }
            else {
                $(this).addClass('active');
            }
        });
    };

    var showSearcher = function () {
        if ($('div.communication-searcher').length) {
            $('.show-searcher').show();
            $('.show-searcher').click(function () {
                $('div.communication-searcher').slideToggle();
            });
        }
    };

    var checkboxStyle = function () {
        $('.checkbox-wrapper').on('click', function () {
            $(this).toggleClass('checked');
            if ($(this).hasClass('checked')) {
                $('input[type="checkbox"]', this).prop('checked', true);
            }
            else {
                $('input[type="checkbox"]', this).prop('checked', false);
            }
        })
    };

    var checksIE11 = function () {

        $('.cont-radio label span').css('display', 'block');
        $('.cont-radio label span').click(function (e) {
            e.preventDefault();
            if ($(this).parent().find('input[type="radio"]').prop('checked') === true) {
                $(this).parent().find('input[type="radio"]').prop('checked', false);
            }
            else {
                $(this).parent().find('input[type="radio"]').prop('checked', true);
            }
        });

        $('.cont-checkbox label span').css('display', 'block');
        $('.cont-checkbox label span').click(function (e) {
            e.preventDefault();
            if ($(this).parent().find('input[type="checkbox"]').prop('checked') === true) {
                $(this).parent().find('input[type="checkbox"]').prop('checked', false);
            }
            else {
                $(this).parent().find('input[type="checkbox"]').prop('checked', true);
            }
        });

        $('.cont-services label span').css('display', 'block');
        $('.cont-services label span').click(function (e) {
            e.preventDefault();
            if ($(this).parent().find('input[type="checkbox"]').prop('checked') === true) {
                $(this).parent().find('input[type="checkbox"]').prop('checked', false);
            }
            else {
                $(this).parent().find('input[type="checkbox"]').prop('checked', true);
            }
        });

        $('.cont-vehicles label span').css('display', 'block');
        $('.cont-vehicles label span').click(function (e) {
            e.preventDefault();
            if ($(this).parent().find('input[type="checkbox"]').prop('checked') === true) {
                $(this).parent().find('input[type="checkbox"]').prop('checked', false);
            }
            else {
                $(this).parent().find('input[type="checkbox"]').prop('checked', true);
            }
        });
    };

    var cargarComportamientoTableTrLink = function () {
        $('tr.link-js td').click(function () {
            if (!$(this).hasClass('no-link-js')) {
                if ($(this).parent('tr').data('url') != '') {
                    window.location = $(this).parent('tr').data('url');
                }
            }
        });
    };

    var ampliacion = function () {
        if ($('.ampliar-js').length > 0) {
            var abrir = function (elemento) {
                elemento.removeClass('cerrado');
                elemento.addClass('abierto');
                elemento.find('.ampliacion-js').slideDown();
            };
            var cerrar = function (elemento) {
                elemento.removeClass('abierto');
                elemento.addClass('cerrado');
                elemento.find('.ampliacion-js').slideUp();
            };
            $('.ampliar-js').each(function () {
                if (!$(this).hasClass('abierto')) { $(this).find('.ampliacion-js').hide(); }
            });
            if ($('.abrir-todos').length > 0) {
                $('.abrir-todos').click(function () {
                    var padre = $(this).closest('.contenedor-ampliaciones');
                    padre.find('.ampliar-js').each(function () {
                        abrir($(this));
                    });
                });
            }
            if ($('.cerrar-todos').length > 0) {
                $('.cerrar-todos').click(function () {
                    var padre = $(this).closest('.contenedor-ampliaciones');
                    padre.find('.ampliar-js').each(function () {
                        cerrar($(this));
                    });
                });
            }
            $('.mostrar-ampliado').click(function () {
                var padre = $(this).closest('.ampliar-js');
                if (padre.hasClass('abierto')) { cerrar(padre); } else { abrir(padre); }
            });
        }
    };
    var flechaSubir = function () {
        if ($('#boton-subir-cabecera').length > 0) {
            $('#boton-subir-cabecera').click(function () {
                $('html, body').animate({ scrollTop: 0 }, 1000);
            });
            $(window).scroll(function () {
                if ($(this).scrollTop() > 500) {
                    $('#boton-subir-cabecera').fadeIn();
                } else {
                    $('#boton-subir-cabecera').fadeOut();
                }
            });
        }
    };

    var loadModalView = function () {
        $(".open-modal-js").click(function (event) {
            event.preventDefault();
            var reveal_id = $(this).data('reveal-id');
            PeticionAjax
                .get(this.href)
                .done(function (data) {
                    $('#' + reveal_id).children().first().html(data);
                });
        });
    };

    var mascaraMapa = function () {
        $(document).ready(function () {
            if (Modernizr.touch) {
                if ($('.contenedor-mapa').length > 0) {
                    $('div.contenedor-mapa').prepend('<div class="mascaraMovil abierta"><span class="ion-arrow-shrink icon" data-pack="default"></span></div>');
                    $('.mascaraMovil').click(function () {
                        if ($('.mascaraMovil').hasClass('abierta')) {
                            $('.mascaraMovil .icon').addClass('ion-arrow-expand');
                            $('.mascaraMovil .icon').removeClass('ion-arrow-shrink');


                            $('.mascaraMovil').animate({
                                width: '30px'
                            }, function () {
                                $('.mascaraMovil').removeClass('abierta');
                            });
                        }
                        else {
                            $('.mascaraMovil .icon').addClass('ion-arrow-shrink');
                            $('.mascaraMovil .icon').removeClass('ion-arrow-expand');


                            $('.mascaraMovil').animate({
                                width: '100%'
                            }, function () {
                                $('.mascaraMovil').addClass('abierta');
                            });
                        }
                    });
                }
            }
        });
    }

    var addClassTab = function () {
        $('.aag-tabs').find('> ul').unbind();
        $('.aag-tabs').each(function () {
            var itemTab = $(this);
            if (itemTab.find('> ul').outerHeight() > itemTab.find('> ul > li:first-child').height()) {
                itemTab.removeClass('one-line');
                itemTab.addClass('multiple-lines');
            }
            else {
                itemTab.addClass('one-line');
                itemTab.removeClass('multiple-lines');
            }

            itemTab.find('> ul').click(function (e) {
                if (e.target !== this) { return; }
                itemTab.toggleClass('open-mobile');
            });
        });
    }

    var tabsResponsive = function () {
        addClassTab();
        $(window).on('resize', function () {
            addClassTab();
        });
    }

    var huecoMinimo = -50;
    var abrirMenuPrincipal = function () {
        $('.abrir-menu-principal').click(function (event) {
            event.preventDefault();
            if (parseInt($('header#header').css('width')) < '50') {
                $.cookie('menuAbierto', 2, { path: '/' });
                $('header#header').animate({
                    width: "15.55rem"
                }, 500);
                $('header#header').addClass('ml-abierto');
                $('header#header').removeClass('ml-cerrado');
                if (huecoMenu > huecoMinimo) {
                    $('.contenedor-ancho').animate({
                        paddingLeft: "15.55rem"
                    }, 500);
                }
                $('.cnt-desplegable').find('span:not(:first)').css('left', '1rem');
                $('.cnt-desplegable2').find('span:not(:first)').css('left', '1rem');

                $('.cnt-desplegable').find('span:first').css('left', '0');
                $('.cnt-desplegable').find('i').css('left', '100%');
                $('.cnt-desplegable2').find('span:first').css('left', '0');
                $('.cnt-desplegable2').find('i').css('left', '100%');
                $('#icon-menu').removeClass('ion-chevron-right').addClass('ion-chevron-left');
            }
            else {
                $.cookie('menuAbierto', 1, { path: '/' });
                $('header#header').animate({
                    width: "45px"
                }, 500, function () {
                    $('header#header').addClass('ml-cerrado');
                    $('header#header').removeClass('ml-abierto');
                });
                if (huecoMenu < huecoMinimo) {
                    $('.contenedor-calendario.contenedor-ancho').css('padding-left', '45px');
                }
                else {
                    $('.contenedor-ancho').animate({
                        paddingLeft: "45px"
                    }, 500);
                }
                $('.cnt-desplegable').find('span:not(:first)').css('left', '0');
                $('.cnt-desplegable2').find('span:not(:first)').css('left', '0');

                $('.cnt-desplegable').find('span:first').css('left', '-7px');
                $('.cnt-desplegable').find('i').css('left', '53px');
                $('.cnt-desplegable2').find('span:first').css('left', '-7px');
                $('.cnt-desplegable2').find('i').css('left', '53px');
                $('#icon-menu').removeClass('ion-chevron-left').addClass('ion-chevron-right');
            }
        });
        var huecoMenu = ($(window).width() - 1100) / 2;
        abrirCerrarMenu(huecoMenu);
        $(window).resize(function () {
            huecoMenu = ($(window).width() - 1100) / 2;
            abrirCerrarMenu(huecoMenu);
        });
    };

    var abrirCerrarDesplegable = function (huecoMenu) {
        $('.cnt-desplegable').click(function () {
            $('.cnt-desplegable > ul').slideToggle();
        });
        $('.cnt-desplegable2').click(function () {
            $('.cnt-desplegable2 > ul').slideToggle();
        });
    };

    var abrirCerrarMenu = function (huecoMenu) {
        if ($.cookie('menuAbierto') == 1 && huecoMenu > huecoMinimo) {
            $('header#header').css('width', '45px');
            $('header#header').addClass('ml-cerrado');
            $('header#header').removeClass('ml-abierto');
            $('.contenedor-ancho').css({ 'max-width': 'none', 'padding-left': '45px' });
            $('.cnt-desplegable').find('span:not(:first)').css('left', '0');
            $('.cnt-desplegable2').find('span:not(:first)').css('left', '0');

            $('.cnt-desplegable').find('span:first').css('left', '-7px');
            $('.cnt-desplegable').find('i').css('left', '53px');
            $('.cnt-desplegable2').find('span:first').css('left', '-7px');
            $('.cnt-desplegable2').find('i').css('left', '53px');
            $('#icon-menu').removeClass('ion-chevron-left').addClass('ion-chevron-right');
        }
        else {
            if ($.cookie('menuAbierto') == 2 && huecoMenu > huecoMinimo) {
                $('header#header').css('width', '15.55rem');
                $('header#header').addClass('ml-abierto');
                $('header#header').removeClass('ml-cerrado');
                $('.contenedor-ancho').css({ 'max-width': 'none', 'padding-left': '15.55rem' });

                $('.cnt-desplegable').find('span:first').css('left', '0');
                $('.cnt-desplegable').find('i').css('left', '100%');
                $('.cnt-desplegable2').find('span:first').css('left', '0');
                $('.cnt-desplegable2').find('i').css('left', '100%');
                $('#icon-menu').removeClass('ion-chevron-right').addClass('ion-chevron-left');
            }
            else {
                if (huecoMenu < huecoMinimo) {
                    $('header#header').css('width', '45px');
                    $('header#header').addClass('ml-cerrado');
                    $('header#header').removeClass('ml-abierto');
                    $('.contenedor-ancho').css({ 'max-width': 'none', 'padding-left': '45px' });

                    $('.cnt-desplegable').find('span:first').css('left', '-7px');
                    $('.cnt-desplegable').find('i').css('left', '53px');
                    $('.cnt-desplegable2').find('span:first').css('left', '-7px');
                    $('.cnt-desplegable2').find('i').css('left', '53px');
                    $('#icon-menu').removeClass('ion-chevron-left').addClass('ion-chevron-right');
                }
                else {
                    $('header#header').css('width', '15.55rem');
                    $('header#header').addClass('ml-abierto');
                    $('header#header').removeClass('ml-cerrado');
                    $('.contenedor-ancho').css({ 'max-width': 'none', 'padding-left': '15.55rem' });

                    $('.cnt-desplegable').find('span:first').css('left', '0');
                    $('.cnt-desplegable').find('i').css('left', '100%');
                    $('.cnt-desplegable2').find('span:first').css('left', '0');
                    $('.cnt-desplegable2').find('i').css('left', '100%');
                    $('#icon-menu').removeClass('ion-chevron-right').addClass('ion-chevron-left');
                }
            }
        }
    };

    var buscadorResponsive = function () {
        $('.abrir-buscador-movil').click(function () {
            $(this).parent().find('.buscador-mo-movil').slideToggle(function () { $(document).foundation(); });
        });
    };

    var dashboardResponsive = function () {
        $('.open-tile-dash').click(function () {
            $(this).parent().find('.hide-tile-dash').slideToggle(function () { $(document).foundation(); });
        });
    };

    var toggleDropDownMenu = function () {
        $('.menu-usuario li.has-dropdown').on('click', function () {
            $(this).find('ul').slideToggle(200);
        });
    };

    var hideFlashMessage = function () {
        var seconds = $('.alert-box-js').data('hide_seconds');
        if (seconds != undefined) {
            $('.alert-box-js').delay(seconds * 1000).slideToggle();
        };
        $('.alert-flash-js').on('click', function () {
            $(this).hide();
        });
    };

    var actualizarFlashMessage = function () {
        PeticionAjax.get('/paginas/flash_message').done(function (data) { $('.alert-flash-js').html(data); $(document).foundation(); });
    }

    return {
        load: function () {
            cargarComportamientoTableTrLink();
            openCloseMenu();
            menuDropdown();
            showSearcher();
            checkboxStyle();
            checksIE11();
            ampliacion();
            flechaSubir();
            loadModalView();
            mascaraMapa();
            tabsResponsive();
            abrirMenuPrincipal();
            buscadorResponsive();
            dashboardResponsive();
            abrirCerrarDesplegable();
            toggleDropDownMenu();
            hideFlashMessage();
        },
        actualizarFlashMessage: function () {
            actualizarFlashMessage();
        },
        loadTr: function () {
            cargarComportamientoTableTrLink();
        }
    }
})();


var PeticionAjax = (function () {

    var get = function (url, data) {
        return $.ajax({
            type: "GET",
            encoding: "UTF-8",
            url: url,
            data: data
        });
    };

    var post = function (url, data) {
        return $.ajax({
            type: "POST",
            encoding: "UTF-8",
            url: url,
            data: data
        });
    };

    var getJSON = function (url, data) {
        return $.ajax({
            type: "GET",
            encoding: "UTF-8",
            dataType: "json",
            url: url,
            data: data
        });
    };

    var postJSON = function (url, data) {
        return $.ajax({
            type: "POST",
            encoding: "UTF-8",
            dataType: "json",
            url: url,
            data: data
        });
    };

    var mostrarCargando = function () {
        var loading =
            '<div class="preloader" style="display: block !important;"> ' +
            '<div class="spinner"> ' +
            '<img src="/img/AllianceAutomotiveGroup.png" alt="GNM AAG"> ' +
            '<br> ' +
            '<div class="rect1"></div> ' +
            '<div class="rect2"></div> ' +
            '<div class="rect3"></div> ' +
            '<div class="rect4"></div> ' +
            '<div class="rect5"></div> ' +
            '<div class="rect6"></div> ' +
            '<div class="rect7"></div> ' +
            '<div class="rect8"></div> ' +
            '</div> ' +
            '</div>';
        $('body').append(loading);
    };

    var ocultarCargando = function () {
        $('.preloader').remove();
    };

    return {
        get: function (url, data) {
            return get(url, data);
        },
        post: function (url, data) {
            return post(url, data);
        },
        getJSON: function (url, data) {
            return getJSON(url, data);
        },
        postJSON: function (url, data) {
            return postJSON(url, data);
        },
        mostrarCargando: function () {
            mostrarCargando();
        },
        ocultarCargando: function () {
            ocultarCargando();
        }
    }
})();

var Alertas = (function () {
    var show = function (selector, tipo, mensaje) {
        selector.html(
            "<div data-alert class='alert-box " + tipo + " m-1'> " +
            mensaje + "<a href='#' class='close'>&times;</a>" +
            "</div>"
        );
        $(document).foundation('alert', 'reflow');
    };

    return {
        show: function (selector, tipo, mensaje) {
            return show(selector, tipo, mensaje);
        }
    }
})();

var Loader = (function () {
    var loadBehaviour = function () {
        $(document).ajaxStart(function () {
            document.body.style.overflow = 'hidden';
            jQuery(".preloader").css("display", "block");
        });
        $(document).ajaxStop(function () {
            setTimeout(function () {
                jQuery(".preloader").css("display", "none");
                document.body.style.overflow = 'visible';
            }, 300);
        });
        $(document).ajaxError(function () {
            jQuery(".preloader").css("display", "none");
            document.body.style.overflow = 'visible';
        });
        window.onerror = function () {
            jQuery(".preloader").css("display", "none");
            document.body.style.overflow = 'visible';
        };
    };

    return {
        load: function () {
            loadBehaviour();
        }
    }
})();

var CommonActions = (function () {
    var delete_action = function () {
        $(".delete-js").click(function (e) {
            e.preventDefault();
            var element = $(this);
            var url = element.data('url');
            var url_redirect = element.data('url_redirect');
            var confirmmsg = element.data('confirmmsg');
            var msg_correct = element.data('msg_correct');
            var msg_bad = element.data('msg_bad');
            swal({
                title: confirmmsg,
                type: "info",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.postJSON(url);
                    request.done(function (data) {
                        if (data.precess == 'true') {
                            swal({ type: "success", title: msg_correct }).then(function () {
                                window.location.replace(url_redirect);
                            })
                        } else {
                            swal({ type: "error", title: msg_bad, text: data.error_text })
                        }
                    });
                }
            });
        });
    };

    var new_delete = function () {
        $(".new-delete-js").click(function (e) {
            e.preventDefault();
            var element = $(this);
            var delete_url = element.data('url');
            var url_redirect = element.data('url_redirect');
            var confirmmsg = element.data('confirmmsg');
            swal({
                title: confirmmsg,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(delete_url);
                    request.done(function (result) {
                        if (result == 2) {
                            swal({
                                title: $.i18n._('Config.Error_delete_item_in_use'),
                                type: "error",
                            }).then(function (result) {
                                window.location.replace(url_redirect);
                            });
                        } else if (result) {
                            swal({
                                title: $.i18n._("Constants.Message_well_deleted"),
                                type: "success",
                            }).then(function (result) {
                                window.location.replace(url_redirect);
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

    var go_back_action = function () {
        const login = 'users/login';
        $('.go-back-js').click(function (event) {
            if (document.referrer.split('/')[2] && document.referrer.split('/')[2] == window.location.host && !document.referrer.includes(login)) {
                //go back
                history.back();
            } else {
                //redirect home
                window.location = '/';
            }
        });
    };

    return {
        load: function () {
            delete_action();
            new_delete();
            go_back_action();
        }
    }
})();

var ValidateJS = (function () {
    var validateCharacters = function (string_to_validate) {
        var patt = /(\{|\}|>|<|~|\\|\º|%|\$|\#|\*)|\[|\]/;
        var err = patt.test(string_to_validate);
        if (err) {
            return false;
        }

        return true;
    };

    return {
        validateCharacters: function (string_to_validate) {
            return validateCharacters(string_to_validate);
        }
    }
})();
