$(document).ready(function () {
    LanguagesNetworks.load();
});

var LanguagesNetworks = (function () {
    function formatState (state) {
		if (!state.id) {
			return state.text;
		}
		var baseUrl = $(state.element).parent().data('url_flags');
		return $('<span><img width="20" src="' + baseUrl + '/' + state.id.toLowerCase() + '" /> ' + state.text + '</span>');
	};

	var loadFlags = function () {
		$(".flag_loco-js").select2({
			templateResult: formatState,
			templateSelection: formatState
		});
	}

	var saveLanguage = function () {
		$(document).on('click', '.save_language_network-js', function(e){
			e.preventDefault();
			elementId = $(this).data('element_id');
			data = {};
			data.id = $('.language-id' + elementId + '-js').val();
			data.network_id = $('.language-network_id' + elementId + '-js').val();
            data.name_loco = $('.language-name_loco' + elementId + '-js').val();
            data.code_loco = $('.language-code_loco' + elementId + '-js').val();
			data.flag_loco = $('.language-flag_loco' + elementId + '-js').val();

			var request = PeticionAjax.postJSON($(this).data('url'), data);
			request.done(function (response) {
				if (response.success == 'true') {
					location.reload();
				} else {
					swal({
						title: response.error_text,
						type: "error",
					});
				}
			});
		});
	}

	var languageLoadAction = function () {
		$(document).on('click', '.language_web_network_action-js', function(e){
			e.preventDefault();
            var element = $(this);
            var url = element.data('url');
            var url_redirect = element.data('url_redirect');
            var confirmmsg = element.data('confirmmsg');
            var msg_correct = element.data('msg_correct');
            var msg_bad = element.data('msg_bad');
            swal({
                title: confirmmsg,
                type: "info",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.postJSON(url);
                    request.done(function (data) {
                        if(data.precess == 'true'){
                            swal({ type: "success", title: msg_correct }).then(function () {
                                window.location.replace(url_redirect);
                            })
                        }else{
                            swal({ type: "error", title: msg_bad, text: data.error_text })
                        }
                    });
                }
            });
		});
	}

    return {
		load: function () {
			loadFlags();
			saveLanguage();
			languageLoadAction();
		},
	};
})();
