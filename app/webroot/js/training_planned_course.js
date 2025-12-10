$(document).ready(function () {
	TrainingsPlannedCourses.load();
	$('#training_course_id').click();
});

var TrainingsPlannedCourses = (function () {
	var valueTrainingCourse = function () {
		$("#training_course_id").on("select2:select click", function () {
			let url = $(this).data("url");
			let data = { id: $("#training_course_id").val() };
			if ($(this).val()) {
				var request = PeticionAjax.post(url, data);
				request.done(function (result) {
					if (result) {
						let name_course_type = null;
						resultado = JSON.parse(result);
						$.each(resultado, function (index, value) {
							info = {
								name_en: value.CourseType.name_en,
								name_fr: value.CourseType.name_fr,
								name_de: value.CourseType.name_de,
								training_provider: value.TrainingProvider.name,
							};
						});
						if (info.name_en) {
							name_course_type = info.name_en;
						} else if (info.name_fr) {
							name_course_type = info.name_fr;
						} else {
							name_course_type = info.name_de;
						}
						$("#course_type_id").val(name_course_type);
						$("#training_provider_id").val(info.training_provider);
						$('#training_provider_id').trigger('change');
					}
				});
			}
		});
	};

	var valueTrainingProvider = function () {
		$('#training_provider_id').change(function () {
			let data = { name_provider: $('#training_provider_id').val() };
			let url = $(this).data("url");
			let trainer = $(this).data("trainer");
			if ($(this).val()) {
				var request = PeticionAjax.post(url, data);
				request.done(function (result) {
					if (result) {
						var trainers = JSON.parse(result);
						var options = '';
						$.each(trainers, function (trainerId, trainerName) {
							options += '<option value="' + trainerId + '">' + trainerName + '</option>';
						});
						$('#training_trainer_id').html(options);
						$('#training_trainer_id').val(trainer);
						$('#training_trainer_id').trigger('change');
					}
				});
			}
		});
	};

	var valueVenue = function () {
		$("#venue_id").on("select2:select click", function () {
			let url = $(this).data("url");
			let data = { id: $("#venue_id").val() };
			if ($(this).val()) {
				var request = PeticionAjax.post(url, data);
				request.done(function (result) {
					if (result) {
						value = JSON.parse(result);
						info = {
							address_1: value.Venue.address_1,
							town: value.Venue.town,
							post_code: value.Venue.post_code,
						};
						$("#venue_address").val(info.address_1);
						$("#venue_town").val(info.town);
						$("#venue_post_code").val(info.post_code);
					}
				});
			}
		});
	};

	var toggleStatusCancel = function () {
		$(".ico-toggle-cancel").on("click", function () {
			let url = $(this).data("url");
			let item = $(this);
			let planned_course = $(this).data("planned-course");
			let alertMessage = $.i18n._("Training.Status_toggle_cancel?");
			swal({
				title: alertMessage,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $.i18n._("General.Yes"),
				cancelButtonText: $.i18n._("General.No"),
			}).then(function (result) {
				if (result.value) {
					refundAllCredits(item);
					var request = PeticionAjax.post(url);
					request.done(function (result) {
						if (result) {
							toggleStatusIconCancel(item, planned_course);
						} else {
							swal({
								title: $.i18n._("Constants.Error_alert_general"),
								type: "error",
							});
						}
					});
					request.fail(function () {
						swal({
							title: $.i18n._("Constants.Error_alert_general"),
							type: "error",
						});
					});
				}
			});
		});
	};

	var toggleStatusActive = function () {
		$(".ico-toggle-active").on("click", function () {
			let url = $(this).data("url");
			let item = $(this);
			let planned_course = $(this).data("planned-course");
			let alertMessage = $.i18n._("Training.Status_toggle_active?");
			swal({
				title: alertMessage,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $.i18n._("General.Yes"),
				cancelButtonText: $.i18n._("General.No"),
			}).then(function (result) {
				if (result.value) {
					var request = PeticionAjax.post(url);
					request.done(function (result) {
						if (result) {
							toggleStatusIconActive(item, planned_course);
						} else {
							swal({
								title: $.i18n._("Constants.Error_alert_general"),
								type: "error",
							});
						}
					});
					request.fail(function () {
						swal({
							title: $.i18n._("Constants.Error_alert_general"),
							type: "error",
						});
					});
				}
			});
		});
	};

	var toggleStatusInactive = function () {
		$(".ico-toggle-inactive").on("click", function () {
			let url = $(this).data("url");
			let item = $(this);
			let planned_course = $(this).data("planned-course");
			let alertMessage = $.i18n._("Training.Status_toggle_inactive?");
			swal({
				title: alertMessage,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $.i18n._("General.Yes"),
				cancelButtonText: $.i18n._("General.No"),
			}).then(function (result) {
				if (result.value) {
					var request = PeticionAjax.post(url);
					request.done(function (result) {
						if (result) {
							toggleStatusIconInactive(item, planned_course);
						} else {
							swal({
								title: $.i18n._("Constants.Error_alert_general"),
								type: "error",
							});
						}
					});
					request.fail(function () {
						swal({
							title: $.i18n._("Constants.Error_alert_general"),
							type: "error",
						});
					});
				}
			});
		});
	};

	var toggleStatusIconCancel = function (item_id, planned_course) {
		$(item_id).attr("hidden", true);
		$("#cancel_status_on_" + planned_course).attr("hidden", false);
		$("#inactive_status_on_" + planned_course).attr("hidden", true);
		$("#inactive_status_off_" + planned_course).attr("hidden", false);
		$("#active_status_on_" + planned_course).attr("hidden", true);
		$("#active_status_off_" + planned_course).attr("hidden", false);

		$("#inactive_status_off_" + planned_course).addClass("no-click");
		$("#active_status_off_" + planned_course).addClass("no-click");
	};

	var toggleStatusIconActive = function (item_id, planned_course) {
		$(item_id).attr("hidden", true);
		$("#active_status_on_" + planned_course).attr("hidden", false);
		$("#inactive_status_on_" + planned_course).attr("hidden", true);
		$("#inactive_status_off_" + planned_course).attr("hidden", false);
		$("#cancel_status_on_" + planned_course).attr("hidden", true);
		$("#cancel_status_off_" + planned_course).attr("hidden", false);
	};

	var toggleStatusIconInactive = function (item_id, planned_course) {
		$(item_id).attr("hidden", true);
		$("#inactive_status_on_" + planned_course).attr("hidden", false);
		$("#active_status_on_" + planned_course).attr("hidden", true);
		$("#active_status_off_" + planned_course).attr("hidden", false);
		$("#cancel_status_on_" + planned_course).attr("hidden", true);
		$("#cancel_status_off_" + planned_course).attr("hidden", false);
	};

	var questionDelete = function (item_id) {
		swal({
			title: $.i18n._('Training.Proceed_delete_all_delegates?'),
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: primary_color,
			confirmButtonText: $.i18n._('General.Yes'),
			cancelButtonText: $.i18n._('General.No'),
		}).then(function (result) {
			if (result.value == true) {
				deleteAllDelegates(item_id);
			} else if (result.dismiss == 'cancel') {
				refundAllCredits(item_id);
			}
		});
	};

	var deleteAllDelegates = function (item_id) {
		let url_delete_delegate = item_id.data('url-delete');
		let credits_points = {};
		credits_points.delete = true;
		credits_points.Credit_points = false;
		swal({
			type: "warning",
			title: $.i18n._("Training.Delete_delegate?"),
			showCloseButton: true,
			showCancelButton: true,
			focusConfirm: false,
			confirmButtonText: $.i18n._("General.Yes_refund_credit"),
			cancelButtonText: $.i18n._("General.Yes_NOT_refund_credit"),
			allowOutsideClick: true,
			allowEscapeKey: true,
		}).then(function (result) {
			let request = null;
			let modalClose = null;
			if (result.value == true) {
				credits_points.Credit_points = 'true';
				request = PeticionAjax.post(url_delete_delegate, credits_points);
				modalClose = false;
			} else if (result.dismiss == 'cancel') {
				credits_points.Credit_points = 'false';
				request = PeticionAjax.post(url_delete_delegate, credits_points);
				modalClose = false;
			} else if (result.dismiss == 'close' || result.dismiss == 'overlay' || result.dismiss == 'esc') {
				modalClose = true;
			}
			if (!modalClose) {
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
							title: $.i18n._('Constants.Message_bad_deleted'),
							type: "error"
						});
					}
				});
				request.fail(function () {
					swal({
						title: $.i18n._('Constants.Message_bad_deleted'),
						type: "error"
					});
				});
			}
		});
	};

	var refundAllCredits = function (item_id) {
		let url_refund_credits = item_id.data('url-delete');
		let credits_points = {};
		credits_points.delete = false;
		credits_points.Credit_points = false;
		swal({
			type: "warning",
			title: $.i18n._("Training.Refund_credits?"),
			showCloseButton: true,
			showCancelButton: true,
			focusConfirm: false,
			confirmButtonText: $.i18n._("General.Refund_credit?"),
			cancelButtonText: $.i18n._("General.NOT_refund_credit?"),
			allowOutsideClick: true,
			allowEscapeKey: true,
		}).then(function (result) {
			let request = null;
			let modalClose = null;
			if (result.value == true) {
				credits_points.Credit_points = 'true';
				request = PeticionAjax.post(url_refund_credits, credits_points);
				modalClose = false;
			} else if (result.dismiss == 'cancel') {
				credits_points.Credit_points = 'false';
				request = PeticionAjax.post(url_refund_credits, credits_points);
				modalClose = false;
			} else if (result.dismiss == 'close' || result.dismiss == 'overlay' || result.dismiss == 'esc') {
				modalClose = true;
			}
			if (!modalClose) {
				request.done(function (result) {
					if (result) {
						swal({
							title: $.i18n._("Constants.Message_well_refunded"),
							type: "success",
						}).then(function (result) {
							location.reload();
						});
					} else {
						swal({
							title: $.i18n._('Constants.Message_bad_refunded'),
							type: "error"
						});
					}
				});
				request.fail(function () {
					swal({
						title: $.i18n._('Constants.Message_bad_deleted'),
						type: "error"
					});
				});
			}
		});
	};

	return {
		load: function () {
			valueTrainingCourse();
			valueTrainingProvider();
			valueVenue();
			toggleStatusCancel();
			toggleStatusActive();
			toggleStatusInactive();
		},
	};
})();
