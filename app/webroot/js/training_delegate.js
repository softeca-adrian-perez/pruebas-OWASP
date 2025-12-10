$(document).ready(function () {
	TrainingsDelegates.load();
});

var TrainingsDelegates = (function () {

	$(document).ready(function() {
		let inputValue = $("#is_refund_eligible").val();
		if (inputValue == 1) {
			$('.hidden-input').css("display", "none");
		}
	});

	var reloadHandleSelect = function () {
		$(".update_garages-js").on("select2:select click", function() {
			let network_id_get = $('#network_id_get').val();
			let url = $(this).data("url");
			let data_select = {};
			data_select.element_id = $(this).val();
			data_select.isEdit = $('.networks_select-js').data("is_edit-js");
			if ($(this).val()) {
				var request = PeticionAjax.post(url, data_select);
				request.done(function(result) {
					if (result) {
						$('.network_class').empty().trigger('change');
						resultado = JSON.parse(result);
						$.each(resultado, function(index, value) {
							info = {
								id: index,
								text: value,
							};
							var newOption = new Option(info.text, info.id, false, false);
							$('.network_class').append(newOption);
						});
						if (network_id_get) {
							$('.network_class').val(network_id_get).click();
						}else{
							$('.network_class option:first').click();
						}
						$('.network_class').trigger('change');
					}
				});
			}
		});
	};

	var reloadHandleSelectDelegate = function () {
		$(".network_class").on("select2:select click", function() {
			let contact_id_get = $('#contact_id_get').val();
			let url = $(this).data("url");
			let data_select = {};
			data_select.element_id = $('#garage_id').val();
			if ($('#garage_id').val()) {
				var request = PeticionAjax.post(url, data_select);
				request.done(function(result) {
					if (result) {
						$('.delegate_class').empty().trigger('change');
						resultado = JSON.parse(result);
						$.each(resultado, function(index, value) {
							info = {
								id: value.Contact.id,
								text: value.Contact.full_name,
								position: value.Contact.position_id
							};
							var newOption = new Option(info.text, info.id, false, false);
							$(newOption).data('position', info.position);
							$('.delegate_class').append(newOption);
						});
						if (contact_id_get) {
							$('.delegate_class').val(contact_id_get).click();
							$('.delegate_class').trigger('change');
						}else{
							$('.delegate_class').prop('selectedIndex', -1);
						}
					}
				});
			}
		});
	};

	var updateValueGarage = function () {
		let inputValue = $('.update_garages-js').val();
		$(".update_garages-js option[value='" + inputValue + "']").click();
	};

	var changeContactSelect = function () {
		$(".delegate_class").on("select2:select click", function () {
			let selectedOption = $(this).find(':selected');
			let position = selectedOption.data('position');
			$('.position_id').val(position).trigger('change');
		});
	};

	var selectRefund = function () {
		$(".refund_eligible").on("select2:select click", function () {
			let selectedOption = $(this).val();
			if (selectedOption === '0') {
				$('.hidden-input').css("display", "block");
			} else {
				$('.hidden-input').css("display", "none");
			}
		});
	};


	return {
		load: function () {
			reloadHandleSelect();
			changeContactSelect();
			updateValueGarage();
			reloadHandleSelectDelegate();
			selectRefund();
		},
	};
})();
