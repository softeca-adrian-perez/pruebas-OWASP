$(document).ready(function () {
	QuotingNetworks.load();
});

let QuotingNetworks = (function () {
    let conditions_toggles = function () {
		$('.all-options-js').on('click', function (e) {
			let checkbox = $(this);
			swal({
				title: $('#config_check_networks').data('confirmmsg'),
				type: $('#config_check_networks').data('type'),
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $('#config_check_networks').data('yes'),
				cancelButtonText: $('#config_check_networks').data('no')
			}).then(function(result){
				if(result.value) {
					let url = new URL(window.location.href);
					let tpye = checkbox.hasClass('quoting-js') ? 'is_quoting_active' : 'quoting_views_active';
					url.searchParams.append(tpye, !checkbox.hasClass('ion-toggle-filled'));
					window.location.href = url;
				}
			})
		});
        $('.ico-toggle-quoting-js, .ico-toggle-quoting-views-js').on('click', function (e) {
			PeticionAjax.mostrarCargando();
            let element = $(this);
			let url = $('#config_check_networks').data('url');
			let data = {};
			data['garage_network_id'] = element.data('garage_id');
			data['value'] = element.hasClass('ion-toggle');
			data['type'] = element.hasClass('ico-toggle-quoting-js') ? 'quoting_active' : 'quoting_views_active';

			let request = PeticionAjax.post(url, data);
			request.done(function (result) {
				if (result !== 'false') {
					if (!element.hasClass('ion-toggle')) {
						if (element.hasClass('ico-toggle-quoting-views-js')) {
							change_toggle($('#checkbox_quoting_' + element.data('key')), false)
						}
					}
					change_toggle(element);
					PeticionAjax.ocultarCargando();
					swal($.i18n._('Constants.Message_well_saved'), '', 'success');
				} else {
					swal($.i18n._('Constants.Error_alert_general'), '', 'error');
				}
				setTimeout(function () {
					let url = new URL(window.location.href);
					window.location.href = url;
				}, 500);
			});
        });
    };

    let change_toggle = function (toggle, activate = null) {
        if (toggle.hasClass('ion-toggle-filled') || (activate !== null && !activate)) {
            toggle.removeClass('ion-toggle-filled c-exito').addClass('ion-toggle c-fallo');
        } else if (toggle.hasClass('ion-toggle') || (activate !== null && activate)) {
            toggle.removeClass('ion-toggle c-fallo').addClass('ion-toggle-filled c-exito');
        }
    }

    return {
		load: function () {
			conditions_toggles();
		},
	};
})();
