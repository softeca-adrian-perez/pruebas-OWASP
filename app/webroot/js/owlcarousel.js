$(document).ready(function () {
    setTimeout(function () { Carousel.load(); }, 500)
    $(window).resize(function () {
        owl_top.trigger('to.owl.carousel', 0);
        Carousel.load_width();
    });
});
var Carousel = (function () {

    var top_carousel = function () {
        owl_top = $("#top_carousel");
        owl_top.owlCarousel({
            items: $('#communications-carousel').data('communications'),
            margin: 15,
            //loop: true,
            autoWidth: true,
            nav: true,
            autoplay: true,
            autoplayHoverPause: true,
            autoplayTimeout: 5000,
            smartSpeed: 900,
            navText: ["<span class='c-blanco ion-arrow-left-c'></span>", "<span class='c-blanco ion-arrow-right-c'></span>"]
        });
        owl_top.on('mouseleave', function () {
            owl_top.trigger('stop.owl.autoplay');
            owl_top.trigger('play.owl.autoplay', [5000]);
        });
    };


    var left_carousel = function () {
        var owl = $("#left_carousel");
        owl.owlCarousel({
            items: 1,
            loop: true,
            nav: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            smartSpeed: 900,
            navText: ["<span class='c-blanco ion-arrow-left-c'></span>", "<span class='c-blanco ion-arrow-right-c'></span>"]
        });
        owl.on('mouseleave', function () {
            owl.trigger('stop.owl.autoplay');
            owl.trigger('play.owl.autoplay', [5000]);
        })
    };

    var sections_carousel = function () {
        var owl = $(".sections_carousel");
        owl.owlCarousel({
            items: 1,
            loop: true,
            nav: false,
            autoplay: true,
            autoplayTimeout: 4000,
            autoplayHoverPause: true,
            smartSpeed: 900,
            navText: [""]
        });
        owl.on('mouseleave', function () {
            owl.trigger('stop.owl.autoplay');
            owl.trigger('play.owl.autoplay', [5000]);
        })
    };

    var right_carousel = function () {
        var owl = $("#right_carousel");
        owl.owlCarousel({
            items: 1,
            loop: true,
            nav: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            smartSpeed: 900,
            navText: ["<span class='c-blanco ion-arrow-left-c'></span>", "<span class='c-blanco ion-arrow-right-c'></span>"]
        });

        owl.on('mouseleave', function () {
            owl.trigger('stop.owl.autoplay');
            owl.trigger('play.owl.autoplay', [5000]);
        })
    };

    var popup_carousel = function () {

        var owl = $("#popup_carousel");
        setTimeout(
            function () {
                if ($("#myModal").data('popup') > 1) {
                    owl.owlCarousel({
                        items: 1,
                        loop: true,
                        nav: true,
                        autoplay: false,
                        smartSpeed: 900,
                        navText: ["<span class='c-blanco ion-arrow-left-c'></span>", "<span class='c-blanco ion-arrow-right-c'></span>"]
                    });
                }
                else {
                    owl.owlCarousel({
                        items: 1,
                        loop: true,
                        nav: true,
                        autoplay: false,
                        smartSpeed: 900,
                        navText: [""]
                    });
                }
            },
            1200
        );



    };

    var load_modal = function () {
        $('.cnt-img-carousel').off('click').on('click', function () {
            var url = $('#modalCommunications').data('url');
            url += '/' + $(this).data('id');
            var request = PeticionAjax.get(url);
            request.done(function (data) {
                $('#modalCommunications_view').html(data);
            });
        })
    };

    var resize_carousel = function () {
        $('.abrir-menu-principal').off('click').on('click', function () {
            setTimeout(function () {
                $('.owl-carousel').trigger('refresh.owl.carousel');
            }, 750);
        });
    };

    var show_types = function () {
        $('.btn-communication-type').off('click').on('click', function () {
            owl_top.trigger('to.owl.carousel', 0);
            owl_top.trigger('stop.owl.autoplay');
            var type = $(this).data('type');
            if ($(this).hasClass('tres')) {
                $(this).removeClass('tres');
                $('.item').each(function () {
                    if ($(this).attr('data-type') == type) {
                        $(this).parent().fadeOut();
                    }
                });
            } else {
                $(this).addClass('tres');
                $('.item').each(function () {
                    if ($(this).attr('data-type') == type) {
                        $(this).parent().fadeIn();
                    }
                });
            }
            setTimeout(function () {
                check_width();
            }, 500);
        });
    };

    var check_width = function () {
        var width = 0;
        var cnt_width = $('.contenedor-ancho').width();
        owl_top.find('.owl-item').each(function () {
            if ($(this).is(':visible')) {
                width += $(this).width();
            }
        });
        if (width < cnt_width) {
            owl_top.on('dragged.owl.carousel', function () {
                owl_top.trigger('to.owl.carousel', 0);
            });
            owl_top.find('.owl-nav').fadeOut();
        } else {
            owl_top.off('dragged.owl.carousel');
            owl_top.find('.owl-nav').fadeIn();
        }
    };

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
            }
        });
        var huecoMenu = ($(window).width() - 1100) / 2;
        abrirCerrarMenu(huecoMenu);
        $(window).resize(function () {
            huecoMenu = ($(window).width() - 1100) / 2;
            abrirCerrarMenu(huecoMenu);
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
                }
            }
        }
    };

    var showPopUps = function () {
        setTimeout(
            function () {
                if ($('#myModal').data('popup')) {
                    let myModal = new Foundation.Reveal($('#myModal'));
                    myModal.open();
                }
            },
            1000
        );
    };

    return {
        load: function () {
            top_carousel();
            sections_carousel();
            left_carousel();
            right_carousel();
            load_modal();
            showPopUps();
            popup_carousel();
            resize_carousel();
            show_types();
            abrirMenuPrincipal();
        },
        load_width: function () {
            check_width();
        }
    }
})();