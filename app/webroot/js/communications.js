$(document).ready(function () {
    if( $('#section_subsection_id').length > 0){
        Communication.loadShowFilters();
    } else {
        Communication.load();
    }
    Communication.loadPopup();
});

var Communication = (function () {
    var load = function () {
        // if($('#section_subsection_value').length){
        //     $('#section_subsection_id').val($('#section_subsection_value').val()).trigger('change');
        // }

        var $image_crop = $('#image-to-crop');
        var mime_type = null;
        $('#image-input').on('change',function () {
            var size = '';
            size = 400 / 200;

            if (this.files.length > 0) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $image_crop.cropper('destroy');
                    $image_crop.cropper({
                        aspectRatio: size,
                        dragCrop: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        minContainerHeight: 600,
                        responsive: true,
                        restore: true
                    });
                    $image_crop.cropper('replace', e.target.result);
                    $('#cropImageModal').foundation('open');
                    $('#close_modal').off('click').on('click',function(){
                        $('#image-input').val('');
                    });

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
            var image_data = image.toDataURL(mime_type, 1);

            $('#new-image-input').val(image_data);
            $('#cropImageModal').foundation('close');
        });

        $('#communication_section').on('change', function () {
           $('#cnt-img').find('.dragdrop-delete-file-js').trigger('click');
        });

        $('#select_communication_section_id').change(function (event) {
            event.preventDefault();

            var url = $(this).data('url');
            var div = $(this).data('div_subsection');

            data = {};
            data.section_id = $(this).val();

            var request = PeticionAjax.post( url, data );
            request.done(function (data) {
                $(div).html(data);
                Select2.load();
                $('#section_subsection_id').prev('label').css({"top":"0px"});
                $('#section_subsection_id').trigger('change');
            });
        });
    };

    var link_section = function(){
        $( ".link-section" ).off('click').on('click',function(){
            $('#search-communications').val('');
            var url = $(this).data('url');
            var butons_tres = $('.link-section');
            var now = $(this);
            butons_tres.each(function() {
                $(this).removeClass('tres');
            });
            if(now.hasClass('tres')) {
                $(this).removeClass('tres');
            }else{
                $(this).addClass('tres');
            }
            var request = PeticionAjax.get(url);
            request.done(function(data){
                $('#ajax_search_communications').html(data);
                cnt_img_carousel();
                cargarComportamientoTableTrLink();
                pagination_ajax();
            });
        });
    };

    var link_section_search_page2 = function(){
        $( ".link-section-search-page2").off('click').on('click',function(){
            var url = $(this).data('url');
            var butons_tres = $('.link-section-search-page2');
            var now = $(this);
            butons_tres.each(function() {
                $(this).removeClass('tres');
            });
            if(now.hasClass('tres')) {
                $(this).removeClass('tres');
            }else{
                $(this).addClass('tres');
            }

            var request = PeticionAjax.get(url);
            request.done(function(data){
                $('#ajax_search_communications_page2').html(data);
                cargarComportamientoTableTrLink();
                cnt_img_carousel();
                pagination_ajax();
                $(document).foundation('reflow');

                $('.table-tracking').basictable('destroy');
                $('.table-tracking').basictable();
            });
        });
    };

    var cnt_img_carousel = function(){
        $('.cnt-img-carousel').off('click').on('click',function(){
            var url = $('#modalCommunications').data('url');
            url += '/' + $(this).data('id');
            var request = PeticionAjax.get(url);
            request.done(function(data){
                $('#modalCommunications_view').html(data);
                let modalCalendar = new Foundation.Reveal($('#modalCommunications'));
                modalCalendar.open();
                cnt_img_carousel();
            });
        });
    };

    var cargarComportamientoTableTrLink = function(){
        $('tr.link-js td').click(function(){
            if(!$(this).hasClass('no-link-js')){
                var communication_title = $(this).parent('tr').data('communication-title');
                var url = 'title=' + communication_title;
                window.location = $(this).parent('tr').data('url') + '?' + url;
            }
        });

    };

    var load_modal = function(){
        cnt_img_carousel();
        link_section();
        link_section_search_page2();
        $(document).foundation('reflow');

        $('.link-js').each(function () {
            if ($(this).data('id') == $('#modal-value').val()) {
                $(this).click();
            }
        });
    };

    var pagination_ajax = function(){
        $('#ajax_search_communications').find('.paginacion a').off('click').on('click', function (event) {
            event.preventDefault();
            var url = $(this).attr('href');
            var request = PeticionAjax.get(url);
            request.done(function(data){
                $('#ajax_search_communications').html(data);
                link_section();
                cargarComportamientoTableTrLink();
                cnt_img_carousel();
                pagination_ajax();
                $(document).foundation('reflow');
                load_modal();
            });
            return false;
        });

        $('#ajax_search_communications_page2').find('.paginacion a').off('click').on('click', function (event) {
            event.preventDefault();
            var url = $(this).attr('href');
            var request = PeticionAjax.get(url);
            request.done(function(data){
                $('#ajax_search_communications_page2').html(data);
                link_section_search_page2();
                cargarComportamientoTableTrLink();
                cnt_img_carousel();
                pagination_ajax();
                $(document).foundation('reflow');
                load_modal();

                $('.table-tracking').basictable('destroy');
                $('.table-tracking').basictable();
            });
            return false;
        });
    };

    var concat_title_to_url = function() {
        var search_communications = $('#ajax_search_communications');
        if(search_communications.data('title') != null){
            $('#search-communications').val(search_communications.data('title'));
        }
        var section_id = search_communications.data('id');
        var title = search_communications.data('title');
        return search_communications.data('url') + '/' + section_id + '?' + 'title=' + title;

    };

    var load_pagination_ajax = function(){
        var url_sections;
        if($('.link-section-search-page2').length == 0){
            url_sections = concat_title_to_url();
        }

        var request_sections = PeticionAjax.post(url_sections);
        request_sections.done(function(data) {
            $('#ajax_search_communications').html(data);
            $(document).foundation('reflow');
            load_modal();
        });

        var param = $('#search-page2').val();
        var url_communications = $('#ajax_search_communications_page2').data('url') + '?' + 'param=' + param;
        var request_communications = PeticionAjax.post(url_communications);
        request_communications.done(function(data) {
            $('#ajax_search_communications_page2').html(data);
            link_section();
            cargarComportamientoTableTrLink();
            cnt_img_carousel();
            pagination_ajax();
            $(document).foundation('reflow');
            load_modal();

            $('.table-tracking').basictable('destroy');
            $('.table-tracking').basictable();
        });
    };

    var searchTime = function(){
        var timer;
        $("#search-communications").keyup(function(e){
            clearTimeout(timer);
            timer = setTimeout(function search(){
                searchCommunication();
            }, 750);
        });
    };
    var searchCommunication = function(){
        var butons_tres = $('.link-section');
        var subsection_id = '';
        butons_tres.each(function() {
            if($(this).hasClass('tres')){
                subsection_id = $(this).data('subsection_id');
            }
        });

        var title_communication = 'title=' + $('#search-communications').val();
        var section_subsection_id = 'section_subsection_id=' + subsection_id;

        var url = title_communication + '&' + section_subsection_id;
        var request = PeticionAjax.get($('#search-communications').data('url') + '?' + url);

        request.done(function(data){
            $('#ajax_search_communications').html(data);
            pagination_ajax();

            $('tr.link-js td').click(function(){
                if(!$(this).hasClass('no-link-js')){
                    window.location = $(this).parent('tr').data('url');
                    cnt_img_carousel();
                }
            });
        });
    };

    var showFilters = function () {
        $('body').on('change', '#section_subsection_id', function () {

            if( $(this).val() ){
                $('#filters_communication').removeClass('d-none');
                var url = $(this).data('url');

                data = {};
                data.subsection_id = $(this).val();
                var request = PeticionAjax.post( url, data );

                request.done(function (data) {
                    $('#filters_communication').html(data);
                });

            }
            else{
                $('#filters_communication').addClass('d-none');
            }

        });
    };

    var togglePopupForm = function() {
        if( $('#is_popup').prop('checked') ){
            $('#cnt_popup').show();
        } else {
            $('#cnt_popup').hide();
        }

        $('#is_popup').on('change',function(){
            $('#cnt_popup').slideToggle();
            if( $(this).prop('checked') ){
                $('#start_date_popup').val('');
                $('#end_date_popup').val('');
            }
        });
    }

    return {
        load: function () {
            load();
            load_modal();
            load_pagination_ajax();
            searchTime();
        },
        loadShowFilters: function() {
            showFilters();
            load();
        },
        loadPopup: function() {
            togglePopupForm();
        }
    }
})();