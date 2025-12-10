$(document).ready(function () {
	TrainingsProviders.load();
});

var TrainingsProviders = (function () {
	var deleteProvider = function () {
		$(".delete-provider").on("click", function () {
			let url = $(this).data("delete-url");
            let data_id = $(this).data("id-provider");
			swal({
				title: $.i18n._("Training.Training_provider_delete?"),
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $.i18n._("General.Yes"),
				cancelButtonText: $.i18n._("General.No"),
			}).then(function (result) {
				if (result.value) {
					var request = PeticionAjax.post(url, data_id);
					request.done(function (result) {
						if (result == "assoc") {
							let url2 = $(".delete-provider[data-id-provider='" + data_id + "']").data("delete-url-assoc");
							swal({
								title: $.i18n._("Training.Training_provider_assoc_delete?"),
								type: "warning",
								showCancelButton: true,
								confirmButtonColor: primary_color,
								confirmButtonText: $.i18n._("General.Yes"),
								cancelButtonText: $.i18n._("General.No"),
							}).then(function (result) {
								if (result.value) {
									var request = PeticionAjax.post(url2);
									request.done(function (result) {
										if (result) {
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
						} else if (result) {
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

	return {
		load: function () {
			deleteProvider();
		},
	};
})();
