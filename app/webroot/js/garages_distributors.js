$(document).ready(function () {
    Distributors.load();
});

var Distributors = (function () {

    var sortDistributors = function(){
        $("#sort-distributors").sortable({
            stop: function( event, ui ) {
                var order = 1;
                var data = {};
                $('#garages-distributors').empty();
                $('.item-distributor').each(function(){
                    var id_tmp = $(this).data('id');
                    var id_dist_tmp = $(this).data('distributor_id');
                    data[id_tmp]= order;
                    var newOption = new Option(order, id_dist_tmp+' '+id_tmp+' '+order, true, true);
                    $('#garages-distributors').append(newOption).trigger('change');
                    order++;
                });
                updateContent();
            }
        });
    };

    var reOrder = function () {
        var counter = 1;
        $("#sort-distributors").find('.order-row').each(function(){
            $(this).html(counter);
            counter++;
        });
    };

    var addDistributor = function(){
        $('#btn_add_distributor').on('click',function(event){
            event.preventDefault();
            if($('#distributor_name').val() != null){
                var data_ = {};
                data_.distributor_id = $('#distributor_name').val();
                if($(".item-distributor[data-distributor_id='" + data_.distributor_id + "']").length > 0){
                    return;
                }

                var oder_row = null;
                if(isNaN(Number($('.order-row').last().find('span').html()) + 1)){
                    oder_row = 1;
                } else {
                    oder_row = Number($('.order-row').last().find('span').html()) + 1;
                }

                var url_ = $('#btn_add_distributor').data('url_info');

                var request  = PeticionAjax.postJSON(url_,data_);
                request.done(function(data){
                    var mamid = '';
                    if(data.Distributor.MAMID != null && data.Distributor.MAMID != undefined){
                        mamid = data.Distributor.MAMID;
                    }
                    $table_row_1 =
                    "<tr>" +
                        "<td class='order-row'>" + oder_row + "</td>" +
                        "<td class='item-distributor' " +
                            "data-distributor_id='" + $("#distributor_name").val() +"'>" + $("#distributor_name option:selected").text() +
                        "</td>" +
                        "<td class='account_number ta-center'>" + mamid + "</td>" +
                        "<td class='account_number ta-center'>" + data.Distributor.account_number + "</td>";
                    $table_row_2 =
                        "<td class='ta-center btn-hide'>" +
                            "<span class='ion-android-cancel cursor-pointer c-fallo delete-garage-distributor'></span>" +
                        "</td>" +
                    "</tr>"
                    $('#sort-distributors').append( $table_row_1 + $table_row_2 );
                    var newOption = new Option(data.Distributor.name, data.Distributor.id+' '+oder_row, true, true);
                    $('#garages-distributors').append(newOption).trigger('change');
                    updateContent();
                });
            }
        });
    };

    var availableOptionsDistributors = function(){
        $('.table-tracking').basictable('destroy');
        $('.table-tracking').basictable();
        $("#distributor_name > option").each(function() {
            var opt_value = $(this).val();
            $('.item-distributor').each(function(){
                if(opt_value == $(this).data("distributor_id")){
                    $("#distributor_name option[value='" + opt_value + "']").remove();
                }
            });
        });
        $("#distributor_name").val($("#distributor_name option:first").val()).trigger('change');
    };

    var deleteGarageDistributor = function(){
        $('.delete-garage-distributor').off('click').on('click',function(){
            var parent_tr = $(this).closest('tr');
            swal({
                title: $.i18n._('GarageDistributor.Confirm_delete'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No'),
            }).then(function (result) {
                if (result.value) {
                    var data = {};
                    data.garage_distributor_id = parent_tr.find('.item-distributor').data('id');
                    data.distributor_id = parent_tr.find('.item-distributor').data('distributor_id');
                    data.garage_id = $('#garage_id').val();
                    if(data.garage_distributor_id === undefined){
                        // no inserted in db yet, just remove from the "to be inserted" select
                        $('#garages-distributors > option').each(function (){
                            if(this.value.startsWith(data.distributor_id + " ")){
                                $(this).remove();
                                $('#garages-distributors').trigger('change');
                            }
                        });
                    } else {
                        // already inserted in bd, add to the "to be deleted" select
                        var newOption = new Option(data.garage_id, data.garage_distributor_id, true, true);
                        $('#garages-distributors-delete').append(newOption).trigger('change');
                    }
                    // var request = PeticionAjax.post(url,data);
                    // var newOption = new Option(parent_tr.find('.item-distributor span').html(), parent_tr.find('.item-distributor').data('distributor_id'));
                    // $('#distributor_name').append(newOption).trigger('change');
                    parent_tr.remove();
                    updateContent();
                }
            });
        });
    };

    var updateContent = function(){
        reOrder();
        $('.table-tracking').basictable('destroy');
        $('.table-tracking').basictable();
        $("#sort-distributors").sortable("destroy");
        $("#sort-distributors li").removeClass('ui-state-default');
        $("#sort-distributors li span").remove();
        sortDistributors();
        availableOptionsDistributors();
        deleteGarageDistributor();
    };

    return {
        load: function () {
            reOrder();
            sortDistributors();
            addDistributor();
            availableOptionsDistributors();
            deleteGarageDistributor();
        }
    }
})();