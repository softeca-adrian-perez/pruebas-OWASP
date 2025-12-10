$(document).ready(function () {
	SubmitBooking.load();
});

var SubmitBooking = (function () {

	var submitHiddenInput = function () {
		$(".submit-btn-trigger").on("click", function () {
			if ($(".garage_filter").prop("disabled", true)) {
				$(".garage_filter").prop("disabled", false);
			}
		});
	};

	return {
		load: function () {
			submitHiddenInput();
		},
	};
})();
