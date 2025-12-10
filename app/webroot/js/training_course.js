$(document).ready(function () {
	TrainingsCourses.load();
});

var TrainingsCourses = (function () {

	var submitCheckHiddenInput = function () {
		$("#course-form").on("submit", function () {
			if ($("#pricePounds").prop("disabled", true)) {
				$("#pricePounds").prop("disabled", false);
			}
		});
	};

	var chargeProducts = function () {
		$(".tr-show").on("click", function () {
			var id = $(this).data("identificator");
			if ($("#" + $(this).attr("id") + " div.ampliar-js").hasClass("abierto")) {
				chargeData(id);
			}
			$("#training_table > tbody > tr").each(function () {
				if ($(this).hasClass("training-" + id)) {
					$(this).fadeToggle();
				}
			});
		});
	};

	var chargeData = function (id) {
		const cancelled = 'Cancel';
		const inactive = 'Deactivate';
		const active = 'Activate';
		$("tr.training-" + id).each(function () {
			$(this).remove();
		});
		var url = $("#" + id).data("url");
		var data = $('.buscador').serializeArray();
		var request = PeticionAjax.post(url, data);
		request.done(function (result) {
			result = JSON.parse(result);
			let texto = "";
			let super_admin = "";
			$.each(result, function (index, value) {
				let inactive_status_on = "hidden";
				let inactive_status_off = "";
				let active_status_on = "hidden";
				let active_status_off = "";
				let cancel_status_on = "hidden";
				let cancel_status_off = "";
				let block_cancel = "";
				if (value["status"] == 0) {
					inactive_status_on = "";
					inactive_status_off = "hidden";
				} else if (value["status"] == 1) {
					active_status_on = "";
					active_status_off = "hidden";
				} else {
					cancel_status_on = "";
					cancel_status_off = "hidden";
				}

				if ($('.is_superAdmin-js')[0]) {
					super_admin = 'no-click';
				}

				let text_available = null;
				if (value["status"] != 1) {
					text_available = '<b>' + $.i18n._("Training.Unavailable") + '</b>'
				} else if (value["availability"] == value["count_available"]) {
					text_available = '<b class="color-yellow-text">' + $.i18n._("Training.Completed") + '</b>'
				} else {
					text_available = '<b class="color-blue-text">' + $.i18n._("Training.Available") + '</b>'
				}
				let icon_plus = "hidden";
				if (value["availability"] != value["count_available"] && (value["status"] == 1)) {
					icon_plus = "";
				}
				if (value["duration"] === null) {
					value["duration"] = "";
				}
				if (value["part_number"] === null) {
					value["part_number"] = "";
				}

				texto +=
					'<tr class="training-' +
					id +
					'"> <td colspan="3"> </td> <td data-training-provider="' + value["training_provider"] + '" class="color-blue-text cursor-pointer provider_click" colspan="1"> ' +
					value["training_provider"] +
					' </td> <td class="color-blue-text" colspan="1"> <b class="ws-nowrap">' +
					value["date_from"] +
					'</b> </td> <td class="color-blue-text" colspan="1"> <b class="ws-nowrap">' +
					value["date_to"] +
					'</b> </td> <td class="color-blue-text" colspan="1"> <b>' +
					value["duration"] +
					'</b> </td> <td colspan="1"> <b data-venue="' + value["venue_id"] + '" class="venue_click cursor-pointer">' +
					value["venue_id"] +
					'</b> </td> <td class="ta-center ws-nowrap" data-planned-course="' +
					value["training_course_id"] +
					'" id="' +
					value["training_course_id"] +
					'" > <span class="bt-content">' +
					"<span " + cancel_status_off + " data-planned-course='" + value["id"] + "' id='cancel_status_off_" + value["id"] + "' class='cursor-pointer ico-toggle-cancel ico-toggle-hover " + super_admin + " '> <img src='/img/iconos/cancel_off.svg' " + " title=" + cancelled + "> </span>" +
					"<span " + cancel_status_on + " data-planned-course='" + value["id"] + "' class=' " + super_admin + "'   id='cancel_status_on_" + value["id"] + "'> <img src='/img/iconos/cancel_on.svg' " + " title=" + cancelled + "> </span>" +
					"<span " + inactive_status_off + " data-planned-course='" + value["id"] + "' id='inactive_status_off_" + value["id"] + "' class='cursor-pointer ico-toggle-inactive ico-toggle-hover " + block_cancel + " " + super_admin + " '> <img src='/img/iconos/off_off.svg' " + " title=" + inactive + "> </span>" +
					"<span " + inactive_status_on + " class=' " + super_admin + "' data-planned-course='" + value["id"] + "' id='inactive_status_on_" + value["id"] + "'> <img src='/img/iconos/off_on.svg' " + " title=" + inactive + "> </span>" +
					"<span " + active_status_off + " data-planned-course='" + value["id"] + "' id='active_status_off_" + value["id"] + "' class='cursor-pointer ico-toggle-active ico-toggle-hover " + block_cancel + " " + super_admin + " '> <img src='/img/iconos/check_off.svg' " + " title=" + active + "> </span>" +
					"<span " + active_status_on + " data-planned-course='" + value["id"] + "' id='active_status_on_" + value["id"] + "' class=' " + super_admin + "' > <img src='/img/iconos/tick_on.svg' " + " title=" + active + "> </span>" +
					'</span></td> <td class="ta-center" colspan="1">' +
					'</td> <td class="color-blue-text ta-center" colspan="1"> <b>' +
					value["price_credit"] +
					' <img src="/img/iconos/cr.svg" /></b> </td> <td class="color-blue-text" colspan="1">' +
					((value["invoice_number"] != null) ? value["invoice_number"] : '') +
					'</td> <td class="ws-nowrap">' +
					text_available + ' ' +
					"<span data-planned-course='" + value["id"] + "' id='planned_course_" + value["id"] + "' class='cursor-pointer available_click'> " + value["count_available"] + "/" + value["availability"] + "</span>" +
					' ';

				if (!($('.is_superAdmin-js')[0])) {
					texto += '<span ' + icon_plus + ' data-planned-course="' + value["id"] + '"class="cursor-pointer ion-android-add-circle color-green-btn"></span>';
				}

				texto += ' </td> <td colspan="1"><b>' +
					value["part_number"] +
					'</b></td> <td></td><td></td>"';
			});

			$("#" + id).after(texto);

			$(".provider_click").on("click", function () {
				let provider = $(this).data("training-provider");
				let url = '/trainings_providers/home?name=' + provider;
				window.location.href = url;
			});

			$(".venue_click").on("click", function () {
				let venue = $(this).data("venue");
				let url = '/venues/home?name=' + venue;
				window.location.href = url;
			});

			$(".available_click").on("click", function () {
				let planned_course = $(this).data("planned-course");
				let url = '/trainings_delegates/home/' + planned_course;
				window.location.href = url;
			});

			$(".color-green-btn").on("click", function () {
				let planned_course = $(this).data("planned-course");
				let url = '/trainings_delegates/add/' + planned_course;
				window.location.href = url;
			});


			$(".ico-toggle-cancel").on("click", function () {
				let item = $(this);
				let planned_course = $(this).data("planned-course");
				let url = '/trainings_planned_courses/ajax_toggle_status_cancel/' + planned_course;
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
						refundAllCredits(planned_course);
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

			$(".ico-toggle-active").on("click", function () {
				let item = $(this);
				let planned_course = $(this).data("planned-course");
				let url = '/trainings_planned_courses/ajax_toggle_status_active/' + planned_course;
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

			$(".ico-toggle-inactive").on("click", function () {
				let item = $(this);
				let planned_course = $(this).data("planned-course");
				let url = '/trainings_planned_courses/ajax_toggle_status_inactive/' + planned_course;
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

			var toggleStatusIconCancel = function (item_id, planned_course) {
				$(item_id).attr("hidden", true);
				$("#cancel_status_on_" + planned_course).attr("hidden", false);
				$("#inactive_status_on_" + planned_course).attr("hidden", true);
				$("#inactive_status_off_" + planned_course).attr("hidden", false);
				$("#active_status_on_" + planned_course).attr("hidden", true);
				$("#active_status_off_" + planned_course).attr("hidden", false);
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

			var questionDelete = function (planned_course) {
				swal({
					title: $.i18n._('Training.Proceed_delete_all_delegates?'),
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: primary_color,
					confirmButtonText: $.i18n._('General.Yes'),
					cancelButtonText: $.i18n._('General.No'),
				}).then(function (result) {
					if (result.value == true) {
						deleteAllDelegates(planned_course);
					} else if (result.dismiss == 'cancel') {
						refundAllCredits(planned_course);
					}
				});
			};

			var deleteAllDelegates = function (planned_course) {
				let url_delete_delegate = '/trainings_delegates/ajax_delete_all/' + planned_course;
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

			var refundAllCredits = function (planned_course) {
				let url_refund_credits = '/trainings_delegates/ajax_delete_all/' + planned_course;
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



		});
	};

	var toggleActive = function () {
		$(".activeInput").on("click", function () {
			let url = $(this).data("url");
			let data_course = {};
			data_course.course_id = $(this).data("id-value");
			let alertMessage = url == '/trainings_courses/ajax_toggle_status_active' ? $.i18n._("TrainingCourse.Status_toggle_inactive?") : $.i18n._("TrainingCourse.Status_toggle_active?");
			swal({
				title: alertMessage,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $.i18n._("General.Yes"),
				cancelButtonText: $.i18n._("General.No"),
			}).then(function (result) {
				if (result.value) {
					var request = PeticionAjax.post(url, data_course);
					request.done(function (result) {
						if (result) {
							swal({
								title: $.i18n._("Constants.Message_well_saved"),
								type: "success",
							}).then(function (result) {
								location.reload();
							});
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

	return {
		load: function () {
			submitCheckHiddenInput();
			chargeProducts();
			toggleActive();
		},
	};
})();
