$(document).ready(function () {
	CreditDelegate.load();
});

var CreditDelegate = (function () {

	var addCredit = function () {
		$("#btn-guardar-delegate").on("click", function () {
			event.preventDefault();
			let item = $(this);
			let array_garages_not_subtract = item.data("array-garages-not-subtract");
			let garage_id = $('#garage_id').val();
			let network_id = $('#network_id').val();
			let delegate_id = $('#delegate_id').val();
			let order_number = $('#order_number').val();
			if (garage_id !== undefined && garage_id !== null && network_id !== undefined && network_id !== null &&
				delegate_id !== undefined && delegate_id !== null && order_number !== undefined && order_number !== "") {
				validateGarageNetwork(item, function (result) {
					if (result) {
						let not_subtract_credit = false;
						if (array_garages_not_subtract.hasOwnProperty(result)) {
							not_subtract_credit = true;
						}
						let swalOptions = {
							title: $.i18n._("Training.Add_delegate?"),
							type: "warning",
							showCloseButton: true,
							showCancelButton: true,
							showConfirmButton: false,
							cancelButtonText: $.i18n._("General.Yes_NOT_add_credit"),
						};
						if (not_subtract_credit) {
							swalOptions.showConfirmButton = true;
							swalOptions.confirmButtonText = $.i18n._("General.Yes_add_credit")
						}
						swal(swalOptions).then(function (result) {
							let modalClose = null;
							if (result.value == true) {
								$("#add_credits_input").val('accept');
								$("#delegate_form").trigger("submit");
							} else if (result.dismiss == 'cancel') {
								questionDelegate(item);
							} else if (result.dismiss == 'close' || result.dismiss == 'overlay' || result.dismiss == 'esc') {
								modalClose = true;
							}
						});
					} else {
						swal({
							title: $.i18n._("Constants.Error_alert_general"),
							type: "error",
						});
					}
				});
			} else {
				swal({
					title: $.i18n._("Crm.Empty_fields_next_appointment"),
					type: "error",
				});
			}
		});
	};

	var validateGarageNetwork = function (item, callback) {
		let url = item.data("url-array-garages-network");
		let garage_network = {};
		garage_network.garage_id = $('#garage_id').val();
		garage_network.network_id = $('#network_id').val();
		request = PeticionAjax.post(url, garage_network);
		if (request) {
			request.done(function (result) {
				if (result) {
					callback(result);
				} else {
					callback(false);
				}
			});
			request.fail(function () {
				callback(false);
			});
		}
	};

	var questionDelegate = function (item) {
		event.preventDefault();
		let array_options = item.data("array-reason");
		swal({
			title: $.i18n._("Training.Reason_delegate?"),
			type: "warning",
			showCancelButton: true,
			confirmButtonText: $.i18n._("General.Save_and_send"),
			input: "select",
			inputOptions: array_options,
			inputPlaceholder: $.i18n._("General.Select_option"),
			inputValidator: (value) => {
				if (!value) {
					return $.i18n._("General.Select_option_please");
				}
			}
		}).then(function (result) {
			if (result.value != false) {
				if (result.dismiss == 'cancel' || result.dismiss == 'close' || result.dismiss == 'overlay' || result.dismiss == 'esc') {
					modalClose = true;
				}
				else if (result && result.dismiss != 'cancel') {
					$("#reason_delegate_id").val(result.value);
					$("#add_credits_input").val("not_accept");
					$("#delegate_form").trigger("submit");
				} else {
					swal({
						title: $.i18n._("Constants.Error_alert_general"),
						type: "error",
					});
				}
				request.fail(function () {
					swal({
						title: $.i18n._("Constants.Error_alert_general"),
						type: "error",
					});
				});
			}
		});
	};

	var deleteDelegate = function () {
		$(".btn-delete-delegate").on("click", function () {
			let item = $(this);
			let url_delete_delegate = $(this).data('url-delete');
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
				if (result.value == true) {
					credits_points.Credit_points = 'true';
					questionCancelled(item, credits_points);
				} else if (result.dismiss == 'cancel') {
					credits_points.Credit_points = 'false';
					questionCancelled(item, credits_points);
				} else if (result.dismiss == 'close' || result.dismiss == 'overlay' || result.dismiss == 'esc') {
					swal.close();
				}
				if (request) {
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
		});
	};

	var questionCancelled = function (item, credits_points) {
		event.preventDefault();
		let array_options = item.data("array-reason");
		let url_delete_delegate = item.data('url-delete');
		swal({
			title: $.i18n._("Training.Reason_cancelled?"),
			type: "warning",
			showCancelButton: true,
			confirmButtonText: $.i18n._("General.Save_and_send"),
			input: "select",
			inputOptions: array_options,
			inputPlaceholder: $.i18n._("General.Select_option"),
			inputValidator: (value) => {
				if (!value) {
					return $.i18n._("General.Select_option_please");
				}
			}
		}).then(function (result) {
			if (result.value != false) {
				if (result.dismiss == 'cancel' || result.dismiss == 'close' || result.dismiss == 'overlay' || result.dismiss == 'esc') {
					modalClose = true;
				}
				else if (result && result.dismiss != 'cancel') {
					$("#reason_cancelled_id").val(result.value);
					credits_points.reason_cancelled_id = result.value;
					request = PeticionAjax.post(url_delete_delegate, credits_points);
				} else {
					swal({
						title: $.i18n._("Constants.Error_alert_general"),
						type: "error",
					});
				}
				request.fail(function () {
					swal({
						title: $.i18n._("Constants.Error_alert_general"),
						type: "error",
					});
				});
			} if (request) {
				request.done(function (result) {
					if (result) {
						swal({
							title: $.i18n._("Constants.Message_well_cancelled"),
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

	var submitCheckHiddenInput = function () {
		$("#delegate_form").on("submit", function () {
			if ($("#garage_id").prop("disabled", true)) {
				$("#garage_id").prop("disabled", false);
			}
			if ($("#network_id").prop("disabled", true)) {
				$("#network_id").prop("disabled", false);
			}
		});
	};

	var toggleStatus = function () {
		$(".ico-toggle-activate").on('click', function () {
			let url = $(this).data('url');
			let item = $(this);
			let alertMessage = null;
			if ($(this).hasClass('c-fallo')) {
				alertMessage = $.i18n._('Training.Status_toggle_reactivate?');
			} else {
				alertMessage = $.i18n._('Training.Status_toggle_activate?');
			}
			swal({
				title: alertMessage,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $.i18n._('General.Yes'),
				cancelButtonText: $.i18n._('General.No'),
			}).then(function (result) {
				if (result.value) {
					addCreditToggle(item);
				}
			});
		});
	};

	var toggleStatusIcon = function (item_id) {
		if ($(item_id).hasClass('c-fallo')) {
			$(item_id).removeClass('c-fallo ion-toggle');
			$(item_id).addClass('c-exito ion-toggle-filled');
		} else {
			$(item_id).removeClass('c-exito ion-toggle-filled');
			$(item_id).addClass('c-fallo ion-toggle');
		}
	}

	var addCreditToggle = function (item) {
		let data = { substract_credits: false };
		let swalOptions = {
			title: $.i18n._("Training.Reactivate_delegate?"),
			type: "warning",
			showCloseButton: true,
			showCancelButton: true,
			showConfirmButton: true,
			confirmButtonText: $.i18n._("General.Yes_add_credit"),
			cancelButtonText: $.i18n._("General.Yes_NOT_add_credit"),
		};
		swal(swalOptions).then(function (result) {
			let modalClose = null;
			if (result.value == true) {
				data.substract_credits = true;
				executeData(item, data);
			} else if (result.dismiss == 'cancel') {
				data.substract_credits = false;
				executeData(item, data);
			} else if (result.dismiss == 'close' || result.dismiss == 'overlay' || result.dismiss == 'esc') {
				modalClose = true;
			}
		});
	};

	var executeData = function (item, boolean) {
		let url = item.data('url');
		var request = PeticionAjax.post(url, boolean);
		request.done(function (result) {
			if (result) {
				toggleStatusIcon(item);
				location.reload();
			} else {
				swal({
					title: $.i18n._('Constants.Error_alert_general'),
					type: "error"
				});
			}
		});
		request.fail(function () {
			swal({
				title: $.i18n._('Constants.Error_alert_general'),
				type: "error"
			});
		});
	};

	return {
		load: function () {
			addCredit();
			deleteDelegate();
			submitCheckHiddenInput();
			toggleStatus();
		},
	};
})();
