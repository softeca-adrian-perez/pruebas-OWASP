$(document).ready(function () {
	Delegates.load();
});

var Delegates = (function () {
	var deleteDelegates = function () {
		$(".delete-delegate").on("click", function () {
			let url = $(this).data("delete-url");
			swal({
				title: $.i18n._("Delegate.Delete_delegate?"),
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $.i18n._("General.Yes"),
				cancelButtonText: $.i18n._("General.No"),
			}).then(function (result) {
				if (result.value) {
					var request = PeticionAjax.post(url);
					request.done(function (result) {
						if(result) {
							swal({
								title: $.i18n._("Constants.Message_well_deleted"),
								type: "success",
							}).then(function (result) {
								location.reload();
							});
						} else {
							swal({
								title: $.i18n._("Constants.Message_bad_deleted"),
								type: "error",
							});
						}
					});
					request.fail(function () {
						swal({
							title: $.i18n._("Constants.Message_bad_deleted"),
							type: "error",
						});
					});
				}
			});
		});
	};

	var reloadHandleSelect = function () {
		$(".cargar_garages, .cargar_distributors").on("select2:select click", function() {
			let checkedClicked = document.querySelector('#div-radio input[type="radio"]:checked');
			let url = $(this).data("url");
			let data_select = {};
			data_select.element_id = $(this).val();
			data_select.type = $(this).attr('id');
			if ($(this).val()) {
				var request = PeticionAjax.post(url, data_select);
				request.done(function(result) {
					$('.email-delegate').val('');
					$('.contact-delegate').val('');
					if (checkedClicked == 'search_garage') {
						let selectedValue = $(this).val();
						$('#garages_id').val(selectedValue);
						$('#distributors_id').val('');
					} else if (checkedClicked.id == 'search_distributor') {
						let selectedValue = $(this).val();
						$('#distributors_id').val(selectedValue);
						$('#garages_id').val('');
					} else if (checkedClicked.id == 'search_supplier') {

					}
					if (result) {
						$('.delegate_class').empty().trigger('change');
						resultado = JSON.parse(result);
						$.each(resultado, function(index, value) {
							info = {
								id: value.Contact.id,
								text: value.Contact.full_name,
								email: value.Contact.email
							};
							var newOption = new Option(info.text, info.id, false, false);
							$(newOption).data('email', info.email);
							$('.delegate_class').append(newOption);
						});
						$('.delegate_class option:first').click();
						$('.delegate_class').trigger('change');
					}
				});
			}
		});
	};

	var checkRadioValue = function () {
		if ($('#garage_selected').prop('checked')) {
			$('#garage_selected').click();
		} else if ($('#distributor_selected').prop('checked')) {
			$('#distributor_selected').click();
		} else if ($('#supplier_selected').prop('checked')) {
			$('#supplier_selected').click();
		}
	};

	var onClickRadio = function () {
		$('#garage_selected').on('click', function() {
			$('#div-garage').attr('hidden', false);
			$('#div-distributor').attr('hidden', true);
		});
		$('#distributor_selected').on('click', function() {
			$('#div-garage').attr('hidden', true);
			$('#div-distributor').attr('hidden', false);
			$('#search_garage').empty();
		});

	};

	var changeContactEmailSelect = function () {
		$(".delegate_class").on("select2:select click", function () {
			let selectedOption = $(this).find(':selected');
			let email = selectedOption.data('email');
			let contact = selectedOption.text();
			$('.email-delegate').val(email);
			$('.contact-delegate').val(contact);
		});
	};

	var submitEnabled = function () {
		$(document).on('submit', 'form', function() {
			$('.email-delegate').prop('disabled', false);
			$('.contact-delegate').prop('disabled', false);
		});
	};

	var selectDistributor = function () {

        $('.select2Dinamico_distributor').select2({
            placeholder: "",
            allowClear: true,
            tags: true
        });

        $('.cargar_distributors').select2({
            ajax: {
               url: '/distributors/get_distributors_name_region',
               delay: 200,
                dataType: 'json',
                data: function (params) {
                    var query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#distributor_name option").remove();
                    return {
                      results: data.items
                    };
                  }
            },
            minimumInputLength: 3,
            placeholder: "Min 3 characters",
        });

    };

	var searchDistributors = function () {
		$('#search_distributor').on('click',function(e){
			PeticionAjax.mostrarCargando();
			var url = $('#search_distributor').data('url');

			data = {};
			data.rsm_id = $('#garage_filter_rsm').val();
			data.bdm_id = $('#garage_filter_bdm').val();
			data.trading_group_id = $('#trading-group-id').val();
			data.customer_activity_id = $('#customer_activity_id').val();

			var request = PeticionAjax.post(
				url,
				data
			);

			request.done(function (data) {
				$('#distributor-id').empty();
				$('#count_distributors').html();
				$('#distributor-id').append('<option value=""></option>');

				datos = JSON.parse(data);
				datos = Object.entries(datos);

				var total = 0;
				$.each(datos, function(i, value) {
					total++;
					$('#distributor-id').append($('<option>').text(value[1]).attr('value', value[0]));
				});

				$('#count_distributors').html('('+total+')');

				ocultarCargando();
			});


		});
	};

	var selectGarage = function () {

        $('.select2Dinamico_garage').select2({
            placeholder: "",
            allowClear: true,
            tags: true
        });

        $('.cargar_garages').select2({
            ajax: {
               url: '/garages/get_garages_name_region',
               delay: 200,
                dataType: 'json',
                data: function (params) {
                    var query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#garage_name option").remove();
                    return {
                      results: data.items
                    };
                  }
            },
            placeholder: "",
            minimumInputLength: 3,
            allowClear: true
        });

    };

	var searchGarages = function () {
		$('#search_garage').on('click',function(e){
			PeticionAjax.mostrarCargando();
			var url = $('#search_garage').data('url');

			data = {};
			data.rsm_id = $('#garage_filter_rsm').val();
			data.bdm_id = $('#garage_filter_bdm').val();
			data.trading_group_id = $('#trading-group-id').val();
			data.customer_activity_id = $('#customer_activity_id').val();

			var request = PeticionAjax.post(
				url,
				data
			);

			request.done(function (data) {
				$('#garage-id').empty();
				$('#count_garages').html();
				$('#garage-id').append('<option value=""></option>');

				datos = JSON.parse(data);
				datos = Object.entries(datos);

				var total = 0;
				$.each(datos, function(i, value) {
					total++;
					$('#garage-id').append($('<option>').text(value[1]).attr('value', value[0]));
				});

				$('#count_garages').html('('+total+')');

				ocultarCargando();
			});


		});
	};

	return {
		load: function () {
			selectDistributor();
			//searchDistributors();
			selectGarage();
			searchGarages();
			deleteDelegates();
			checkRadioValue();
			submitEnabled();
			changeContactEmailSelect();
			onClickRadio();
			reloadHandleSelect();
		},
	};
})();
