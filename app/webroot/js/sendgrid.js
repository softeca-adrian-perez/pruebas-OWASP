$(document).ready(function () {
    Sendgrid.load();
});

var Sendgrid = (function () {
    var addSendgridLicense = function () {
        $('#add_sendgrid_form').on('click', function () {
            var url = $('#add_sendgrid_form').data('url');
            var formData = new FormData($('#form-sendgrid-js')[0]);

            if (checkValuesConfig(formData)) {
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
        });
    };

    var deleteSendgridLicense = function () {
        $(".delete-license-js").on('click', function () {
            let url = $(this).data('delete_url');
            let sendgrid_license_id = $(this).data('sendgrid_license_id');
            let platform_id = $(this).data('platform_id');
            swal({
                title: $.i18n._('Alert.Sure?'),
                html: "<b>" + $.i18n._('Alert.Delete_license_sendgrid?') + "</b> " + $.i18n._('General.Irreversible_action'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
                    var data = {};
                    data['sendgrid_license_id'] = sendgrid_license_id;
                    data['platform_id'] = platform_id;
                    console.log(url);
                    console.log(data);
                    var request = PeticionAjax.post(url, data);
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
                };
            });
        });
    };

    return {
        load: function () {
            addSendgridLicense();
            deleteSendgridLicense();
        }
    }
})();

function edit_sendgrid_conf(comp) {
    var id = comp.id;
    var url = $('#' + id).data('url');
    var formData = new FormData($('#sendgrid-form' + id)[0]);

    if (checkValuesConfig(formData)) {
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
}

function add_sendgrid_email_type_template(comp) {
    var id = comp.id;
    var url = $('#' + id).data('url');
    var email_type_id = $('#' + id).data('email_type_id');
    var platform_id = $('#' + id).data('platform_id');
    var country_id = $('#' + id).data('country_id');
    var aag_region_id = $('#' + id).data('aag_region_id');

    var form_name = '#form-add-sendgrid-email-type-template-js-' + id;
    var formData = new FormData($(form_name)[0]);

    var data = {};
    data['email_type_id'] = email_type_id;
    data['language_id'] = formData.get('data[SendGridEmailTypeTemplate][language_id]');
    data['language_web_id'] = formData.get('data[SendGridEmailTypeTemplate][language_web_id]');
    data['sendgrid_template_id'] = formData.get('data[SendGridEmailTypeTemplate][sendgrid_template_id]');
    data['platform_id'] = platform_id;
    data['country_id'] = country_id;
    data['aag_region_id'] = aag_region_id;

    if (checkValuesSendGridEmailTypeTemplate(data)) {
        var request = PeticionAjax.post(url, data);
        request.done(function (result) {
            if (result) {
                swal({
                    title: $.i18n._('Constants.Message_well_saved'),
                    type: "success"
                }).then(function () {
                    $('a.close-modal').trigger('click');
                    location.reload();
                });
            } else {
                swal({
                    title: $.i18n._('Constants.Message_bad_saved'),
                    type: "error"
                });
            }
        });
        request.fail(function () {
            swal({
                title: $.i18n._('Constants.Message_bad_saved'),
                type: "error"
            });
        });
    }
};

function edit_sendgrid_email_type_template(comp) {
    var id = comp.id;
    var url = $('#' + id).data('url');

    var form_name = '#form-edit-sendgrid-email-type-template-js-' + id;
    var formData = new FormData($(form_name)[0]);

    var data = {};
    data['id'] = formData.get('data[SendGridEmailTypeTemplate][sendgrid_email_type_template_id]');
    data['language_id'] = formData.get('data[SendGridEmailTypeTemplate][language_id]');
    data['language_web_id'] = formData.get('data[SendGridEmailTypeTemplate][language_web_id]');
    data['sendgrid_template_id'] = formData.get('data[SendGridEmailTypeTemplate][sendgrid_template_id]');

    if (checkValuesSendGridEmailTypeTemplate(data)) {
        var request = PeticionAjax.post(url, data);
        request.done(function (result) {
            if (result) {
                swal({
                    title: $.i18n._('Constants.Message_well_saved'),
                    type: "success"
                }).then(function () {
                    $('a.close-modal').trigger('click');
                    location.reload();
                });
            } else {
                swal({
                    title: $.i18n._('Constants.Message_bad_saved'),
                    type: "error"
                });
            }
        });
        request.fail(function () {
            swal({
                title: $.i18n._('Constants.Message_bad_saved'),
                type: "error"
            });
        });
    }
};

// Check if the entered values for SendGridLicenseConfig are correct
function checkValuesConfig(formData) {
    var booleanReturn = false;
    var formDataObj = {};

    formData.forEach(function (value, key) {
        formDataObj[key] = value;
    });

    if (!formData.get('data[SendgridLicenseConfig][country_id]') || formData.get('data[SendgridLicenseConfig][country_id]') === '') {
        swal({
            title: $.i18n._('Email.Country_empty'),
            type: "error"
        });
    } else if (!formData.get('data[SendgridLicenseConfig][name]') || formData.get('data[SendgridLicenseConfig][name]') === '') {
        swal({
            title: $.i18n._('Email.Name_empty'),
            type: "error"
        });
    } else if (!formData.get('data[SendgridLicenseConfig][from_email]') || formData.get('data[SendgridLicenseConfig][from_email]') === '') {
        swal({
            title: $.i18n._('Email.From_email_empty'),
            type: "error"
        });
    } else if (!formData.get('data[SendgridLicenseConfig][api_key]') || formData.get('data[SendgridLicenseConfig][api_key]') === '') {
        swal({
            title: $.i18n._('Email.Api_key_empty'),
            type: "error"
        });
    } else {
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(formData.get('data[SendgridLicenseConfig][from_email]'))) {
            swal({
                title: $.i18n._('Email.Invalid_email'),
                type: "error"
            });
        } else {
            booleanReturn = true;
        }
    }

    return booleanReturn;
}

// Check if the entered values for SendGridEmailTypeTemplate are correct
function checkValuesSendGridEmailTypeTemplate(data) {
    var booleanReturn = false;

    if ((!data['language_id'] || data['language_id'] === '') && (!data['language_web_id'] || data['language_web_id'] === '')) {
        swal({
            title: $.i18n._('Sendgrid.Language_empty'),
            type: "error"
        });
    } else if (!data['sendgrid_template_id'] || data['sendgrid_template_id'] === '') {
        swal({
            title: $.i18n._('Sendgrid.Sendgrid_template_id_empty'),
            type: "error"
        });
    } else {
        booleanReturn = true;
    }

    return booleanReturn;
}