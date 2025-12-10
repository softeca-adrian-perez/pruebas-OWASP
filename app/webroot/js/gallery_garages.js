$(document).ready(function () {
    GalleryGarage2.load()
});

var GalleryGarage2 = (function () {
    var crop = function () {
        var $image_crop = $('#image-to-crop');
        var mime_type = null;
        $('#image-input').on('change', function () {
            if (this.files.length > 0) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $image_crop.cropper('destroy');
                    $image_crop.cropper({
                        dragCrop: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        minContainerHeight: 600,
                        responsive: true,
                        restore: true,
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

                $('input[name="file-content[]"]').first().val(image_data);
                $('#cropImageModal').foundation('close');

                if ($('#image-input') !== undefined && $('#image-input').data('images_upload') !== undefined) {
                    $('#image-input').data('images_upload', $('#image-input').data('images_upload') + 1);
                }
            } else {
                $('input[name="file-content[]"]').first().val(false);
            }
        });
    };

    var cargarDragAndDrop2 = function () {
        $('.dragdrop-js.dragdrop-v2-js').each(function () {
            $(this).niceFileInput();
            var fileWrapperParent = $(this).parents('.fileWrapper:not(.fileWrapperList)');
            if ($(this).hasClass('dragdrop-multiple-js')) {
                $(this).next().hide();
                fileWrapperParent.addClass('fileWrapperMultiple');
            }

            $(this).change(function () {
                if ($(this).data('max_images') !== undefined && $(this).data('images_upload') !== undefined && $(this).data('images_upload') >= $(this).data('max_images')) {
                    swal({
                        title: $(this).data('max_images_message'),
                        type: "info",
                    }).then(function (result) {
                        $('#cropImageModal').foundation('close');
                    });
                } else {
                    fileWrapperParent = $(this).parents('.fileWrapper:not(.fileWrapperList)');
                    var fileWrapperDragDropDeleteFile = '<span class="dragdrop-delete-file-js"><span class="icon-delete"></span></span>';
                    if ($(this).hasClass('dragdrop-multiple-js')) {
                        var fileWrapperClone = fileWrapperParent.clone(true, true);
                        var fileWrapperParentInputText = fileWrapperParent.find('.fileInputText');
                        fileWrapperParentInputText.show();
                        fileWrapperParentInputText.after(fileWrapperDragDropDeleteFile);
                        var fileWrapperDragDropFileContent = "<input type='hidden' name='file-content[]'>";
                        fileWrapperParentInputText.after(fileWrapperDragDropFileContent);
                        fileWrapperParent.addClass('fileWrapperList');
                        $(this).parent().before(fileWrapperClone);
                        fileWrapperClone.children('input[type="file"]').val('');
                        fileWrapperClone.children('.fileInputText').val('');
                    } else {
                        if (!fileWrapperParent.find('.dragdrop-delete-file-js').html()) {
                            fileWrapperParent.find('.fileInputText').after(fileWrapperDragDropDeleteFile);
                        }
                    }
                    FormHelper.cargarEliminarDragAndDrop();
                }
            });
        });
    };

    return {
        load: function () {
            cargarDragAndDrop2();
            crop();
        }
    }
})();
