$(document).ready(function(){
	ButtonSMS();
    updateApiKeyView();
    
    var editClicks = {};
    $('.edit-template-js').on('click', function() {
        var id = $(this).data('template-id');

        if (!editClicks[id]) {
            descriptionLength(id);
            ButtonSMSEdit(id);
            updateDescription(id)
            editClicks[id] = true;
        }
    });

    $('#add_sms_form').on('click', function(){
        var url = $('#add_sms_form').data('url');
        var formData = new FormData($('#sms-add-form')[0]);
        var request = $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false
        });

        request.done(function (data) {
            $('a.close-modal').trigger('click');
            location.reload();
        });
    });

    $(".delete-license-js").on('click', function () {
        let url = $(this).data('delete-url');
        let licenseId = $(this).data('data-license-id');
        swal({
            title: $.i18n._('Alert.Sure?'),
            html: "<b>" + $.i18n._('General.Delete_license') + "</b> " + $.i18n._('General.Irreversible_action'),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: primary_color,
            confirmButtonText: $.i18n._('General.Yes'),
            cancelButtonText: $.i18n._('General.No'),
        }).then(function (result) {
            if (result.value) {
                var request = PeticionAjax.post(url, { licenseId: licenseId });
                request.done(function (result) {
                    if(result) {
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
            };
        });
    });

    $(".delete-template-js").on('click', function () {
        let url = $(this).data('delete-url');
        let templateId = $(this).data('data-template-id');
        swal({
            title: $.i18n._('Alert.Sure?'),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: primary_color,
            confirmButtonText: $.i18n._('General.Yes'),
            cancelButtonText: $.i18n._('General.No'),
        }).then(function (result) {
            if (result.value) {
                var request = PeticionAjax.post(url, { templateId: templateId });
                request.done(function (result) {
                    if(result) {
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
            };
        });
    });

    $('#add_sms_template').on('click', function(){
        var url = $('#add_sms_template').data('url');
        var formData = new FormData($('#sms-template-form-js')[0]);
        var request = $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false
        });

        request.done(function (data) {
            $('a.close-modal').trigger('click');
            location.reload();
        });
    });

    $('#char_count_container-add').text(1600 - $('.chart_count-add').val().length);
    $('#sms_count_container-add').text(0);

    $('.chart_count-add').on("input", function() {
        var maxCharacters = 1600;
        var charactersPerSMS = 160;
        var currentLength = $(this).val().length;
        if (currentLength > maxCharacters) {
            $(this).val($(this).val().substring(0, maxCharacters));
            currentLength = maxCharacters;
        }
        var remainingCharacters = maxCharacters - currentLength;
        $('#char_count_container-add').text(remainingCharacters);
        var smsCount = Math.ceil(currentLength / charactersPerSMS);
        $('#sms_count_container-add').text(smsCount);
    });

	$('.type_template_sms-js').on('change', function () {
		PeticionAjax.mostrarCargando();

		$('.chart_count-add').val('');
		let data = {};
		data.template_type_id = $(this).val();
		let requestCountries = PeticionAjax.post($(this).data('url'), data);
		requestCountries.done(function (dataCountries) {
			let datos = Object.entries(JSON.parse(dataCountries));
			$('.country_sms-js').empty().append('<option value=""></option>');
			$.each(datos, function(i, value) {
				$('.country_sms-js').append($('<option>').text(value[1]).attr('value', value[0]));
			});
		});

		let request = PeticionAjax.post($(this).data('url_options_add'), data);
		request.done(function (dataOptionsAddTemplate) {
            PeticionAjax.ocultarCargando();
			$('.options_add_template-js').html(dataOptionsAddTemplate);
			ButtonSMS();
		});
	});

    $('.save-config-js').on('click',function(event){
        event.preventDefault();
        var container = $(this).closest('.container-url-js');
        var url = $(this).data('shortner-url');
        var id = $(this).attr("id");
        var data = {};
        data.sms_id = $(this).data('sms-id');
        data.country_id = $(this).data('country-id');
        data.api_key = container.find('.api-key-js').val() ?? null;
        data.expire_at_datetime = container.find('.expire-at-datetime-js').val();
        data.expire_at_views = container.find('.expire-at-views-js').val();
        data.domain = container.find('.domain-js').val();
        PeticionAjax.mostrarCargando();
        var request = PeticionAjax.post(url,data);
        request.done(function(result){
            PeticionAjax.ocultarCargando();
            if (result === 'ok') {
                swal({
                    title: $.i18n._("Sms.Message_well_saved"),
                    type: "success",
                }).then(function () {
                    var url = $('#'+id).data('url');
                    var formData = new FormData($('#sms-form-url'+id)[0]);
                    var request = $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        async: false,
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                    request.done(function (data) {
                        $('a.close-modal').trigger('click');
                        location.reload();
                    });
                });
            }else if(result === 'restart'){
                swal({
                    title: $.i18n._("Sms.Message_well_saved"),
                    type: "success",
                }).then(function () {
                    request.done(function (data) {
                        $('a.close-modal').trigger('click');
                        location.reload();
                    });
                });
            }else if(result === 'error_apikey'){
                swal({
                    title: $.i18n._("Sms.Cannot_create_api"),
                    type: "error",
                });
            }else if(result === 'error_domain'){
                swal({
                    title: $.i18n._("Sms.Cannot_create_domain"),
                    type: "error",
                });
            }else{
                swal({
                    title: $.i18n._("Sms.Cannot_create"),
                    type: "error",
                });
            }
        });
    });

    $('.expire-at-datetime-js, .expire-at-views-js').on('keydown', function(event) {
        if (event.key === '-' || event.key === '.') {
            event.preventDefault();
        }
    }).on('input', function() {
        var value = parseInt($(this).val(), 10);
        if (isNaN(value) || value <= 0) {
            $(this).val('');
        }
    });

    $('.domain-js').on('keyup', function(event) {
        var container = $(this).closest('.container-url-js');
        if ($(this).val() != 'sms.aaggnm.com' && $(this).val() != '') {
            container.find('.api-key-js').attr('type', 'text');
        } else {
            container.find('.api-key-js').attr('type', 'hidden');
        }
    });
      

});

function ButtonSMS() {
	$(".button-sms-js").on('click', function () {
		let text = $('.chart_count-add').val();
		$('.chart_count-add').val( text + ' ' + $(this).data('value') + ' ');
        $('.chart_count-add').trigger('input');
	});
    
}

function Edit_sms_conf(comp){
    var id = comp.id;
    var url = $('#'+id).data('url');
    var formData = new FormData($('#sms-form'+id)[0]);
    var request = $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        async: false,
        cache: false,
        contentType: false,
        processData: false
    });

    request.done(function (data) {
        $('a.close-modal').trigger('click');
        location.reload();
    });
}

