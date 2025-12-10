$(document).ready(function () {
    CustomerActivities.load();
});

var CustomerActivities = (function () {

    var sortCustomerActivities = function(){
        $("#sort-customer-activities").sortable({
            stop: function( event, ui ) {
                var order = 1;
                var data = {};
                $('.item-activity').each(function(){
                    var id_tmp = $(this).data('id');
                    data[id_tmp]= order;
                    order++;
                });
                var url = $('#sort-customer-activities').data('url-sort');
                PeticionAjax.post(url,data);
                updateContent();
            }
        });
    };

    var reOrder = function () {
        var counter = 1;
        $("#sort-customer-activities").find('.order-row').each(function(){
            $(this).html(counter);
            counter++;
        });
    };

    var addCustomerActivity = function(){
        $('#btn_add_activity').on('click',function(event){
            event.preventDefault();
            if($('#customer_activity_name').val() != null){
                var oder_row = null;
                if(isNaN(Number($('.order-row').last().find('span').html()) + 1)){
                    oder_row = 1;
                } else {
                    oder_row = Number($('.order-row').last().find('span').html()) + 1;
                }

                var url = $('#btn_add_activity').data('url');
                var data = {};
                data.customer_activity_id = $('#customer_activity_name').val();
                data.garage_id = $('#garage_id').val();
                data.order = oder_row;
                var request = PeticionAjax.post(url,data);
                request.done(function(garage_customer_activity_id){
                    $('#sort-customer-activities').append(
                        "<tr>" +
                            "<td class='order-row'>" + oder_row + "</td>" + 
                            "<td class='item-activity' " + 
                                "data-id='" + garage_customer_activity_id + "'" + 
                                "data-activity_id='" + $("#customer_activity_name").val() +"'>" + $("#customer_activity_name option:selected").text() + 
                            "</td>" + 
                            "<td>" +
                                "<span class='icon-delete cursor-pointer c-fallo delete-garage-customer-activity'></span>" +
                            "</td>" +
                        "</tr>"
                    );

                    updateContent();
                });
            }
        });
    };

    var availableOptionsCustomerActivities = function(){ 
        $('.table-tracking').basictable('destroy');
        $('.table-tracking').basictable();
        $("#customer_activity_name > option").each(function() {
            var opt_text = $(this).text().trim();
            var opt_value = $(this).val();
            $('.item-activity').each(function(){
                if($(this).find('span').html().trim() == opt_text){
                    $("#customer_activity_name option[value='" + opt_value + "']").remove();
                }
            });
        });
        $("#customer_activity_name").val($("#customer_activity_name option:first").val()).trigger('change');
    };

    var deleteGarageCustomerActivity = function(){
        $('.delete-garage-customer-activity').off('click').on('click',function(){
            var parent_tr = $(this).closest('tr');
            var url = $('#sort-customer-activities').data('url-delete');
            swal({
                title: $.i18n._('CustomerActivity.Confirm_delete'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
				var div_tmp = 	'<div class="row alert-box-js">'+
									'<div class="medium-12 columns contenedor-ancho cnt-alert">'+
										'<div data-alert class="alert-box fallo m-bottom-1">'+
											$.i18n._('Constants.Message_bad_deleted')+
											'<a href="#" class="close">&times;</a>'+
										'</div>'+
									'</div>'+
								'</div>';

                if (result.value) {
                    var data = {};
                    data.garage_customer_activity_id = parent_tr.find('.item-activity').data('id');
                    data.garage_id = $('#garage_id').val();
                    var request = PeticionAjax.post(url,data);

                    request.done(function(){
                        var newOption = new Option(parent_tr.find('.item-activity span').html(), parent_tr.find('.item-activity').data('activity_id'));
                        $('#customer_activity_name').append(newOption).trigger('change');
                        parent_tr.remove();
                        updateContent();
						div_tmp = 	'<div class="row alert-box-js">'+
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
                    });
                }
				else{
					$("#container").append(div_tmp);
					$('.close').click(function(){
						$('.alert-box-js').delay(0).slideToggle();
                    });
                }
            });
        });
    };

    var updateContent = function(){
        reOrder();
        $('.table-tracking').basictable('destroy');
        $('.table-tracking').basictable();
        $("#sort-customer-activities").sortable("destroy");
        $("#sort-customer-activities li").removeClass('ui-state-default');
        $("#sort-customer-activities li span").remove();
        sortCustomerActivities();
        availableOptionsCustomerActivities();
        deleteGarageCustomerActivity();
    };

    return {
        load: function () {
            reOrder();
            sortCustomerActivities();
            addCustomerActivity();
            availableOptionsCustomerActivities();
            deleteGarageCustomerActivity();
        }
    }
})();