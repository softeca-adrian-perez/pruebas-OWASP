const sizeFilesUpload = window.sizeFilesUpload = 4000000;

$(document).ready(function () {
	tinymceFunctionCall.load();
});

var tinymceFunctionCall = (function () {

    var tinymceFunction = function () {
		if (typeof (tinymce) !== 'undefined') {
            tinymce.init({
                selector: 'textarea.description_tinymce-js',
                promotion: false,
                menubar: false,
                image_generaltab: false,
                media_poster: false,
                media_alt_source: false,
                verify_html: true,
                link_assume_external_targets: 'https',
                link_default_protocol: 'https',
                images_upload_url: '/images_upload/upload/' + $('.description_tinymce-js').data('type'),
                relative_urls: false,
                remove_script_host: false,
                plugins: 'code table lists link image media',
                images_file_types: 'jpg,jpeg,png',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | link table image media | code',
                table_default_styles: {},
                table_default_attributes: { class: 'table-tinyeditor' },
                images_upload_handler: (blobInfo) =>
                    new Promise((resolve, reject) => {
                        const xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', '/images_upload/upload/'+ $('.description_tinymce-js').data('type'));

                        xhr.onload = () => {
                            const json = JSON.parse(xhr.responseText);
                            resolve(json.location);
                        };

                        let image_size = blobInfo.blob().size; // image size in bytes
                        if (image_size > sizeFilesUpload) {
                            reject({ message: 'HTTP Error: ' + $('.description_tinymce-js').data('message_file_size'), remove: true });
                            return;
                        }

                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        xhr.send(formData);
                    }).catch(function (error) {
                        throw new Error($('.description_tinymce-js').data('general_error') + '. ' + error);
                    }),
                setup: function (editor) {
                    editor.on('ExecCommand', (event) => {
                        const command = event.command;
                        if (command === 'mceMedia') {
                            const tabElems = document.querySelectorAll('div[role="tablist"] .tox-tab');
                            tabElems.forEach((tabElem, index) => {
                                let isLastElement = index == tabElems.length - 1;
                                if (isLastElement) {
                                    tabElem.click();
                                } else {
                                    tabElem.remove();
                                }
                            });
                        }
                        if (command === 'mceLink') {
                            setTimeout(function () {
                                const tabElem = document.querySelector('button.tox-browse-url');
                                if (tabElem !== undefined && tabElem !== null) {
                                    tabElem.remove();
                                }
                            });
                        }
                    });
                },
            });
        }
	};

	return {
		load: function () {
			tinymceFunction();
		},
	};
})();
