$(document).ready(function () {
	LoopNetworks.load();
});

let LoopNetworks = (function () {
    let conditions_toggles = function () {
		$('.all-options-js').on('click', function (e) {
			let checkbox = $(this);
			swal({
				title: $('#config_loop-js').data('confirmmsg'),
				type: $('#config_loop-js').data('type'),
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $('#config_loop-js').data('yes'),
				cancelButtonText: $('#config_loop-js').data('no')
			}).then(function(result){
				if(result.value) {
					let url = new URL(window.location.href);
					let type = 'loop';
					url.searchParams.append(type, !checkbox.hasClass('ion-toggle-filled'));
					window.location.href = url;
				}
			})
		});
        $('.ico-toggle-loop-js').on('click', function (e) {
			PeticionAjax.mostrarCargando();
            let element = $(this);
			let url = $('#config_loop-js').data('url');
			let data = {};
			data['garage_network_id'] = element.data('garage_id');
			data['value'] = element.hasClass('ion-toggle');

			let request = PeticionAjax.post(url, data);
			request.done(function (result) {
				if (result !== 'false') {
                    change_toggle(element);
					PeticionAjax.ocultarCargando();
					swal($.i18n._('Constants.Message_well_saved'), '', 'success');
				} else {
					swal($.i18n._('Constants.Error_alert_general'), '', 'error');
				}
				setTimeout(function () {}, 500);
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
