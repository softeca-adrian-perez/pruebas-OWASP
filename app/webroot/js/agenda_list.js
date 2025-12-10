$(document).ready(function () {
    AgendaList.load();
});

var AgendaList = (function () {

    var searchTime = function () {
       var timer;

       $("#name_customer").keyup(function (e) {
           clearTimeout(timer);
           timer = setTimeout(function search() {
               searchAppointment();
           }, 750);
       });
       $("#search_id").keyup(function () {
           clearTimeout(timer);
           timer = setTimeout(function search() {
               searchEvent();
           }, 750);
       });
    };
    var searchAppointment = function () {
            var from_id = 'from=' + $('#from').val();
            var to_id = 'to=' + $('#to').val();
            var appointment_feeling_id = 'appointment_feeling_id=' + $('#appointment_feeling_id').val();
            var appointment_status_id = '';
            var appointment_status = $('#appointment_status_id').val();
            if(appointment_status != null){
                appointment_status.forEach(function(appointment_status_tmp){
                    appointment_status_id += 'appointment_status_id[]=' + appointment_status_tmp + '&';
                });
            }
            var name_customer = 'name_customer=' + $('#name_customer').val();
            var user_assigned_id = 'user_assigned_id=' + $('#user_assigned_id').val()
            var appointment_type_id = 'appointment_type_id=' + $('#appointment_type_id').val();
            var feedback_fill_up_val = 0;
            if ($('#feedback_fill_up').prop('checked') == true) {
                feedback_fill_up_val = 1;
            }
            var feedback_fill_up = 'appointment_fill_up=' + feedback_fill_up_val;
            if (name_customer == "") {
                name_customer = 'all';
            }
            var url = from_id + '&' + to_id + '&' + appointment_feeling_id + '&' + appointment_status_id + user_assigned_id + '&' + name_customer + '&' +
                appointment_type_id + '&' + feedback_fill_up;

            if ($('#requires_follow_up').prop('checked') != undefined) {
                var requires_follow_up_val = 0;
                if ($('#requires_follow_up').prop('checked') == true) {
                    requires_follow_up_val = 1;
                }
                var requires_follow_up = 'requires_follow_up=' + requires_follow_up_val;

                url += '&' + requires_follow_up
            }

            var request = PeticionAjax.post($('#name_customer').data('url') + '?' + url);
            request.done(function (data) {
                $('#agenda-lists').html(data);
                if($('#total_paginator').html() != undefined ){
                    $('#counter-total').html($('#total_paginator').html());
                } else {
                    $('#counter-total').html($('table tbody tr').length);
                }

                $('tr.link-js td').click(function () {
                    if (!$(this).hasClass('no-link-js')) {
                        window.location = $(this).parent('tr').data('url');
                    }
                });
            });
    };

    var load_feeling_select = function (){
        
        $("#appointment_feeling_id").removeClass('select2-multiple');

        var feelings_images = $("#appointment_feeling_id").data('feelings_images');
    
        function formatState (state) {
            if (!state.id) { return state.text; }
            var img = "/img/iconos/" + feelings_images[state.text];
            var $state = $(
                '<img src=' + img + ' style="max-width: 25px; background-color: white; border-radius: 12px;"/>' + ' ' + state.text + '</span>'
            );
            return $state;
        }
        
        $("#appointment_feeling_id").select2({
            templateResult: formatState,
            allowClear: true,
            placeholder: '',
        });

    }

    
    return {
        load: function () {
            searchTime();
            load_feeling_select();
        }
    }
})();