$(document).ready(function () {
    Home.load();
});
var Home = (function () {

    var calendar_home = function(){
        var $calendar = $('#calendar');
        var url = $calendar.data('url_events');
        $('.hide_show_check').hide();

        $calendar.fullCalendar({
            lang: language_code,
            defaultView: 'month',
            nowIndicator: true,
            height: 'auto',
            events: url,
            displayEventTime : false,
            header: {
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            eventAfterAllRender: function(event) {
                element = $('.fc-today');
                element.addClass('circle_fecha');
                $('.fc-content').addClass('ta-center');
            },
            eventRender: function(event, element) {
                if(event.event_type == 1){
                    if(Object.keys(event.CustomerAppointment).length > 1 ){
                        element.find('.fc-content').html('<span class="ion-plus-round"></span> ' + Object.keys(event.CustomerAppointment).length);
                    }
                } else {
                    if(Object.keys(event.CustomerEvent).length > 1 ){
                        element.find('.fc-content').html('<span class="ion-plus-round"></span> ' + Object.keys(event.CustomerEvent).length);
                    }
                }

                if( event.event_type == 1 ){
                    $('#check-planned').closest('.hide_show_check').show();
                } else if( event.event_type == 2 ) {
                    $('#check-accomplished').closest('.hide_show_check').show();
                }

                return filter(event);
            },
            eventClick: function(calEvent, jsEvent, view) {
                let modalCalendar = new Foundation.Reveal($('#calendarModal'));
                modalCalendar.open();
                if(calEvent.event_type == 1){
                    $('#modal_calendar_title').html($.i18n._('Appointment.Appointments') + ' - ' + calEvent.start.format('YYYY-MM-DD'));
                    $('#calendarModal').css('border', '10px rgb(71, 134, 255) solid');
                    var content_tmp = '';
                    $('#modal_calendar_table').html(content_tmp);
                    for( var appointment in calEvent.CustomerAppointment){
                        if(calEvent.CustomerAppointment[appointment].garage_name == undefined){
                            var name_tmp = $.i18n._('Appointment.No_customer_related');
                        } else {
                            var name_tmp = calEvent.CustomerAppointment[appointment].garage_name;
                        }
                        content_tmp = '<tr class="link-js" data-url="/appointments/edit/' + calEvent.CustomerAppointment[appointment].appointment_id + '">';
                        content_tmp += '<td>' + name_tmp + '</td>';
                        content_tmp += '<td class="ta-center">' + moment(calEvent.CustomerAppointment[appointment].start).format('HH:mm') + '</td>';
                        content_tmp += '<td class="ta-center">' + moment(calEvent.CustomerAppointment[appointment].end).format('HH:mm') + '</td>';
                        content_tmp += '<td class="ta-center">' + status_list_js[calEvent.CustomerAppointment[appointment].status] + '</td>';
                        content_tmp += '</tr>';
                        $('#modal_calendar_table').append(content_tmp);
                    }
                } else if(calEvent.event_type == 2) {
                    $('#modal_calendar_title').html($.i18n._('Event.Events') +  ' - ' + calEvent.start.format('YYYY-MM-DD'));
                    $('#calendarModal').css('border', '10px rgb(132, 193, 91) solid');
                    var content_tmp = '';
                    $('#modal_calendar_table').html(content_tmp);
                    for( var event_tmp in calEvent.CustomerEvent){
                        if(calEvent.CustomerEvent[event_tmp].garage_name == undefined || calEvent.CustomerEvent[event_tmp].garage_name == ''){
                            var name_tmp = $.i18n._('Event.No_title');
                        } else {
                            var name_tmp = calEvent.CustomerEvent[event_tmp].garage_name;
                        }
                        content_tmp = '<tr class="link-js" data-url="/appointments/edit_event/' + calEvent.CustomerEvent[event_tmp].appointment_id + '">';
                        content_tmp += '<td>' + name_tmp + '</td>';
                        content_tmp += '<td class="ta-center">' + moment(calEvent.CustomerEvent[event_tmp].start).format('HH:mm') + '</td>';
                        content_tmp += '<td class="ta-center">' + moment(calEvent.CustomerEvent[event_tmp].end).format('HH:mm') + '</td>';
                        content_tmp += '<td class="ta-center">' + $.i18n._('Appointment.Event') + '</td>';
                        content_tmp += '</tr>';
                        $('#modal_calendar_table').append(content_tmp);
                    }
                }
                if($('#role_id').val() != $('#garage_role_id').val() && $('#role_id').val() != $('#distributor_role_id').val()){
                    Tools.loadTr();
                }
                $('.table-tracking').basictable('destroy');
                $('.table-tracking').basictable();
            }
        });

        change_checkbox();
    };

    var filter = function(calEvent){
        var vals = [];

        $('input:checkbox.check-status:checked').each(function() {
            vals.push($(this).val());
        });
        return vals.indexOf(calEvent.event_type) !== -1;
    };

    var change_checkbox = function(){
        $('.checkbox-wrapper').on('click',function(){
            $('#calendar').fullCalendar('rerenderEvents');
        });
    };

    return {
        load: function ($context) {
            calendar_home();
        }
    }
})();