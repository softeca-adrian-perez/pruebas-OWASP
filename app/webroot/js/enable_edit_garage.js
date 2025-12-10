$(document).ready(function () {
    EnableEdit.load()
    // $('#edit-btn-disable').click();
});

var EnableEdit = (function() {

    var initialCheck = function() {
        if ($('#edit-btn-disable').data('edit_enabled')) {
            toggleEdit($('#edit-btn-disable'));
        } else {
            loadProvince($('#edit-btn-disable'));
        }
    };

    var enableDisable = function() {

        $('#edit-btn-disable').click(function () {
            toggleEdit(this);
            loadProvince(this);

            PeticionAjax.post($(this).data('url'));
        });
    };

    var toggleEdit = function(item) {
        var user_role_id = $('#aag-region-select').data('role-id');
        var super_admin =  $('#aag-region-select').data('super-admin');
        var manually_created = $('#status-select').data('manually-created');

        if ($(item).val() != 0) {
            $(item).val(0);
            $(item).text('View');
            $('.btn-hide').removeClass('hidden').prop('hidden', false);
            $('.link-text a').each(function() {
                $(this).attr('href', $(this).data('edit'));
            })
            $('#btn-guardar').show();
            $('.pointer-disabled').css({'pointer-events': 'auto', 'cursor': 'pointer'});
            if(user_role_id == super_admin){
                $('.input-disabled-region').removeClass('disabled').prop('disabled', false);
            }
            if(manually_created){
                $('.input-disabled-g-number').removeClass('disabled').prop('disabled', false);
                $('.input-disabled-erpcode').removeClass('disabled').prop('disabled', false);
            }
            $('.input-disabled').addClass('disabled').prop('disabled', false);
            $('.input_disabled_status-js').addClass('disabled').prop('disabled', false);
            $('.priority_contact_checkbox-js').removeClass('disabled').prop('disabled', false);
            $('.priority_contact_checkbox-js').parent().removeClass('disabled');
            $('.clickable_name_contact_staff-js').removeAttr('hidden');
            $('.not_clickable_name_contact_staff-js').attr('hidden', true);
            $(".breadcrumbs").children(":last").text($("#edit-btn-disable").data("edit"));
        }else{
            $(item).val(1);
            $(item).text('Edit');
            $('.btn-hide').addClass('hidden').prop('hidden', true);
            $('.link-text a').each(function() {
                $(this).attr('href', $(this).data('view'));
            })
            $('#btn-guardar').hide();
            $('.pointer-disabled').css({'pointer-events': 'none', 'cursor': 'not-allowed'});
            if(user_role_id == super_admin){
                $('.input-disabled-region').addClass('disabled').prop('disabled', true);
            }
            $('.input-disabled-g-number').addClass('disabled').prop('disabled', true);
            $('.input-disabled-erpcode').addClass('disabled').prop('disabled', true);
            $('.input_disabled_status-js').addClass('disabled').prop('disabled', true);
            $('.input-disabled').addClass('disabled').prop('disabled', true);
            $('.priority_contact_checkbox-js').addClass('disabled').prop('disabled', true);
            $('.priority_contact_checkbox-js').parent().addClass('disabled');
            $('.clickable_name_contact_staff-js').attr('hidden', true);
            $('.not_clickable_name_contact_staff-js').removeAttr('hidden');
            $(".breadcrumbs").children(":last").text($("#edit-btn-disable").data("view"));
        }
    };

	var delete_request = function(){
        $(".delete-request-js").click(function(e){
            e.preventDefault();
            var element = $(this);
            var url = element.data('url');
            var url_redirect = element.data('url_redirect');
            var confirmmsg = element.data('confirmmsg');
            var msg_correct = element.data('msg_correct');
            var msg_bad = element.data('msg_bad');
            swal({
                title: confirmmsg,
                type: "info",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function (result) {
                if (result.value) {
                    var request = PeticionAjax.postJSON(url);
                    request.done(function (data) {
                        if(data.precess == 'true'){
							var div_tmp = '<div class="row alert-box-js">'+
											'<div class="medium-12 columns contenedor-ancho cnt-alert">'+
												'<div data-alert class="alert-box exito m-bottom-1">'+
													$.i18n._('Constants.Message_well_deleted')+
													'<a href="#" class="close">&times;</a>'+
												'</div>'+
											'</div>'+
										'</div>'
							$("#container").append(div_tmp);
							$('.close').click(function(){
								$('.alert-box-js').delay(0).slideToggle();
							});
							window.location.replace(url_redirect);

                        }else{
							var div_tmp = 	'<div class="row alert-box-js">'+
												'<div class="medium-12 columns contenedor-ancho cnt-alert">'+
													'<div data-alert class="alert-box fallo m-bottom-1">'+
														$.i18n._('Constants.Message_bad_deleted')+
														'<a href="#" class="close">&times;</a>'+
													'</div>'+
												'</div>'+
											'</div>';
							$("#container").append(div_tmp);
							$('.close').click(function(){
								$('.alert-box-js').delay(0).slideToggle();
							});
                        }
                    });
                }
            });

        });
    };

    var sendData = function() {
        $("#btn-guardar").on('click',function(e){
            if($('#aag-region-select').length > 0){
                e.preventDefault();
                $('#aag-region-select').attr('disabled', false);
                $('.input-disabled-g-number').attr('disabled', false);
                $('.input-disabled-erpcode').attr('disabled', false);
                $('#form').submit();
            }
        });
    }

    var loadProvince = function(item){

        var province_name = $('#autocomplete-province').data('province-name');
        var city_selected = $('#autocomplete-province').data('city_selected');

        if (province_name != undefined && province_name.length != 0){
            if ($(item).val() == 0) {
                $("#autocomplete-province option[value='"+province_name.id+"']").remove();
                $('#autocomplete-province').val(null).trigger('change');
            }else{
                if ($('#autocomplete-province').find("option[value='" + province_name.id + "']").length) {
                    $('#autocomplete-province').val(province_name.id).trigger('change');
                } else {
                    var newOption = new Option(province_name.text, province_name.id, false, false);
                    $('#autocomplete-province').append(newOption).trigger('change');
                }
                $('#autocomplete-province').val(province_name.id).trigger('change');
            }
        }
        $('#autocomplete-city').val(city_selected).trigger('change');
    }

    return {
        load: function () {
            enableDisable();
            initialCheck();
			delete_request();
            sendData();
        }
    }

})();
