$(document).ready(function() {
    Gallery.load();
});

var Gallery = (function() {

    var loadDeleteFilesBehaviour = function() {
        $(".delete-file-js").click(function(event) {
            event.preventDefault();
            var element = $(this);
            swal({
                title: element.data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: element.data('yes'),
                cancelButtonText: element.data('no')
            }).then(function (result) {
                if (result.value) {
                    data = {};
                    data.id = element.data('id');
                    id = element.data('id');
                    var div = element.data('div');
                    var request = PeticionAjax.post(element.data('url'), data);
                    request.done(function(data) {
                        $("#file_attachment_" + id).remove();
                        removeActiveImage();
                        location.reload()
                    });
                }
            });
        });
    };

    var makePrincipal = function() {
        $(".principal-file-js").click(function(event) {
            event.preventDefault();
            var element = $(this);
            swal({
                title: element.data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: element.data('yes'),
                cancelButtonText: element.data('no')
            }).then(function (result) {
                if (result.value) {
                    data = {};
                    data.id = element.data('id');
                    var position_child = element.data('child');
                    var div = element.data('div');
                    var request = PeticionAjax.post(element.data('url'), data);
                    request.done(function(data) {
                        $(div).replaceWith(data);
                        makePrincipal();
                        loadDeleteFilesBehaviour();
                        setTimeout(function(){
                            $(document).foundation();
                            $.fx.off = true;
                            $(".orbit-container ol li:nth-child("+position_child+")").trigger('click');
                            //$('.orbit-slides-container img').load(function() {
                                //var img_height = $('.orbit-slides-container img').height();
                                //$('.orbit-slides-container').height(img_height);
                            //});
                            $.fx.off = false;
                        }, 0);
                    });
                }
            });
        });
    };

    var reloadAllDIV = function() {
        $('#col-id-img').removeAttr('style');
        $('.orbit-slide-number').html('<span>0</span> of <span>0</span>');
        $('.orbit-timer').remove();
        $('.orbit-next').remove();
        $('.orbit-prev').remove();
    };

    var clickOrbit = function() {
        $(this).removeAttr('style');
        $('.orbit-prev').click();
    };

    var clickOrbitOne = function() {
        $('.garages_images_carousel').removeAttr('style');
        $('.orbit-prev').click();
    };
    
    var removeActiveImage = function() {
        var listElement = $('li.active');
        listElement.remove();
        if ($('.garages_images_carousel').length < 2 ) {
            if ($('.garages_images_carousel').length == 0 ) {
                reloadAllDIV();
            }else{
                clickOrbitOne();
            }
        }else{
            clickOrbit();
        }
    };

    return {
        load: function() {
            loadDeleteFilesBehaviour();
            makePrincipal();
        }
    }

})();