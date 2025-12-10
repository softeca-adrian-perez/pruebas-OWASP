$(document).ready(function () {
    Suppliers.load()
});

var Suppliers = (function () {

    var info = function () {
        if ($('.supplierClick').length > 0) {
            $('.supplierClick').click(function () {
                var supplierClick = $(this);
                $('.supplierInfo').slideUp();
                $('div.supplierClick > div.cnt-imageBrand').removeClass('apagado');
                if ($('.' + $(this).attr('id')).css('display') == 'none') {
                    $('.' + $(this).attr('id')).slideDown();
                    // $('div.supplierClick > div.cnt-imageBrand').addClass('apagado');
                    $(this).find('.cnt-imageBrand').removeClass('apagado');
                    setTimeout(function () {
                        $("html, body").animate({ scrollTop: $('.' + supplierClick.attr('id')).offset().top - 92 }, 500);
                    }, 400);
                }
                var supplier_id = $(this).attr('data-id');
                var url_video = $(this).attr('data-video-supplier-' + supplier_id);
                if ($(".video-supplier-" + supplier_id).length > 0) {
                    var supplier_video = $(".video-supplier-" + supplier_id);
                    if (supplier_video.html().length == 0 && url_video != "") {
                        supplier_video.append(
                            '<iframe class="youtube-player" ' +
                            'width="100%" src=' + url_video + ' ' +
                            'frameborder="0" allowfullscreen></iframe>'
                        );
                    }
                }
            });
        }
    }

    var crop = function () {
        var $image_crop = $('#image-to-crop');
        var mime_type = null;
        $('#image-input').on('change', function () {
            var size = 400 / 200;
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
                    $('#close_modal').off('click').on('click', function () {
                        $('#image-input').val('');
                    });

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
    }

    var deleteSupplier = function () {
        $('.delete-supplier-js').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var element = $(this);
            swal({
                title: element.data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function (result) {
                if (result.value) {
                    window.location = element.attr('href');
                }
            });
        })
    }

    var infoProduct = function () {
        $('.brands').off('click').on('click', function () {
            var url = $('#products_url').data('url');
            var data = {};
            data.brand_id = $(this).data('id');
            var request = PeticionAjax.post(url, data);
            request.done(function (data) {
                var json = JSON.parse(data);
                var exit = "";
                if (json.length > 0) {
                    $.each(json, function (index) {
                        if (!json[index]['ProductImage']['file']) {
                            exit += "<div class='productImage' style='display: flex;align-items: center;justify-content: center;'>";
                            exit += json[index]['Product']['name'];
                            exit += "<div class='overlay'><div class='text'>" + json[index]['Product']['name'] + "</div></div>";
                            exit += "</div>";
                        }
                        else {
                            exit += "<div class='productImage'>";
                            exit += "<img src='img/products/" + json[index]['ProductImage']['file'] + "'>";
                            exit += "<div class='overlay'><div class='text'>" + json[index]['Product']['name'] + "</div></div>";
                            exit += "</div>";
                        }
                    });
                    $('#modalTitle').html($.i18n._('Products.Products') + " " + json[0]['Brand']['name']);
                    $('#products').html(exit);
                } else {
                    $('#modalTitle').html($.i18n._('Products.Products'));
                    $('#products').html($.i18n._('Products.No_product'));
                }
            });
            let productsInfo = new Foundation.Reveal($('#productsInfo'));
            productsInfo.open();
        });
    }

    var tooltipPreview = function () {
        var actual_css = $('#css_id').attr('href');
        if ($('.preview_icon').length > 0) {
            $('.preview_icon').tooltipster({
                theme: 'tooltipster-noir',
                trigger: 'click',
                interactive: true,
                functionReady: function (instance, helper) {
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

            $('.networks').find('.img_preview').on('click', function () {
                $('.preview_icon').tooltipster('hide');
                $('.remove_preview_icon').removeClass('d-none');
                var name = $(this).attr('title');
                var data = {};
                var network_id = $(this).data('network-id');
                data.network_id = network_id;
                data.trading_group_id = null;
                PeticionAjax.post(url, data).done(function (data) {
                    $('.cnt-page-suppliers').fadeOut("slow", function () {
                        $('.cnt-page-suppliers').html(data);
                        $('.preview-name').html(name);
                        $('.cnt-page-suppliers').fadeIn();
                        $('.cnt-preview-name').fadeIn();
                        info();
                        infoProduct();
                    });
                });
            });

            $('.trading_groups').find('.img_preview').on('click', function () {
                $('.preview_icon').tooltipster('hide');
                $('.remove_preview_icon').removeClass('d-none');
                var name = $(this).attr('title');
                var data = {};
                var trading_group_id = $(this).data('trading-group-id');
                data.network_id = null;
                data.trading_group_id = trading_group_id;
                PeticionAjax.post(url, data).done(function (data) {
                    $('.cnt-page-suppliers').fadeOut("slow", function () {
                        $('.cnt-page-suppliers').html(data);
                        $('.preview-name').html(name);
                        $('.cnt-page-suppliers').fadeIn();
                        $('.cnt-preview-name').fadeIn();
                        info();
                        $('#css_id').attr('href', '/css/styles_trd_' + trading_group_id + '_' + 'en' + '.css');
                        infoProduct();
                    });
                });
            });

            $('.remove_preview_icon').on('click', function () {
                $(this).addClass('d-none');
                var data = {};
                data.network_id = null;
                data.trading_group_id = null;
                PeticionAjax.post(url, data).done(function (data) {
                    $('.cnt-page-suppliers').fadeOut("slow", function () {
                        $('.cnt-page-suppliers').html(data);
                        $('.cnt-page-suppliers').fadeIn();
                        $('.cnt-preview-name').fadeOut();
                        info();
                        $('#css_id').attr('href', actual_css);
                        infoProduct();
                    });
                });
            });
        }
    }

    function initAutocomplete() {
        let userAagRegionId = $('#autocomplete-address').data('user_aag_region_id');
        const UKIERegionId = 2;
        const BENLLURegionId = 1;
        let restrictions = {
            language: 'en',
        };
        if (userAagRegionId == UKIERegionId) {
            restrictions['componentRestrictions'] = { 'country': ['GB', 'IE'] };
        } else if (userAagRegionId == BENLLURegionId) {
            restrictions['componentRestrictions'] = { 'country': ['BE', 'NL', 'LU'] };
        }
        //Autocomplete object restricted to geocode search
        autocomplete = new google.maps.places.Autocomplete(
            (document.getElementById('autocomplete-address')),
            {
                ...restrictions
            }
        );

        //Listener activates when user searches for an address
        autocomplete.addListener('place_changed', fillInLocationFields);
    }

    function fillInLocationFields() {
        let address2 = '';
        let postcode = '';
        let town = '';
        //Gets place details from autocomplete
        let place = autocomplete.getPlace();
        for (const element of place.address_components) {
            let component = element;
            //Checks component with postal_town type and it's used as town field
            if (component.types == 'postal_town') {
                town = component.short_name;
                $('#autocomplete-town').val(town);
            }
            //Checks component with postal_code type and it's used as postcode field
            if (component.types == 'postal_code') {
                postcode = component.short_name;
                if (postcode.indexOf(' ') >= 0) {
                    let postCodeValid = postcode.split(' ')[0];
                    $('#autocomplete-postcode').val(postCodeValid);
                } else {
                    $('#autocomplete-postcode').val(postcode);
                }
            }
        }
        address2 = place.address_components[1].long_name;
        $('#autocomplete-address2').val(address2);
    }

    return {
        load: function () {
            info();
            crop();
            deleteSupplier();
            infoProduct();
            tooltipPreview();
            initAutocomplete();
        }
    }

})();