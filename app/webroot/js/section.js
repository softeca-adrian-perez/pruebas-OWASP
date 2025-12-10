$(document).ready(function(){
    Section.load();
});

var Section = function() {

    var loadSection = function () {
        $('.select-section-js').change(function (event) {
            if($(this).val()){
                window.location.href = $(this).data('url')+'/'+$(this).val();
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
                    $('.cnt-page-communications').fadeOut( "slow", function() {
                        $('.cnt-page-communications').html(data);
                        $('.preview-name').html(name);
                        $('.cnt-page-communications').fadeIn();
                        $('.cnt-preview-name').fadeIn();
                        Carousel.load();
                        Carousel.load();
                    });
                });
            });

            $('.trading_groups').find('.img_preview').on('click',function(){
                $('.preview_icon').tooltipster('hide');
                $('.remove_preview_icon').removeClass('d-none');
                var name = $(this).attr('title');
                var data = {};
                var trading_group_id = $(this).data('trading-group-id')
                data.network_id = null;
                data.trading_group_id = trading_group_id;
                PeticionAjax.post(url,data).done(function(data){
                    $('.cnt-page-communications').fadeOut( "slow", function() {
                        $('.cnt-page-communications').html(data);
                        $('.preview-name').html(name);
                        $('.cnt-page-communications').fadeIn();
                        $('.cnt-preview-name').fadeIn();
                        Carousel.load();
                        $('#css_id').attr('href', '/css/styles_trd_' + trading_group_id + '_' + 'en' + '.css');
                        Carousel.load();
                    });
                });
            });

            $('.remove_preview_icon').on('click',function(){
                $(this).addClass('d-none');
                var data = {};
                data.network_id = null;
                data.trading_group_id = null;
                PeticionAjax.post(url,data).done(function(data){
                    $('.cnt-page-communications').fadeOut( "slow", function() {
                        $('.cnt-page-communications').html(data);
                        $('.cnt-page-communications').fadeIn();
                        $('.cnt-preview-name').fadeOut();
                        Carousel.load();
                        $('#css_id').attr('href', actual_css);
                        Carousel.load();
                    });
                });
            });
        }
    };

    return {
        load: function(){
            loadSection();
            tooltipPreview();
        }
    }
}();
