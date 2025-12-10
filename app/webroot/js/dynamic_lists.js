$(document).ready(function () {
    DynamicLists.load();
});

let DynamicLists = (function () {
    /**
     * dynamic select used for search pages
     * no placholder
     */
    let selectDistributor = function () {
        $('.dynamicSelect2_distributors').select2({
            ajax: {
                url: '/distributors/get_distributors_name_region',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#distributor_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            placeholder: "",
            minimumInputLength: 3,
            allowClear: true
        });

        if ($('#distributor-id-js').length && $('#distributor-id-js').data('selected_distributors').length) {
            $('#distributor-id-js').data('selected_distributors').forEach(distributor => {
                let newOption = new Option(distributor.text, distributor.id, true, true);
                $('#distributor-id-js').append(newOption).trigger('change');
            });
            $('#distributor-id-js').removeAttr('data-selected_distributors');
        }
    };

    let selectGarage = function () {
        $('.dynamicSelect2_garages').select2({
            ajax: {
                url: '/garages/get_garages_name_region',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#garage_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 3,
            allowClear: true,
        });
    };

    /**
     * Dynamic cities filter for garages search page
     */
    let selectCities = function () {
        $('.dynamicSelect2_cities').select2({
            ajax: {
                url: '/cities/get_cities_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#city-id option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 3,
            allowClear: true,
        });
    };

    /**
     * Dynamic cities filter for clients search page.
     * temporary duplicated function
     */
    let selectCitiesClients = function () {
        $('.dynamicSelect2_cities_clients').select2({
            ajax: {
                url: '/cities/get_cities_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#city-id-clients option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 3,
            allowClear: true,
        });

        if ($('#city-id-clients').length && $('#city-id-clients').data('selected-cities').length) {
            $('#city-id-clients').data('selected-cities').forEach(city => {
                let newOption = new Option(city.text, city.id, true, true);
                $('#city-id-clients').append(newOption).trigger('change');
            });
            $('#city-id-clients').removeAttr('data-selected-cities');
        }
    };

    let selectDistributorAdd = function () {
        let garage_id = $('#distributor_name').data('garage_id');
        $('.select2Dinamico_distributor_dynamic').select2({
            ajax: {
                url: '/distributors/get_distributors_name_region_dynamic',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term,
                        garage_id: garage_id,
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#distributor_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            placeholder: "Distributors",
            minimumInputLength: 3,
            allowClear: true,
        });
    };

    let selectContactsDistributorsBDM = function () {
        $('.dynamicSelect2_contacts_distributors_bdm').select2({
            ajax: {
                url: '/distributors_contacts_bdm/get_contacts_bdm_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#bdm-id option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            placeholder: "",
            minimumInputLength: 3,
            allowClear: true,
        });
    };

    let selectContactsGaragesBDM = function () {
        $('.dynamicSelect2_contacts_garages_bdm').select2({
            ajax: {
                url: '/garages_contacts_bdm/get_contacts_bdm_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#bdm-id option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            placeholder: "",
            minimumInputLength: 3,
            allowClear: true,
        });
    };

    let selectUser = function () {
        $('.dynamicSelect2_users').select2({
            ajax: {
                url: '/appointments/get_users_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#user_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 3,
            allowClear: true,
        });
    };

    //Used in tasks/home view
    let selectUserBDM = function () {
        $('.select2Dinamico_user_bdm').select2({
            placeholder: "",
            allowClear: true,
            tags: true
        });

        $('.update_users_bdm').select2({
            ajax: {
                url: '/appointments/get_users_bdm_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#user_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 3,
            placeholder: "Min 3 characters",
            allowClear: true,
        });
    };

    let selectVenue = function () {
        $('.dynamicSelect2_venues').select2({
            ajax: {
                url: '/venues/get_venues_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#garage_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            placeholder: "",
            minimumInputLength: 3,
            allowClear: true
        });
    };

    let selectSupplier = function () {
        $('.dynamicSelect2_suppliers').select2({
            ajax: {
                url: '/suppliers/get_suppliers_name',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#supplier_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            placeholder: "",
            minimumInputLength: 3,
            allowClear: true,
        });
    };

    let selectGarageLive = function () {
        $('.dynamicSelect2_garages_live').select2({
            ajax: {
                url: '/garages/get_garages_name_live',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#garage_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            placeholder: "",
            minimumInputLength: 3,
            allowClear: true,
        });
    };

    let selectContactsDelegates = function () {
        $('.dynamicSelect2_contacts_delegates').select2({
            ajax: {
                url: '/contacts/get_contacts_name_delegates',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#delegate_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 3,
            placeholder: "",
            allowClear: true,
        });
    };

    let selectGarageOnlyLive = function () {
        $('.update_garages_only_live').select2({
            ajax: {
                url: '/garages/get_garages_name_only_live',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        name: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#garage_name option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            minimumInputLength: 3,
            placeholder: "Min 3 characters",
            allowClear: true,
        });
    };

    let selectEmailsContacts = function () {
        $('.dynamicSelect2_contacts_emails').select2({
            ajax: {
                url: '/contacts/get_contacts_email_region',
                delay: 200,
                dataType: 'json',
                data: function (params) {
                    let query = {
                        email: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    $("#contact_email option").remove();
                    return {
                        results: data.items
                    };
                }
            },
            placeholder: "",
            minimumInputLength: 3,
            allowClear: true
        });

		$('.contact-id-js').each(function(){
			if ($(this).length && $(this).data('selected_contacts_emails').length) {
				$(this).data('selected_contacts_emails').forEach(contact => {
					let newOption = new Option(contact.text, contact.id, true, true);
					$(this).append(newOption).trigger('change');
				});
				$(this).removeAttr('data-selected_contacts_emails');
			}
		});
    };

    return {
        load: function () {
            selectDistributor();
            selectGarage();
            selectCities();
            selectCitiesClients();
            selectDistributorAdd();
            selectContactsDistributorsBDM();
            selectContactsGaragesBDM();
            selectUser();
            selectUserBDM();
            selectVenue();
            selectSupplier();
            selectGarageLive();
            selectContactsDelegates();
            selectGarageOnlyLive();
            selectEmailsContacts();
        },
    };
})();
