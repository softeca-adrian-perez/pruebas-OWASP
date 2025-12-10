
$(function () {
    var url = $('#auto-save').attr('data-url');
    if(url != undefined){
        setInterval(
            function () {
                $.post(
                    url,
                    $("#appointment-form").serialize(),
                    function(result){
                        var dt = new Date();
                        var time = (dt.getHours()<10?'0':'') + dt.getHours() + ":" + (dt.getMinutes()<10?'0':'') + dt.getMinutes() + ":" + (dt.getSeconds()<10?'0':'') + dt.getSeconds();

                        if(result == 'Error-time'){
                            $("#auto-save").html(
                                "<span class='ion-close-circled c-fallo' > " + $.i18n._('Validation.Error_start_end') + ' ' + time +" </span>"
                            );
                        }
                        else if(result == 'Error-date'){
                            $("#auto-save").html(
                                "<span class='ion-close-circled c-fallo' > " + $.i18n._('Validation.Missing_date' )+ ' ' + time +" </span>"
                            );
                        }
                        else if(result === 'Error-customer'){
                            $("#auto-save").html(
                                "<span class='ion-close-circled c-fallo' > " + $.i18n._('Validation.Error_customer') + ' ' + time +" </span>"
                            );
                        }
                        else if(result === false){
                            $("#auto-save").html(
                                "<span class='ion-close-circled c-fallo' > " + $.i18n._('Validation.Update_error') + ' ' + time +" </span>"
                            );
                        }
                        else{
                            $("#auto-save").html(
                                "<span class='ion-checkmark-circled c-exito' > " + $.i18n._('Validation.Last_update') + ' ' + time +" </span>"
                            );
                            $("#appointment-id").val(result);
                        }
                    }
                );
            },
            30000
        );
    } 
});