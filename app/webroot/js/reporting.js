$(document).ready(function () {
	Reporting.load();
});

let Reporting = (function () {
	let loadBehaviour = function () {
		$(".fecha-js.from-js").on('change', function () {
			$(".fecha-js.max-stats-js").each(function () {
				$(this).datepicker("option", "maxDate", moment($(".fecha-js.from-js").val(), "DD-MM-YYYY").add(1, 'year').add(-1, 'month').endOf('month').format('DD-MM-YYYY'));
			});
		});
	};

	let loadchartServices = function () {
		if (document.getElementById("chartServices") != null) {
			let chartServices = new Chart(document.getElementById("chartServices").getContext('2d'), {
				type: 'doughnut',
				data: {
					datasets: [
						{
							data: $('#chartServices').data('datasets_data'),
							backgroundColor: _colors()
						}
					],
					labels: $('#chartServices').data('labels')
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					legend: { position: 'left' },
					title: {
						display: true,
						text: $('#chartServices').data('title')
					},
					tooltips: {
						enabled: true,
						mode: 'nearest',
						callbacks: {
							title: function (tooltipItem, data) {
								return data.labels[tooltipItem[0].index];
							},
							label: function (tooltipItems, data) {
								return data.datasets[tooltipItems.datasetIndex].data[tooltipItems.index] + ' (%)';
							},
						}
					},
				}
			});
		}
	}

	let loadchartMetrics = function () {
		if (document.getElementById("chartMetrics") != null) {
			let chartMetrics = new Chart(document.getElementById("chartMetrics").getContext('2d'), {
				type: 'line',
				data: {
					labels: $('#chartMetrics').data('labels'),
					datasets: $('#chartMetrics').data('datasets_data'),
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					legend: { position: 'top' },
					tooltips: {
						mode: 'nearest',
						intersect: false
					},
					scales: {
						yAxes: [{
							ticks: {
								beginAtZero: true
							}
						}]
					},
					title: {
						display: true,
						text: $('#chartMetrics').data('titletext')
					}
				}
			});
		}
	}

	let downloadData = function () {
		$(document).on('click', '.download-chart-js', function () {
			let a = document.createElement('a');
			a.href = document.getElementById($(this).data('canvas_id')).toDataURL();
			a.download = 'chart.png';
			a.click();
		});

		$(document).on('click', '.download_chart_excel-js', function (event) {
			let _url = $(this).data('url');
			let datasets_data = $(this).data('datasets_data');
			let labels = $(this).data('labels');
			event.preventDefault();
			PeticionAjax.mostrarCargando();

			// Timeout to wait for ajax stop
			setTimeout(function () {
				$.fileDownload(_url, {
					type: "POST",
					data: {
						'datasets_data': datasets_data,
						'labels': labels
					},
					prepareCallback: function () {
						PeticionAjax.mostrarCargando();
					},
					successCallback: function () {
						PeticionAjax.ocultarCargando();
					},
					failCallback: function () {
						PeticionAjax.ocultarCargando();
						swal({
							title: $.i18n._('Constants.Error_alert_general'),
							type: "error"
						});
					}
				});
			}, 400);

			return false;
		});
	}

	let _colors = function () {
		return [
			'#e6194b', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#46f0f0', '#f032e6', '#bcf60c', '#fabebe', '#008080', '#e6beff', '#9a6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000075', '#808080', '#ffffff', '#000000'
		]
	}

	let loadGarages = function () {
		$('#garage_name_reporting-js').select2({
			minimumInputLength: 2,
			language: {
				inputTooShort: function (args) {
					return $.i18n._('General.Enter_at_least_two_characters');
				},
				noResults: function (args) {
					return $.i18n._('General.No_results_found');
				},
				searching: function (args) {
					return $.i18n._('General.Searching');
				},
				errorLoading: function () {
					return $.i18n._('General.The_results_could_not_be_loaded');
				},
				loadingMore: function () {
					return $.i18n._('General.Loading_more_results');
				},
			},
			ajax: {
				url: $('#garage_name_reporting-js').data('url'),
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						search: params.term,
						page: params.page || 1
					};
				},
				processResults: function (data) {
					return {
						results: $.map(data, function (obj) {
							return {
								id: obj.Garage.id, text: obj.Garage.complete_name
							};
						}),
						pagination: {
							more: data.length >= 10
						}
					};
				}
			}
		});
	};

	return {
		load: function () {
			loadBehaviour();
			loadchartServices();
			loadchartMetrics();
			downloadData();
			loadGarages();
		}
	}
})();
