$(document).ready(function () {

    var gridster;
    start();

    function save_in_bd() {
        var unique = function (origArr) {
            var newArr = [],
                origLen = origArr.length,
                found, x, y;

            for (x = 0; x < origLen; x++) {
                found = undefined;
                for (y = 0; y < newArr.length; y++) {
                    if (origArr[x]['widget_id'] === newArr[y]['widget_id']) {
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    newArr.push(origArr[x]);
                }
            }
            return newArr;
        };
        var data = gridster.serialize();
        data_list = unique(data);
        var data_widget = {
            widget: data_list
        };


        $.post('/panel/widgets/save_ajax', data_widget)
            .done(function (data) {
                if (!data) {
                    swal({
                        title: error_saving,
                        type: "error"
                    });
                }
            });
    }

    function restore_database_values() {
        $.get('/panel/widgets/get_widgets_available_from_db', function (available_widgets) {
            const cleanData = DOMPurify.sanitize(available_widgets);
            $('#available_widgets').html(cleanData);
        });
        $.get('/panel/widgets/get_widgets_from_db', function (widget_ids) {
            const cleanData = DOMPurify.sanitize(widget_ids);
            $('.gridster').html(cleanData);
            start();
            $('.edition-container ').fadeOut();
            $('.gridster ul li > div').trigger('stopRumble');
            $('.delete').fadeOut();
            $('.drag-container').fadeOut();
            $('.gs-resize-handle-both').fadeOut();
            $('.gridster ul li').removeClass('suprimible');
        });

    }

    function activateEdition() {
        $('.gridster ul li > div').jrumble({
            x: 1,
            y: 1,
            rotation: 0,
            speed: 125
        });
        if (!$('.gridster ul li').hasClass('player') && !$('.gridster ul li').hasClass('resizing')) {
            if ($('.gridster ul li').hasClass('suprimible')) {
                $('.gridster ul li > div').trigger('stopRumble');
                $('.delete').fadeOut();
                $('.drag-container').fadeOut();
                $('.gs-resize-handle-both').fadeOut();
                $('.gridster ul li').removeClass('suprimible');
                $('.edition-container ').fadeOut();
                $('.container-cancel-edition').fadeOut();
            }
            else {
                $('.gridster ul li > div').trigger('startRumble');
                $('.delete').fadeIn();
                $('.drag-container').fadeIn();
                $('.gs-resize-handle-both').fadeIn();
                $('.gridster ul li').addClass('suprimible');
                $('.edition-container ').fadeIn();
                $('.container-cancel-edition').fadeIn();
            }
        }
    }

    function start() {
        var itemWidth = ($('.container-widgets').width() / 2) - 20;
        $(function () {
            gridster = $(".gridster > ul").gridster({
                widget_margins: [10, 10],
                widget_base_dimensions: [itemWidth, (itemWidth * .75)],
                min_cols: 1,
                max_cols: 2,
                resize: { enabled: true },
                draggable: { handle: 'div.drag-container' }
            }).data('gridster');

            $('.gridster ul li').bind("contextmenu", function (e) {
                return false;
            });
        });
    }

    $('#available_widgets').on('click', '.create', function (e) {
        $('html, body').animate({ scrollTop: $(document).height() }, 250);
        var widget_id = $(this).attr('id')
        $.get('/panel/widgets/get_data_widget_by_id/' + widget_id, function (content) {
            gridster.add_widget(
                '<li data-widget_id="' + widget_id + '" class="new">' +
                '<div class="individual-container-widget">' +
                '<span class="ion-ios-close delete"></span>' +
                '<div class="title-widget"><div class="text-tit">' + title_widgets[widget_id] + '</div><div class="drag-container"></div></div>' +
                '<div class="f-widget body-widget"><div class="p-1">' +
                content +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>', 1, 1);
            $('.gridster ul li > div').jrumble({
                x: 1,
                y: 1,
                rotation: 0,
                speed: 125
            });
            $('.gridster ul li > div').trigger('startRumble');
            $('.delete').fadeIn();
            $('.drag-container').fadeIn();
            $('.gs-resize-handle-both').fadeIn();
            $('.gridster ul li').addClass('suprimible');
            $('.edition-container ').fadeIn();
        });
        $(this).remove();
    });

    $('.general-container').on('click', '.container-activate-edition', function () {
        activateEdition();
        $('.container-activate-edition').hide();
    });

    $('.general-container').on('click', '.container-cancel-edition, .cancel_widgets', function () {
        restore_database_values();
        activateEdition();
        $('.container-activate-edition').show();
        $('.container-cancel-edition').hide();
    });

    $('.container-widgets').on('click', '.delete', function (e) {
        gridster.remove_widget($('.gridster li').eq($('.gridster li').index($(this).parent().parent())));
        var widget_id = $('.gridster li').eq($('.gridster li').index($(this).parent().parent())).attr('data-widget_id');
        $('#available_widgets').append('<div class="create wd-' + widget_id + '" id="' + widget_id + '">' + title_widgets[widget_id] + '</div>');
    });

    $('.edition-container ').on('click', '.save_widgets', function () {
        $('.gridster ul li > div').trigger('stopRumble');
        $('.delete').fadeOut();
        $('.drag-container').fadeOut();
        $('.gs-resize-handle-both').fadeOut();
        $('.gridster ul li').removeClass('suprimible');
        $('.edition-container ').fadeOut();
        $('.container-activate-edition').fadeIn();
        $('.container-cancel-edition').fadeOut();
        save_in_bd();
    });

    var resizeId;
    $(window).resize(function () {
        clearTimeout(resizeId);
        resizeId = setTimeout(doneResizing, 500);
    });

    function doneResizing() {
        var gridster;
        var itemWidth = ($('.container-widgets').width() / 2) - 20;
        $(function () {
            gridster = $(".gridster > ul").gridster({
                widget_margins: [10, 10],
                widget_base_dimensions: [itemWidth, (itemWidth * .75)],
                min_cols: 1,
                max_cols: 2,
                resize: { enabled: true }
            }).data('gridster');
        });
    }

    var rtime;
    var timeout = false;
    var delta = 200;

    function resizeend() {
        if (new Date() - rtime < delta) {
            setTimeout(resizeend, delta);
        } else {
            timeout = false;
            restore_database_values();
            $('.container-activate-edition').fadeIn();
            $('.container-cancel-edition').fadeOut();
        }
    }
});