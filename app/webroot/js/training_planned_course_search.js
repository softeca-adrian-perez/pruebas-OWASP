$(document).ready(function () {
	TrainingSearch.load();
});

var TrainingSearch = (function () {
	var updateRangeValue = function () {
		$("#input_price_credit").on("input", function () {
			$("#rangeValue").text($(this).val());
		});
	};

	var updateValueSpan = function () {
		$("#rangeValue").text($("#input_price_credit").val());
	};

	var updateRangeValue2 = function () {
		$("#input_cost_training").on("input", function () {
			$("#rangeValue2").text($(this).val());
		});
	};

	var updateValueSpan2 = function () {
		$("#rangeValue2").text($("#input_cost_training").val());
	};

	return {
		load: function () {
			updateRangeValue();
			updateValueSpan();
			updateRangeValue2();
			updateValueSpan2();
		},
	};
})();
