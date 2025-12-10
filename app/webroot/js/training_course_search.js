$(document).ready(function () {
	TrainingSearch.load();
});

let TrainingSearch = (function () {
	let updateRangeValue = function () {
		$("#training_course_price_credit").on("input", function () {
			$("#rangeValue").text($(this).val());
		});
		$("#input_price_credit").on("input", function () {
			$("#rangeValue").text($(this).val());
		});
	};

	let updateValueSpan = function () {
		$("#rangeValue").text($("#training_course_price_credit").val());
		$("#rangeValue").text($("#input_price_credit").val());
	};

	let updateRangeValue2 = function () {
		$("#input_cost_training").on("input", function () {
			$("#rangeValue2").text($(this).val());
		});
	};

	let updateValueSpan2 = function () {
		$("#rangeValue2").text($("#input_cost_training").val());
	};

	let searchTime = function () {
		let timer;

		$("#name-delegates").keyup(function (e) {
			clearTimeout(timer);
			timer = setTimeout(function search() {
				searchContract();
			}, 750);
		});
	};

	let searchContract = function () {
		let name_delegates = $('#name-delegates').val() == undefined ? '' : 'Delegate_name=' + $('#name-delegates').val();
		let network_name = $('#network_name').val() == undefined ? '' : 'Network_name=' + $('#network_name').val();
		let garage_name = $('#garage_name').val() == undefined ? '' : 'Garage_name=' + $('#garage_name').val();
		let training_course_name = $('#training_course_name').val() == undefined ? '' : 'TrainingCourse_name=' + $('#training_course_name').val();
		let training_planned_course_date_from = $('#training_planned_course_date_from').val() == undefined ? '' : 'TrainingPlannedCourse_date_from=' + $('#training_planned_course_date_from').val();
		let training_planned_course_date_to = $('#training_planned_course_date_to').val() == undefined ? '' : 'TrainingPlannedCourse_date_to=' + $('#training_planned_course_date_to').val();
		let distributor_account_number = $('#distributor_account_number').val() == undefined ? '' : 'Distributor_account_number=' + $('#distributor_account_number').val();
		let venue_name = $('#venue_name').val() == undefined ? '' : 'Venue_name=' + $('#venue_name').val();
		let training_course_price_credit = $('#training_course_price_credit').val() == undefined ? '' : 'TrainingCourse_price_credit=' + $('#training_course_price_credit').val();
		let training_delegate_credit_taken = $('#training_delegate_credit_taken').val() == undefined ? '' : 'TrainingDelegate_credit_taken=' + $('#training_delegate_credit_taken').val();
		let delegate_invoice_number = $('#delegate_invoice_number').val() == undefined ? '' : 'Delegate_invoice_number=' + $('#delegate_invoice_number').val();
		let delegate_order_number = $('#delegate_order_number').val() == undefined ? '' : 'Delegate_order_number=' + $('#delegate_order_number').val();
		let provider_name = $('#provider_name').val() == undefined ? '' : 'Provider_name=' + $('#provider_name').val();
		let delegate_cancelled = $('#delegate_cancelled').val() == undefined ? '' : 'Delegate_cancelled=' + $('#delegate_cancelled').val();
		let delegate_reason_cancelled_id = $('#delegate_reason_cancelled_id').val() == undefined ? '' : 'Delegate_reason_cancelled_id=' + $('#delegate_reason_cancelled_id').val();

		let url = name_delegates + '&' + network_name + '&' + garage_name + '&' + training_course_name + '&' + training_planned_course_date_from + '&' + training_planned_course_date_to + '&' + distributor_account_number + '&' + venue_name + '&' + training_course_price_credit + '&' + training_delegate_credit_taken + '&' + delegate_invoice_number + '&' + delegate_order_number + '&' + provider_name + '&' + delegate_cancelled + '&' + delegate_reason_cancelled_id;

		let request = PeticionAjax.post($('#name-delegates').data('url') + '?' + url);
		request.done(function (data) {
			$('#ajax_search_home').html(data);
			if ($('#total_paginator').html() != undefined) {
				$('#counter-total').html($('#total_paginator').html());
			} else {
				$('#counter-total').html($('table tbody tr').length);
			}
		});
	};

	return {
		load: function () {
			updateRangeValue();
			updateValueSpan();
			updateRangeValue2();
			updateValueSpan2();
			searchTime();
		},
	};
})();
