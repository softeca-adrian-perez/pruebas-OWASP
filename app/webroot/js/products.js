$(document).ready(function () {
    Products.load()
});

var Products = (function() {

    var crop = function() {
        var $image_crop = $('#image-to-crop');
        var mime_type = null;
        $('#image-input').on('change',function () {
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
                    $('#close_modal').off('click').on('click',function() {
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
            if(image != null){
                var image_data = image.toDataURL(mime_type, 1);

                $('#new-image-input').val(image_data);
                $('#cropImageModal').foundation('close');
            } else {
                $('#new-image-input').val(false);
            }
        });
    };

    var deleteProduct = function() {
        $('.delete-product-js').click(function(e) {
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
    };

    var brandsCheckbox = function () {
        $("#brands").on('click', '.brands_checkbox', function() {
            var id_checked = $(this).data('id');
            var checked = $(this).find('input[data-id=' + id_checked + ']');
            checked.prop('checked', true);
            if(checked.is(":checked")) {
                $(".brands_checkbox").each(function() {
                    var tmp = $(this).find('input[data-id=' + $(this).data('id') + ']');
                    if($(this).data('id') != id_checked) {
                        tmp.prop('checked', false);
                    }
                });
            }
        });
    };

    return {
        load: function () {
            crop();
            deleteProduct();
            brandsCheckbox();
        }
    }

})();
