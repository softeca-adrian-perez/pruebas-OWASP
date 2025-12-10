$(document).ready(function () {
    Category.load();
});

var Category = (function () {
    var load = function () {

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

    };

    return {
        load: function () {
            load();
        }
    }
})();