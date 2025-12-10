$(document).ready(function () {
    ExportExcel.load();
});

var ExportExcel = function () {
    var loadExportExcelBehaviour = function () {
        $('.gd-export-excel-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');

            var networks_search = [];
            var networks_uk = [];
            var networks_fr = [];
            var networks_de = [];

            $('.networks-garages').each(function () {
                if ($(this).prop('checked')) {
                    var string_network = "network-garages-" + $(this).data("country") + "-" + $(this).data('id');
                    var string_replace = "network-garages-" + $(this).data("country") + "-";
                    var string_id = string_network.replace(string_replace, '');
                    if ($(this).data("country") == 'uk') {
                        networks_uk.push(string_id);
                    } else if ($(this).data("country") == 'fr') {
                        networks_fr.push(string_id);
                    } else if ($(this).data("country") == 'de') {
                        networks_de.push(string_id);
                    }
                }
            });
            if ($('#garage-province-uk').val() != 0) {
                networks_uk.push('province_id_uk_' + $('#garage-province-uk').val());
            }
            if ($('#garage-province-fr').val() != 0) {
                networks_fr.push('province_id_fr_' + $('#garage-province-fr').val());
            }
            if ($('#garage-province-de').val() != 0) {
                networks_de.push('province_id_de_' + $('#garage-province-de').val());
            }

            networks_search.push(networks_uk);
            networks_search.push(networks_fr);
            networks_search.push(networks_de);

            var jsonStringGarages = JSON.stringify(networks_search);

            var trading_groups_distributors_search = [];
            var trading_groups_distributors_uk = [];
            var trading_groups_distributors_fr = [];
            var trading_groups_distributors_de = [];
            var distributors_filters = [];

            $('.trading-groups-distributors-uk').each(function () {
                if ($(this).prop('checked') == true) {
                    trading_groups_distributors_uk.push($(this).data("id"));
                }
            });
            $('.trading-groups-distributors-fr').each(function () {
                if ($(this).prop('checked') == true) {
                    trading_groups_distributors_fr.push($(this).data("id"));
                }
            });
            $('.trading-groups-distributors-de').each(function () {
                if ($(this).prop('checked') == true) {
                    trading_groups_distributors_de.push($(this).data("id"));
                }
            });

            $('.distributor_filters').each(function () {
                if ($(this).prop('checked') == true) {
                    if ($(this).attr('id') == 'activity-check-branch') {
                        distributors_filters.push($(this).data("head_office_0"));
                    } else if ($(this).attr('id') == 'activity-check-independent') {
                        distributors_filters.push($(this).data("subsidiary_0"));
                    } else {
                        distributors_filters.push($(this).data("filter"));
                    }
                }
            });
            if ($('#distributor-province-uk').val() != 0) {
                distributors_filters.push('province_id_uk_' + $('#distributor-province-uk').val());
            }
            if ($('#distributor-province-fr').val() != 0) {
                distributors_filters.push('province_id_fr_' + $('#distributor-province-fr').val());
            }
            if ($('#distributor-province-de').val() != 0) {
                distributors_filters.push('province_id_de_' + $('#distributor-province-de').val());
            }

            trading_groups_distributors_search.push(trading_groups_distributors_uk);
            trading_groups_distributors_search.push(trading_groups_distributors_fr);
            trading_groups_distributors_search.push(trading_groups_distributors_de);
            trading_groups_distributors_search.push(distributors_filters);

            var jsonStringDistributors = JSON.stringify(trading_groups_distributors_search);

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
                    type: "POST",
                    data: {
                        garages: jsonStringGarages,
                        distributors: jsonStringDistributors
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
                            text: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelTrainingBehaviour = function () {
        $('.gd-export-training-excel-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
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
                            text: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelBuscador = function () {
        $('.gd-export-excel-buscador-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
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
                            text: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcel = function () {
        $('.gd-export-js').click(function (event) {
            let maxExport = $(this).data('max_export');
            let modalExport = $(this).data('modal_export');
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;
            let url = $(this).data('url_ajax_count');
            var request = PeticionAjax.post(url, data);

            request.done(function (data2) {
                if (data2 < maxExport) {
                    //Export file
                    // Timeout to wait for ajax stop
                    setTimeout(function () {
                        $.fileDownload(_url, {
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
                                    text: 'ERROR during the file generation.',
                                    type: "error"
                                });
                            }
                        });
                    }, 400);
                } else {
                    //Load modal to send csv email
                    let popup = new Foundation.Reveal($('#' + modalExport));
                    popup.open();
                }
            });
            return false;
        });
    };

    var csvEmailExport = function () {
        $('.send-data-export-js').click(function (event) {
            event.preventDefault();
            PeticionAjax.mostrarCargando();
            var url = $(this).data('url');
            let ajaxUrl = $(this).data('url_ajax');
            data = $('.buscador-js').serialize();
            let email = $('#contact-email-js').val();
            data = data + '&contact-email=' + email;
            var request = PeticionAjax.post(url, data);
            request.done(function (data2) {
                if (data2 == 1) {
                    //Call ajax that loads csv data.
                    var request = PeticionAjax.post(ajaxUrl, data);
                    request.done(function () {
                        swal({
                            title: 'Success',
                            text: $.i18n._('Export.sent_email'),
                            type: "success"
                        });
                        $('a.close-modal').trigger('click');
                        PeticionAjax.ocultarCargando();
                    });
                } else {
                    //Email is not valid
                    swal({
                        title: $.i18n._('Constants.Error_alert_general'),
                        text: $.i18n._('Validation.Email_incorrect_format'),
                        type: "error"
                    });
                }
            });
            return false;
        });
    }

    var loadExportExcelMarketingEmails = function () {
        $('.gd-export-excel-marketingEmails-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
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
                            text: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelCourses = function () {
        $('.gd-export-excel-courses-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
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
                            text: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    }

    var loadExportExcelTrainingProviders = function () {
        $('.gd-export-training-providers-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
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
                            text: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelTrainers = function () {
        $('.gd-export-training-trainers-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
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
                            text: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelTrainingCourses = function () {
        $('.gd-export-excel-training-courses-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
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
                            text: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelGarageCredit = function () {
        $('.gd-export-credits-garages-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
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
                            text: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelCreditMovements = function () {
        $('.gd-export-credits-movements-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
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
                            text: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelBookings = function () {
        $('.gd-export-excel-bookings-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
                    prepareCallback: function (url) {
                        PeticionAjax.mostrarCargando();
                    },
                    successCallback: function (url) {
                        PeticionAjax.ocultarCargando();
                    },
                    failCallback: function () {
                        PeticionAjax.ocultarCargando();
                        swal({
                            title: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelEnquiries = function () {
        $('.gd-export-excel-enquiries-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
                    prepareCallback: function () {
                        PeticionAjax.mostrarCargando();
                    },
                    successCallback: function () {
                        PeticionAjax.ocultarCargando();
                    },
                    failCallback: function () {
                        PeticionAjax.ocultarCargando();
                        swal({
                            title: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelQuotations = function () {
        $('.gd-export-excel-quotations-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
                    prepareCallback: function () {
                        PeticionAjax.mostrarCargando();
                    },
                    successCallback: function () {
                        PeticionAjax.ocultarCargando();
                    },
                    failCallback: function () {
                        PeticionAjax.ocultarCargando();
                        swal({
                            title: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    var loadExportExcelDelegates = function () {
        $('.gd-export-delegates-js').click(function (event) {
            event.preventDefault();
            var _url = $(this).data('url');
            data = $('.buscador-js').serialize();
            _url = _url + '?' + data;

            // Timeout to wait for ajax stop
            setTimeout(function () {
                $.fileDownload(_url, {
                    prepareCallback: function () {
                        PeticionAjax.mostrarCargando();
                    },
                    successCallback: function () {
                        PeticionAjax.ocultarCargando();
                    },
                    failCallback: function () {
                        PeticionAjax.ocultarCargando();
                        swal({
                            title: 'ERROR during the file generation.',
                            type: "error"
                        });
                    }
                });
            }, 400);
            return false;
        });
    };

    return {
        load: function () {
            loadExportExcelBehaviour();
            loadExportExcelTrainingBehaviour();
            loadExportExcelBuscador();
            loadExportExcelMarketingEmails();
            loadExportExcelCourses();
            loadExportExcelTrainingProviders();
            loadExportExcelTrainers();
            loadExportExcelTrainingCourses();
            loadExportExcelGarageCredit();
            loadExportExcelCreditMovements();
            loadExportExcelBookings();
            loadExportExcelEnquiries();
            loadExportExcelQuotations();
            loadExportExcel();
            csvEmailExport();
            loadExportExcelDelegates();
        }
    }

}();
