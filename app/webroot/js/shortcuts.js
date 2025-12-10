$(document).ready(function () {
    Shorcut.load();
});

var Shorcut = (function () {
    var shortcuts = function(){
        $('.cnt-favorite-shortcut').off('click').on('click',function(){
            var my_id = $(this).data('id');
            var request = PeticionAjax.get('/shortcuts/ajax_view/' + my_id);
            request.done(function(data){
                $('#myModalSSO_view').html(data);
                var link_modal = $('#fav_shortcut_' + my_id);
                link_modal.trigger('click');
            });
        });
        $('.cnt-tech-shortcut').off('click').on('click',function(){
            var link_modal = $('#shortcut_' + $(this).data('id'));
            link_modal.trigger('click');
        });
        $('.cnt-tech-shortcut-single').off('click').on('click',function(){
            var link_modal = $('#shortcut_' + $(this).data('id'));
            link_modal.trigger('click');
        });
        $('.cnt-merch-shortcut').off('click').on('click',function(){
            var link_modal = $('#shortcut_' + $(this).data('id'));
            link_modal.trigger('click');
        });
        $('.cnt-merch-shortcut-single').off('click').on('click',function(){
            var link_modal = $('#shortcut_' + $(this).data('id'));
            link_modal.trigger('click');
        });
        $('.cnt-direct-shortcut').off('click').on('click',function(){
            var link_modal = $('#shortcut_' + $(this).data('id'));
            link_modal.trigger('click');
        });
        $('.cnt-direct-shortcut-single').off('click').on('click',function(){
            var link_modal = $('#shortcut_' + $(this).data('id'));
            link_modal.trigger('click');
        });

        $("#myModalSSO").unbind('opened').bind('opened', function() {
            load_shortcuts();
            remove_shortcut();
            shortcuts();
            $( document ).ajaxStop(function() {
                load_shortcuts();
                remove_shortcut();
                shortcuts();
            });
        });
    };

    var load_shortcuts = function () {
        $('.shortcut-favorite').off('click').on('click', function () {
            var span = $(this).find('span');
            if (span.hasClass('ion-ios-star-outline') && Number($('#favorites_number').val()) >= 4) {
                swal({
                    title: $.i18n._('Shortcut.Favourites_limit'),
                    text: $.i18n._('Shortcut.Favourites_limit_4'),
                    type: 'error'
                })
            } else {
                $('#shortcut_modal_' + $(this).data('shortcut')).trigger('click');
                var data = {};
                var url = $(this).data('url');
                if (span.hasClass('ion-ios-star-outline')) {
                    data.id = $(this).data('shortcut');
                    data.fav = 1;
                    var request = PeticionAjax.post(url,data);
                    request.done(function(data){
                        $('#cnt-favorites').html(data);
                    });
                    span.removeClass('ion-ios-star-outline');
                    span.addClass('ion-ios-star');
                } else if (span.hasClass('ion-ios-star')) {
                    data.id = $(this).data('shortcut');
                    data.fav = 0;
                    var request = PeticionAjax.post(url,data);
                    request.done(function(data){
                        $('#cnt-favorites').html(data);
                    });
                    span.removeClass('ion-ios-star');
                    span.addClass('ion-ios-star-outline');
                }
            }
        });
    };

    var check_shortcuts = function () {

        if(!$('#is_sso').is(":checked")){
            $('#parameter_name_2').val('').attr('disabled', true);
            $('#parameter_name_1').val('').attr('disabled', true);
        }

        $('#is_sso').on('change', function () {
            if(!$('#is_sso').is(":checked")){
                $('#is_sso').prop( "checked" );
                $('#parameter_name_1').val('').attr('disabled', true);
                $('#parameter_name_2').val('').attr('disabled', true);
            }else{
                $('#is_sso').prop( "unchecked" );
                $('#parameter_name_1').attr('disabled', false);
                $('#parameter_name_2').attr('disabled', false);
            }
        });
    };

    var remove_shortcut = function(){
        $('#remove_shortcut').off('click').on('click',function(){
            swal({
                title: $.i18n._('General.Delete_keys'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function (result) {

                if (result.value) {
                    var url = '/Shortcuts/remove_shortcut/' + $('#remove_shortcut').data('id');
                    PeticionAjax.post(url,null).done(function(data){
                            if(data != ""){
                                $('#myModalSSO_view').html(data);
                                $('#ShortcutForm').attr('action','/shortcuts/ajax_view/' + $('#ShortcutForm').data('id'));
                                Shorcut.load();
                                var request = PeticionAjax.post('/shortcuts/ajax_load_favorite');
                                request.done(function(data){
                                    $('#cnt-favorites').html(data);
                                    load_shortcuts();
                                    shortcuts();
                                });
                            }
                        })
                        .fail(function( jqXHR, textStatus ) {
                            swal({
                                title: $.i18n._('General.Error'),
                                text: textStatus,
                                type: 'error'
                            });
                        });
                }
            });
        });
    };

    var crop = function () {
        var $image_crop = $('#image-to-crop');
        var mime_type = null;
        $('#image-input').change(function () {
            if (this.files.length > 0) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $image_crop.cropper('destroy');
                    $image_crop.cropper({
                        aspectRatio: 1,
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

    var delete_shortcut = function(){
        $('#delete-shortcut').on('click',function(e){
            e.preventDefault();
            var url = $(this).data('url');
            if($(this).data('delete')){
                swal({
                    title: $.i18n._('Shortcut.Delete_shortcut?'),
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: $.i18n._('Alert.Confirm_delete'),
                    cancelButtonText: $.i18n._('General.No')
                }).then(function (result) {
                    if (result.value) {
                        window.location = url;
                    }
                });
            } else {
                swal($.i18n._('General.Error'), $.i18n._('Shortcut.Cant_delete'),'error');
            }
        });
    };

    var tooltipPreview = function () {
        var actual_css = $('#css_id').attr('href');
        if($('.preview_icon').length > 0){
            $('.preview_icon').tooltipster({
                theme: 'tooltipster-noir',
                trigger: 'click',
                interactive: true,
                functionReady: function(instance, helper){
                    $('.networks').find('img').sort(function (a, b) {
                        return $(a).height() < $(b).height() ? 1 : -1;
                    }).appendTo('.networks');
                    $('.trading_groups').find('img').sort(function (a, b) {
                        return $(a).height() < $(b).height() ? 1 : -1;
                    }).appendTo('.trading_groups');
                },
            });
            $('.preview_icon').attr('title', $.i18n._('Suppliers.Preview_as'));

            var url = $('.preview_icon').data('url');

            $('.networks').find('.img_preview').on('click',function(){
                $('.preview_icon').tooltipster('hide');
                $('.remove_preview_icon').removeClass('d-none');
                var name = $(this).attr('title');
                var data = {};
                var network_id = $(this).data('network-id');
                data.network_id = network_id;
                data.trading_group_id = null;
                PeticionAjax.post(url,data).done(function(data){
                    $('.cnt-page-software').fadeOut( "slow", function() {
                        $('.cnt-page-software').html(data);
                        $('.preview-name').html(name);
                        $('.cnt-page-software').fadeIn();
                        $('.cnt-preview-name').fadeIn();
                    });
                });
            });

            $('.trading_groups').find('.img_preview').on('click',function(){
                $('.preview_icon').tooltipster('hide');
                $('.remove_preview_icon').removeClass('d-none');
                var name = $(this).attr('title');
                var data = {};
                var trading_group_id = $(this).data('trading-group-id');
                data.network_id = null;
                data.trading_group_id = trading_group_id;
                PeticionAjax.post(url,data).done(function(data){
                    $('.cnt-page-software').fadeOut( "slow", function() {
                        $('.cnt-page-software').html(data);
                        $('.preview-name').html(name);
                        $('.cnt-page-software').fadeIn();
                        $('.cnt-preview-name').fadeIn();
                        $('#css_id').attr('href', '/css/styles_trd_' + trading_group_id + '_' + 'en' + '.css');
                    });
                });
            });

            $('.remove_preview_icon').on('click',function(){
                // $(this).addClass('d-none');
                // var data = {};
                // data.network_id = null;
                // data.trading_group_id = null;
                // PeticionAjax.post(url,data).done(function(data){
                //     $('.cnt-page-software').fadeOut( "slow", function() {
                //         $('.cnt-page-software').html(data);
                //         $('.cnt-page-software').fadeIn();
                //         $('.cnt-preview-name').fadeOut();
                //         shortcuts();
                //         check_shortcuts();
                //         delete_shortcut();
                //         load_shortcuts();
                //         remove_shortcut();
                //         $('#css_id').attr('href', actual_css);
                //     });
                // });

                location.reload();
            });
        }
    };

    return {
        load: function () {
            shortcuts();
            check_shortcuts();
            delete_shortcut();
            crop();
            tooltipPreview();
        },
        load_shortcuts: function () {
            load_shortcuts();
        },
        remove: function () {
            remove_shortcut();
        }
    }
})();

function SaveShortcut() {
    var data = $('#ShortcutForm').serialize();
    var url = $('#ShortcutForm').attr('action');
    PeticionAjax.post(url, data).done(function(data){
            if(data != ""){
                $('#myModalSSO_view').html(data);
                Shorcut.remove();
                Shorcut.load();
            }
        })
        .fail(function( jqXHR, textStatus ) {
            swal({
                title: $.i18n._('General.Error'),
                text: textStatus,
                type: 'error'
            });
        });
}