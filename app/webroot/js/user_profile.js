$(document).ready(function(){
    UserProfile.load();
});

var UserProfile = (function () {

    var loadCropper = function () {
        var isIE11 = !!navigator.userAgent.match(/Trident.*rv\:11\./);
        if(isIE11){
            $('#image').on('click',function(){
                $(this).closest('label').trigger('click');
            })
        }
        var mime_type = null;
        if($('#image').attr('src') == '/img/upload_pic.png'){
            $('.btn-delete-user-image-js').hide();
        }

        $('.btn-delete-user-image-js').on('click', function (e) {
            $('#image').attr('src', '/img/upload_pic.png');
            $('#new-image-input').val('delete_image');
            $('.btn-delete-user-image-js').hide();
        });

        $('#image-change').change(function () {
            if (this.files.length > 0) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#image-to-crop').cropper('destroy');
                    $('#image-to-crop').cropper({
                        aspectRatio: 1,
                        dragCrop: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        minContainerHeight: 600,
                        responsive: true,
                        restore: true
                    });
                    $('#image-to-crop').cropper('replace', e.target.result);
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
            var image = $('#image-to-crop').cropper('getCroppedCanvas', { fillColor: '#FFF' });
            var image_data = image.toDataURL(mime_type, 1);

            $('#image').attr('src', image_data);
            $('#new-image-input').val(image_data);
            $('.btn-delete-user-image-js').show();

            $('.btn-delete-user-image-js').on('click', function (e) {
                $('#image').attr('src', '/img/upload_pic.png');
                $('#new-image-input').val('delete_image');
                $('.btn-delete-user-image-js').hide();
            });

            $('#cropImageModal').foundation('close');
        });

        $('#cropImageModal').on('closed.zf.reveal', function(){
            var originalFiles = $('#image-change').prop('files');
            var destinationFileInput = $('#image-input')[0];

            var newDataTransfer = new DataTransfer();
            $.each(originalFiles, function(index, file){
                newDataTransfer.items.add(file);
            });

            destinationFileInput.files = newDataTransfer.files;

            $('#image-change').val('');
        });

        $(window).resize(function () {
            var img_w = $('#image').width();
            $('#image').height(img_w)
        }).resize();
    };

    var sendData = function() {
        $("#btn-guardar").on('click',function(e){
            e.preventDefault();
            $('#region-select').attr('disabled', false);
            $('#form').submit();
        });

    }

    return {
        load: function () {
            loadCropper();
            sendData();
        }
    }
})();
