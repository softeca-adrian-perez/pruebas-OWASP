$(document).ready(function () {
    ValueSuppliersTable.load()
});

var ValueSuppliersTable = (function () {

    var addValue = function () {
        $("#add-value-supplier").on('click', function () {
            if (validateData()) {
                var url = $('#add-value-supplier').data('add');
                var data = {};
                data['value_add_supplier_id'] = $("#select-value-add-supplier").val();
                data['value_add_supplier_type_id'] = $("#select-value-add-supplier-type").val();
                data['to_date'] = $("#to-date").val();
                data['from_date'] = $("#from-date").val();
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
        if ($("#select-value-add-supplier").val() && $("#to-date").val() && $("#from-date").val() && $("#select-value-add-supplier-type").val()) {
            return true;
        } else {
            return false;
        }
    }

    var reloadData = function() {
        $('#select-value-add-supplier').empty().trigger('change');
        $('#select-value-add-supplier-type').empty().trigger('change');
        $("#to-date").val('');
        $("#from-date").val('');
        var url = $('#add-value-supplier').data('reload');
        var request = PeticionAjax.post(url);
        request.done(function (result) {
            resultado = JSON.parse(result);

            $.each(resultado['ValueAddSupplier'], function(index, value) {
                info = {
                    id: index,
                    text: value
                };
                var newOption = new Option(info.text, info.id, false, false);
                $('#select-value-add-supplier').append(newOption).trigger('change');
            });
            $('#select-value-add-supplier').val('').trigger('change');

            $.each(resultado['ValueAddSupplierType'], function(index, value) {
                info = {
                    id: index,
                    text: value
                };
                var newOption = new Option(info.text, info.id, false, false);
                $('#select-value-add-supplier-type').append(newOption).trigger('change');
            });
            $('#select-value-add-supplier-type').val('').trigger('change');

            var tabla;
            $.each(resultado['GarageValueAddSupplier'], function(index, value) {
                tabla += "<tr><td>" + value['ValueAddSupplier']['name_en'] + "</td><td>" + value['GarageValueAddSupplier']['from_date'] + "</td><td>" + value['GarageValueAddSupplier']['to_date'] + "</td><td>" + value['ValueAddSupplierType']['name_en'] + "</td><td class='btn-hide'><span class='delete-value aag-icon-papelera c-fallo cursor-pointer' data-key='" + value['GarageValueAddSupplier']['id'] + "'></span></td></tr>";
            });
            $("#tabla-value-add-supplier tbody").empty();
            $("#tabla-value-add-supplier tbody").append(tabla);

            deleteValue();
        });
    }

    var deleteValue = function () {
        $(".delete-value").on('click', function () {
            let url = $(this).data('delete-url');
            swal({
                title: $.i18n._('Config.Confirm_delete_value'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.post(url);
					var div_tmp = 	'<div class="row alert-box-js">'+
										'<div class="medium-12 columns contenedor-ancho cnt-alert">'+
											'<div data-alert class="alert-box fallo m-bottom-1">'+
												$.i18n._('Constants.Message_bad_deleted')+
												'<a href="#" class="close">&times;</a>'+
											'</div>'+
										'</div>'+
									'</div>';

                    request.done(function (result) {
                        if (result) {
							reloadData();
							div_tmp = '<div class="row alert-box-js">'+
											'<div class="medium-12 columns contenedor-ancho cnt-alert">'+
												'<div data-alert class="alert-box exito m-bottom-1">'+
													$.i18n._('Constants.Message_well_deleted')+
													'<a href="#" class="close">&times;</a>'+
												'</div>'+
											'</div>'+
										'</div>'
							$("#container").append(div_tmp);
                        } else {
							$("#container").append(div_tmp);
                        }
						$('.close').click(function(){
							$('.alert-box-js').delay(0).slideToggle();
						});
                    });

                    request.fail(function () {
						$("#container").append(div_tmp);
						$('.close').click(function(){
							$('.alert-box-js').delay(0).slideToggle();
						});
					});
                };
            });
        });
    };

    return {
        load: function () {
            addValue();
            deleteValue();
        }
    }

})();
