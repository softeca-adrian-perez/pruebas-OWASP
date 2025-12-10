$(document).ready(function () {
    EmployeeTable.load()
});

var EmployeeTable = (function () {

    var addEmployee = function () {
        $("#add-campaign").on('click', function () {
            if (validateData()) {
                var url = $('#add-campaign').data('add');
                var data = {};
                data['campaign'] = $("#select-campaign").val();
                data['number'] = $("#input-number").val();
                var request = PeticionAjax.post(url, data);
                request.done(function (result) {
                    if (result) {
                        swal({
                            title: $.i18n._('Constants.Message_well_saved'),
                            type: "success"
                        }).then(function (result) {
                            reloadData();
                        });
                    } else {
                        swal({
                            title: $.i18n._('Constants.Message_bad_saved'),
                            type: "error"
                        }).then(function (result) {

                        });
                    }
                });
                request.fail(function () {
                    swal({
                        title: $.i18n._('Constants.Message_bad_saved'),
                        type: "error"
                    }).then(function (result) {

                    });
                })
            } else {
                swal({
                    title: $.i18n._('Config.Empty_field'),
                    type: "error"
                }).then(function (result) {
                });
            }
        });
    };

    var validateData = function () {
        if ($("#select-campaign").val() && $("#input-number").val()) {
            return true;
        } else {
            return false;
        }
    }

    var reloadData = function () {
        $('#select-campaign').empty().trigger('change');
        $("#input-number").val('');
        var url = $('#add-campaign').data('reload');
        var request = PeticionAjax.post(url);
        request.done(function (result) {
            resultado = JSON.parse(result);

            $.each(resultado['CampaignEntry'], function (index, value) {
                info = {
                    id: index,
                    text: value
                };
                var newOption = new Option(info.text, info.id, false, false);
                $('#select-campaign').append(newOption).trigger('change');
            });
            $('#select-campaign').val('').trigger('change');

            var tabla;
            $.each(resultado['GarageCampaign'], function (index, value) {
                tabla += "<tr><td>" + value['CampaignEntry']['name_en'] + "</td><td>" + (value['GarageCampaign']['number'] ? value['GarageCampaign']['number'] : 0) + "</td><td class='btn-hide'><span class='delete-campaign aag-icon-papelera c-fallo cursor-pointer' data-key='" + value['GarageCampaign']['id'] + "'></span></td></tr>";
            });
            $("#tabla-campaign tbody").empty();
            $("#tabla-campaign tbody").append(tabla);

            deleteEmployee();
        });
    }

    var deleteEmployee = function () {
        $(".delete-campaign").on('click', function () {
            var url = $('#add-campaign').data('delete');
            var data = {};
            data['id'] = $(this).data('key');
            swal({
                title: $.i18n._('Config.Confirm_delete_value'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(url, data);
                    var div_tmp = '<div class="row alert-box-js">' +
                        '<div class="medium-12 columns contenedor-ancho cnt-alert">' +
                        '<div data-alert class="alert-box fallo m-bottom-1">' +
                        $.i18n._('Constants.Message_bad_deleted') +
                        '<a href="#" class="close">&times;</a>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    request.done(function (result) {
                        if (result) {
                            reloadData();
                            div_tmp = '<div class="row alert-box-js">' +
                                '<div class="medium-12 columns contenedor-ancho cnt-alert">' +
                                '<div data-alert class="alert-box exito m-bottom-1">' +
                                $.i18n._('Constants.Message_well_deleted') +
                                '<a href="#" class="close">&times;</a>' +
                                '</div>' +
                                '</div>' +
                                '</div>'
                            $("#container").append(div_tmp);
                        } else {
                            $("#container").append(div_tmp);
                        }
                        $('.close').click(function () {
                            $('.alert-box-js').delay(0).slideToggle();
                        });
                    });

                    request.fail(function () {
                        $("#container").append(div_tmp);
                        $('.close').click(function () {
                            $('.alert-box-js').delay(0).slideToggle();
                        });
                    });
                }
            });
        });
    }

    return {
        load: function () {
            addEmployee();
            deleteEmployee();
        }
    }

})();
