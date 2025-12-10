$(document).ready(function(){
    DistributorActivity.load();
});

var DistributorActivity = (function(){

    var workshopDetails = function(){
        $('#button_add').on('click',function(){
            if($('#workshop_activity_id').val() != ''){
                $('#table_workshop_detail').append(
                    '<tr>' +
                        '<td class="select-option">' + 
                            '<input type="hidden" value="' + $("#workshop_activity_id").val() + '" name="data[DistributorCustomerActivity][workshop_activity_id][]">' +
                            $("#workshop_activity_id option:selected").text() +
                        '</td>' +
                        '<td>' + 
                            '<input type="hidden" value="' + $("#activity_details").val() + '" name="data[DistributorCustomerActivity][activity_details][]">' +
                            $("#activity_details").val() + 
                        '</td>' +
                        '<td>' +
                            '<span class="ion-android-cancel cursor-pointer c-fallo delete-workshop-activity"></span>' +
                        '</td>' +
                    '</tr>'
                );
                $("#workshop_activity_id option[value='" + $("#workshop_activity_id").val() + "']").remove();
                $("#workshop_activity_id").val('').trigger('change');
                $("#activity_details").val('');
                $('.table-tracking').basictable('destroy');
                $('.table-tracking').basictable();
                removeRow();
            } else {
                swal($.i18n._('Distributor.Workshop_activity_empty'),$.i18n._('Distributor.Select_workshop_activity'), 'warning');
            }
        });
    };
    
    var removeRow = function(){
        $('.delete-workshop-activity').off('click').on('click',function(){
            var tr_select = $(this).parent().parent().parent();
            $("#workshop_activity_id").append('<option value="'+ tr_select.find('input').val() + '">' + tr_select.text() + '</option>');
            tr_select.remove();
        });
    };

    var deleteDistributorActivity = function() {
        $('.delete-distributor-activity-js').click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            var element = $(this);
            swal({
                title: element.data('confirmmsg'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: primary_color,
                confirmButtonText: $(this).data('yes'),
                cancelButtonText: $(this).data('no')
            }).then(function (result) {
                if (result.value) {
                    window.location = element.attr('href');
                }
            });
        })
    };

    return {
        load: function(){
            workshopDetails();
            removeRow();
            deleteDistributorActivity();
        }
    }

})();