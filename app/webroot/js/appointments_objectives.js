$(document).ready(function () {
	Objective.load();
});

var Objective = (function () {

	var deleteObjective = function () {
		$(".delete-all-objectives").off('click').on('click', function (e) {
			e.preventDefault();
			var url = $(this).data('url');

			// scheduled task limit to delete and lauched time (launched every x minutes)
			let objectivesDeletedScheduledTask = $(this).data('limit-objectives-deleted-scheduled-task');
			let minutesScheduledTask = $(this).data('time-objectives-deleted-scheduled-task');

			swal({
				title: $.i18n._('AppointmentObjective.Delete_all_objectives?'),
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: primary_color,
				confirmButtonText: $.i18n._('General.Yes'),
				cancelButtonText: $.i18n._('General.No'),
			}).then(function (result) {
				if (result.value) {
					PeticionAjax.mostrarCargando();
					var request = PeticionAjax.post(url);
					request.done(function (totalObjectivesToDelete) {
						ocultarCargando();

						// if some objective will be deleted with the scheduled task
						if (totalObjectivesToDelete > 0) {
							// calculate approx time to finish deletion
							let timeToFinish = (totalObjectivesToDelete * minutesScheduledTask) / objectivesDeletedScheduledTask;

							// if time to finish is less than the time of execution of the scheduled task
							if (timeToFinish < minutesScheduledTask) {
								timeToFinish = minutesScheduledTask;
							}

							swal({
								title: $.i18n._('AppointmentObjective.Objectives_delete_minutes', totalObjectivesToDelete, Math.round(timeToFinish)),
								type: 'warning'
							}).then(function () {
								window.location.replace(window.location.origin + '/appointments_objectives/view_objectives');
							});
						} else {
							window.location.replace(window.location.origin + '/appointments_objectives/view_objectives');
						}
					});
				}
			})
		});
	};

	var ocultarCargando = function () {
		$('.preloader').remove();
	};

	var objectives = function () {
		$('#check_Tg_management').click(function () {
			$('#check_Gpc_management').prop('checked', false);
			$('#Gpc_management').addClass('d-none');
			$('#Tg_management').removeClass('d-none');
			$('#select_distributors').removeClass('d-none');
		});

		$("#check_Gpc_management").click(function () {
			$('#check_Tg_management').prop('checked', false);
			$('#Gpc_management').removeClass('d-none');
			$('#Tg_management').addClass('d-none');
			$('#select_distributors').removeClass('d-none');
		});
	};

	var selectItem = function () {
		var $list1 = $('#List1');
		var $list2 = $('#List2');

		$list1.children().on('click', function () {
			$list1.children().each(function () {
				$(this).removeClass('selected_item');
			});
			$list2.children().each(function () {
				$(this).removeClass('selected_item');
			});

			$(this).addClass('selected_item');
			selected_item = $(this);
		});

		$list2.children().on('click', function () {
			$list1.children().each(function () {
				$(this).removeClass('selected_item');
			});
			$list2.children().each(function () {
				$(this).removeClass('selected_item');
			});

			$(this).addClass('selected_item');
			selected_item = $(this);
		});
	};

	var moveItem = function () {
		$('#move-to-left').on('click', function () {
			if (selected_item != null) {
				if (selected_item.parent().attr('id') == 'List2') {
					selected_item.detach();
					$('#List1').prepend(selected_item);
				}
			}
		});
		$('#move-to-right').on('click', function () {
			if (selected_item != null) {
				if (selected_item.parent().attr('id') == 'List1') {
					selected_item.detach();
					$('#List2').prepend(selected_item);
				}
			}
		});
	};

	var searchTime = function () {
		var timer;
		$('#search-form').keyup(function () {
			clearTimeout(timer);
			timer = setTimeout(function () {
				search();
			}, 750);
		});
	};

	var search = function () {
		var $search = $("#search-form");
		var data = {};
		data.name = $search.val();

		var url = $search.data('url');
		var request = PeticionAjax.post(url, data);
		request.done(function (data) {
			$('#list_form').html(data);
			selectItem();
			sortTableList();
			selected_item = null;
			window.dispatchEvent(new Event('resize'));
		});
	};

	var sortTableList = function () {
		$(function () {
			$("#List1, #List2").sortable(
				{
					appendTo: 'body',
					tolerance: 'pointer',
					connectWith: '#List1, #List2',
					revert: 'invalid',
					forceHelperSize: true,
					helper: 'original',
					scroll: true
				});
		});
	};

	var saveDistributorsObjectives = function () {
		$('#set-objectives-js').on('click', function (e) {
			var distributors_ids = [];
			var objectives_ids = [];

			e.preventDefault();

			$("#Tg_management").find(".tg_objectives").each(function () {
				if ($(this).prop('checked') == true) {
					objectives_ids.push($(this).data('objective-id'));
				}
			});

			$("#Gpc_management").find(".gpc_objectives").each(function () {
				if ($(this).prop('checked') == true) {
					objectives_ids.push($(this).data('objective-id'));
				}
			});

			if ($('#check_Tg_management').prop('checked') == false && $('#check_Gpc_management').prop('checked') == false) {
				swal({
					title: $.i18n._('DistributorObjective.Error_no_objectives'),
					type: "info",
				});
			}
			else if (distributors_ids.length === 0 && $('#all_check').prop('checked') == false) {
				swal({
					title: $.i18n._('DistributorObjective.Error_vacio'),
					type: "info",
				});
			}
			else if (objectives_ids.length === 0) {
				swal({
					title: $.i18n._('DistributorObjective.Error_objectives_vacio'),
					type: "info",
				});
			}
			else if ($('#to').val() && !$('#from').val()) {
				swal({
					title: $.i18n._('DistributorObjective.Error_from_vacio'),
					type: "info",
				});
			}
			else {
				var url = $('#set-objectives-js').data('url');

				data = {};
				data.objectives_ids = objectives_ids;
				data.from = $('#from').val();
				data.to = $('#to').val();
				data.filter_rsm = $('#garage_filter_rsm').val();
				data.filter_bdm = $('#garage_filter_bdm').val();
				data.filter_trading = $('#trading-group-id').val();
				data.filter_customer = $('#customer_activity_id').val();

				var EmptyRSM = false;
				var EmptyBDM = false;
				var EmptyTrading = false;
				var EmptyCustomer = false;

				if (!$('#garage_filter_rsm').val() || $('#garage_filter_rsm').val().length === 0) {
					EmptyRSM = true;
				}

				if (!$('#garage_filter_bdm').val() || $('#garage_filter_bdm').val().length === 0) {
					EmptyBDM = true;
				}

				if (!$('#trading-group-id').val() || $('#trading-group-id').val().length === 0) {
					EmptyTrading = true;
				}

				if (!$('#customer_activity_id').val() || $('#customer_activity_id').val().length === 0) {
					EmptyCustomer = true;
				}

				if ($('#distributor_name_assigned_to').val() == null) {
					$("#distributor_name_assigned_to option").each(function () {
						if ($(this).val()) {
							distributors_ids.push($(this).val());
						}
					});
					data.distributors_ids = distributors_ids;
				}
				else {
					data.distributors_ids = $('#distributor_name_assigned_to').val();
				}

				var value_distributor = null;

				if ((EmptyRSM && EmptyBDM && EmptyTrading && EmptyCustomer) && data.distributors_ids.length === 0) {
					value_distributor = $.i18n._('General.All').toLowerCase();
				} else if ((!EmptyRSM || !EmptyBDM || !EmptyTrading || !EmptyCustomer) && data.distributors_ids.length === 0) {
					value_distributor = $('#distributor-count-update-js').val();
				} else {
					value_distributor = data.distributors_ids.length;
				}

				swal({
					title: $.i18n._('DistributorObjective.Do_you_want_set_objectives_for') + ' ' + value_distributor + ' ' + $.i18n._('Distributor.Distributors').toLowerCase() + '?',
					type: $('#set-objectives-js').data('type'),
					showCancelButton: true,
					showConfirmButton: (value_distributor == 0) ? false : true,
					confirmButtonColor: primary_color,
					confirmButtonText: $.i18n._('General.Yes'),
					cancelButtonText: $.i18n._('General.No'),
				}).then(function (result) {
					if (result.value) {
						var request = PeticionAjax.post(url, data);

						PeticionAjax.mostrarCargando();
						request.done(function () {
							ocultarCargando();

							var objectivesCreatedScheduledTask = $('#set-objectives-js').data('limit-objectives-created-scheduled-task');
							var minutesScheduledTask = $('#set-objectives-js').data('time-objectives-created-scheduled-task');

							// get total number of distributors
							var countDistributors = value_distributor != 'all' ? value_distributor : $('#count_distributors').html().replace("(", "").replace(")", "");

							// calculate total number of objectives that will be created
							// 1 objective for each distributor (total number of distributors * total of objectives) minus the first x objectives created
							// without the scheduled task
							var totalObjectivesToCreate = (countDistributors * objectives_ids.length) - objectivesCreatedScheduledTask;

							if (totalObjectivesToCreate > 0) {
								// calculate approx time to finish deletion
								var timeToFinish = (totalObjectivesToCreate * minutesScheduledTask) / objectivesCreatedScheduledTask;

								swal({
									title: $.i18n._('AppointmentObjective.Objectives_create_minutes', totalObjectivesToCreate, Math.round(timeToFinish)),
									type: 'warning'
								}).then(function () {
									location.reload();
								});
							} else {
								swal({
									title: $.i18n._("Constants.Message_well_dis_created"),
									type: "success",
								}).then(function () {
									location.reload();
								});
							}
						});
						request.fail(function () {
							swal({
								title: $.i18n._('Constants.Message_bad_dis_created'),
								type: "error"
							});
						});
					}
				});
			}
		});
	};

	var selectDistributors = function () {
		$('#all_check').on('click', function (e) {
			if ($('#all_check').prop('checked') == true) {
				swal({
					title: $('#all_check').data('texto1'),
					type: "info",
					showCancelButton: true,
					confirmButtonText: $(this).data('yes'),
					cancelButtonText: $(this).data('no')
				}).then(function (result) {
					if (!result.value) {
						$('#all_check').prop('checked', false);
					}
					else {
						PeticionAjax.mostrarCargando();
						var html_1 = $('#List1').html();
						var html_2 = $('#List2').html();
						html = html_1 + html_2;
						$('#List1').html(html);
						$('#List2').html('');
						sortTableList();
						ocultarCargando();
						// $('#List2 div').each(function(){
						// 	if( $(this).data('id') != 0 ){
						// 		$(this).detach();
						// 		$('#List1').prepend($(this));
						// 		sortTableList();
						// 		ocultarCargando();
						// 	}
						// });
					}
				});
			}
			else {
				swal({
					title: $('#all_check').data('texto2'),
					type: "info",
					showCancelButton: true,
					confirmButtonText: $(this).data('yes'),
					cancelButtonText: $(this).data('no')
				}).then(function (result) {
					if (!result.value) {
						$('#all_check').prop('checked', true);
					}
					else {
						PeticionAjax.mostrarCargando();
						var html_1 = $('#List1').html();
						var html_2 = $('#List2').html();
						html = html_2 + html_1;
						$('#List1').html('');
						$('#List2').html(html);
						sortTableList();
						ocultarCargando();

						// $('#List1 div').each(function(){
						// 	if( $(this).data('id') != 0 ){
						// 		$(this).detach();
						// 		$('#List2').prepend($(this));
						// 		sortTableList();
						// 		ocultarCargando();
						// 	}
						// });

					}
				});
			}
		});
	};

	var handleChangeFilter = function () {
		$('#search_distributors, clear_field').on('click', searchDistributors);
	}

	var searchDistributors = function () {
		PeticionAjax.mostrarCargando();
		var url = $('#search_distributors').data('url');

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
			if (data == 'all_dis') {
				$('#count_distributors').html('(' + $('#distributor-count-js').val() + ')');
				$('#distributor-count-update-js').val($('#distributor-count-js').val());
			} else {
				$('#distributor-id').empty();
				$('#distributor-id').append('<option value=""></option>');

				datos = JSON.parse(data);
				datos = Object.entries(datos);

				var total = 0;
				$.each(datos, function (i, value) {
					total++;
					$('#distributor-id').append($('<option>').text(value[1]).attr('value', value[0]));
				});

				$('#count_distributors').html('(' + total + ')');
				$('#distributor-count-update-js').val(total);
			}
			ocultarCargando();
		});
	};

	var searchDistributorsUpdated = function () {
		var distributor_select = $('#distributor_name_assigned_to');
		setTimeout(function () {
			distributor_select.select2({
				minimumInputLength: 3,
				placeholder: "Min 3 characters",
				allowClear: true,
				ajax: {
					url: distributor_select.data('url'),
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							search: params.term,
							page: params.page || 1,
							distributor_filter_bdm: $('#garage_filter_bdm').val(),
							distributor_filter_rsm: $('#garage_filter_rsm').val(),
							trading_group_id: $('#trading-group-id').val(),
							activity_id: $('#customer_activity_id').val(),
						};
					},
					processResults: function (data) {
						return {
							results: $.map(data, function (obj) {
								return {
									id: obj.Distributor.id, text: obj.Distributor.complete_name
								};
							}),
							pagination: {
								more: data.length >= 10
							}
						};
					}
				}
			});
		}, 50);
	};

	return {
		load: function () {
			objectives();
			selectItem();
			moveItem();
			searchTime();
			deleteObjective();
			saveDistributorsObjectives();
			selectDistributors();
			searchDistributorsUpdated();
			handleChangeFilter();
		},
		ocultarCargando: function () {
			ocultarCargando();
		}
	}
})();