function Edit_template_conf(comp){
    var id = comp.id;
    var url = $('#'+id).data('url');
    var formData = new FormData($('#sms-template-form'+id)[0]);
    var request = $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        async: false,
        cache: false,
        contentType: false,
        processData: false
    });

    request.done(function (data) {
        $('a.close-modal').trigger('click');
        location.reload();
    });
}

function ButtonSMSEdit(id) {
	$(".button-sms-edit-js"+id).on('click', function (){
        var text = $('.chart_count-edit'+id).val();
        $('.chart_count-edit'+id).val( text + ' ' + $(this).data('value') + ' ');
        $('.chart_count-edit' + id).trigger('input');
    });
    
}

function descriptionLength(id){
    $('#char_count_container-edit'+id).text(1600 - $('.chart_count-edit'+id).val().length);
    $('#sms_count_container-edit'+id).text(1);
}

function updateDescription(id){
    $('.chart_count-edit'+id).on("input", function() {
        var maxCharacters = 1600;
        var charactersPerSMS = 160;
        var currentLength = $(this).val().length;
        var smsCount = null;
        if (currentLength > maxCharacters) {
            $(this).val($(this).val().substring(0, maxCharacters));
            currentLength = maxCharacters;
        }
        var remainingCharacters = maxCharacters - currentLength;
        
        var updateText = checkUrlLength($('.chart_count-edit'+id), id).then(function(url_length) {
            if (url_length == undefined || url_length == null) {
                smsCount = Math.ceil(currentLength / charactersPerSMS);
                $('#sms_count_container-edit'+id).text(smsCount);
                $('#char_count_container-edit'+id).text(remainingCharacters);
            }else{
                smsCount = Math.ceil((maxCharacters - (remainingCharacters - url_length)) / charactersPerSMS);
                $('#sms_count_container-edit'+id).text(smsCount);
                $('#char_count_container-edit'+id).text(remainingCharacters - url_length);
            }
        }).catch(function(error) {
            console.error('Error calculating URL length:', error);
        });
        if (!updateText) {
            smsCount = Math.ceil(currentLength / charactersPerSMS);
            $('#sms_count_container-edit'+id).text(smsCount);
            $('#char_count_container-edit'+id).text(remainingCharacters);
        }
    });
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

async function checkUrlLength(data, id) {
    var regex = new RegExp(escapeRegExp("$description.url"), "g"); // set variable checked on description '$description.url'
    let array_matches = data.val().match(regex); // How many times is found the variable

    let result = await dataFromCountry($('.country-js-' + id).val());
    let result_decoded = JSON.parse(result);

    var domain_long = 57; // max length from url NO SHORT
    var domain_short = 14; // max length from shortner url with custome domain
    var domain_short_tly = 18; // max length from shortner url
    var selected_length = null;

    if (array_matches) {
        if(result_decoded.length != 0 && result_decoded.api_key != "" && result_decoded.domain == ""){
            selected_length = domain_short_tly;
        }else if(result_decoded.length != 0 && result_decoded.api_key != "" && result_decoded.domain != ""){
            selected_length = domain_short + result_decoded.domain.length;
        }else{
            selected_length = domain_long;
        }
        let full_domain_length = array_matches.length * selected_length;
        return full_domain_length;
    }else{
        return null
    }
}

function dataFromCountry(country_id) {
    var url = '/sms/ajax_check_length_from_country';
    var data = {};
    data.country_id = country_id;
    return $.post(url, data);
}

function updateApiKeyView() {
    $('.domain-js').each(function() {
        var container = $(this).closest('.container-url-js');
        if ($(this).val() != 'sms.aaggnm.com' && $(this).val() != '') {
            container.find('.api-key-js').attr('type', 'text');
        } else {
            container.find('.api-key-js').attr('type', 'hidden');
        }
    });
}