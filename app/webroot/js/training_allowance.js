$(document).ready(function () {
    TrainingAllowance.load();
});

let TrainingAllowance = (function () {

    let selectedGarageTrigger = function () {
        $("#garage-filter-js").on("select2:select click", function() {
			var url = $(this).data("url");
			var data = {};
        	data.garage_id = $(this).val();
            if ($(this).val()) {
				var request = PeticionAjax.post(url, data);
				request.done(function(result) {
					if (result) {
						let datos = JSON.parse(result);
						$('#allowance-filter-js').empty().append('<option value=""></option>');
						$.each(datos, function(i, value) {
							$('#allowance-filter-js').append($('<option>').text(value['TrainingAllowance']['complete_date']).attr('value', value['TrainingAllowance']['id']));
						});
						$("#garage-filter-js").trigger('change');
					}
				});
			}
		});
    };

	let filterGarageChanged = function () {
        $("#garage-filter-js").on("change", function() {
			if ($("#garage-filter-js").val() === "" || $("#garage-filter-js").val() === null) {
				var url = $(this).data("url-all-allowance");
				var request = PeticionAjax.post(url);
				request.done(function(result) {
					if (result) {
						let datos = JSON.parse(result);
						$('#allowance-filter-js').empty().append('<option value=""></option>');
						$.each(datos, function(i, value) {
							$('#allowance-filter-js').append($('<option>').text(value['TrainingAllowance']['complete_date']).attr('value', value['TrainingAllowance']['id']));
						});
						$("#allowance-filter-js").trigger('change');
					}
				});
			}
		});
    };

    return {
        load: function () {
            selectedGarageTrigger();
			filterGarageChanged();
        },
    };
})();
