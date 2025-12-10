$(document).ready(function () {
    Calendar.load();
});

var Calendar = (function () {

    var calendarHome = function(){
        var $calendar = $('#calendar');
        var url = $calendar.data('url_events');

        var buttons = $('<div>')
            .append(createButton($.i18n._('General.Yes') + ', ' + $.i18n._('Appointment.Create_visit'), 'uno createVisit'))
            .append(createButton($.i18n._('General.Yes') + ', ' + $.i18n._('Appointment.Create_event'), 'uno createEvent'))
            .append(createButton($.i18n._('General.Cancel'), 'cuatro cancelEvent'));
        var url_create_appointment = '';

        $(document).on('click', '.createVisit', function() {
            url_create_appointment = $('#calendar').data('url_selection');
            swal.clickConfirm();
        });
        $(document).on('click', '.createEvent', function() {
            url_create_appointment = $('#calendar').data('url_selection_event');
            swal.clickConfirm();
        });
        $(document).on('click', '.cancelEvent', function() {
            swal.clickCancel();
        });

        $calendar.fullCalendar({
            lang: language_code,
            defaultView: calendar_view_preferences,
            height: 'auto',
            minTime: '06:00:00',
            maxTime: '24:00:00',
            longPressDelay: 50,
            defaultDate: $('#default_date').val(),
            timeFormat: 'H:mm',
            slotEventOverlap: false,
            eventLimit: 2,
            editable: true,
            eventDurationEditable: true,
            allDaySlot: false,
            selectConstraint:{
                start: '00:00',
                end: '24:00'
            },
            selectable: true,

            eventDrop: function(event, delta, revertFunc, jsEvent, ui, view) {
                if( event.status == 1 || event.status == 7){

                    var url = $('#calendar').data('url_edit_event');
                    var data = {};
                    data.date = event.start.format('DD-MM-YYYY');
                    data.end_date = event.end.format('DD-MM-YYYY');
                    data.start_time = event.start.format('H:mm');
                    data.end_time = event.end.format('H:mm');
                    data.id = event.appointment_id;
                    data.appointment_status_id = event.status;
                    var request = PeticionAjax.post(url,data);
                    request.done(function(data){
                        swal({
                            title: $.i18n._('General.Changes_saved'),
                            type: "success",
                            confirmButtonColor: primary_color,
                        });

                        var datos;
                        datos = JSON.parse(data);
                        event.status = datos.Appointment.appointment_status_id;

                        var elemento ;
                        elemento = $('#A'+event.appointment_id+'');

                        if( event.status == 1 ){
                            event.borderColor = 'rgb(71,134,255)';
                            elemento.css('border-color','rgb(71,134,255)');
                        }
                        else if( event.status == 7 ){
                            event.borderColor = 'rgb(221, 198, 20)';
                            elemento.css('border-color', 'rgb(221, 198, 20)');
                        }

                        $calendar.fullCalendar('refetchEvents');
                    });

                } else {
                    revertFunc();
                }
            },
            eventResize: function(event, delta, revertFunc, jsEvent, ui, view) {
                if( event.status == 1 || event.status == 7){
                    var url = $('#calendar').data('url_edit_event');
                    var data = {};
                    data.date = event.start.format('DD-MM-YYYY');
                    data.end_date = event.end.format('DD-MM-YYYY');
                    data.start_time = event.start.format('H:mm');
                    data.end_time = event.end.format('H:mm');
                    data.id = event.appointment_id;
                    data.appointment_status_id = event.status;
                    var request = PeticionAjax.post(url,data);
                    request.done(function(){
                        swal({
                            title: $.i18n._('General.Changes_saved'),
                            type: "success",
                            confirmButtonColor: primary_color,
                        });
                    });
                    calendarResizeHeight(view.type);
                } else {
                    revertFunc();
                }
            },
            select: function(start, end) {
                if (!($('.is_superAdmin-js')[0])){
                    var view = $calendar.fullCalendar('getView');
                    var user_id = $('#selected-user').val();
                    if(view.type == 'month'){
                        swal({
                            title: $.i18n._('Appointment.Event_date_create',start.format($('#date_format').data('fecha_hora')),end.format('HH:MM')),
                            type: 'warning',
                            html: buttons,
                            showConfirmButton: false,
                            showCancelButton: false
                        }).then(function(result){
                            if(result.value) {
                                var form = $('<form action="' + url_create_appointment + '" method="get">' +
                                    '<input type="hidden" name="date" value="' + start.format('DD-MM-YYYY') + '" />' +
                                    '<input type="hidden" name="user" value="' + user_id + '" />' +
                                    '</form>');
                                $('body').append(form);
                                form.submit();
                            }
                        })
                    } else {
                        swal({
                            title: $.i18n._('Appointment.Event_date_create',start.format($('#date_format').data('fecha_hora')),end.format('HH:mm')),
                            type: 'warning',
                            html: buttons,
                            showConfirmButton: false,
                            showCancelButton: false
                        }).then(function(result){
                            if(result.value) {
                                var form = $('<form action="' + url_create_appointment + '" method="get">' +
                                    '<input type="text" name="date" value="' + start.format('DD-MM-YYYY') + '" />' +
                                    '<input type="text" name="start_time" value="' + start.format('HH:mm') + '" />' +
                                    '<input type="text" name="end_time" value="' + end.format('HH:mm') + '" />' +
                                    '<input type="text" name="user" value="' + user_id + '" />' +
                                    '</form>');
                                $('body').append(form);
                                form.submit();
                            }
                        })
                    }
                }
            },

            header: {
                left: 'agendaDay, agendaWeekLabor, agendaWeek, month,  prev,next',
                center: 'title',
                right: false
            },
            views: {
                agendaWeekLabor: {
                    type: 'agendaWeek',
                    hiddenDays: [0, 6],
                    buttonText: $.i18n._('Appointment.Work_week')
                }
            },
            displayEventEnd: true,
            events: url,
            eventRender: function(event, element) {

                if (event.is_event) {
                    element.find(".fc-time").append(
                        "<a class='fc-trash'>" +
                        "<span class='icon-delete fc-delete-appointment' title='" + $.i18n._('Event.Cancel_event') +"' data-id='" + event.appointment_id + "' data-type='event'></span>" +
                        "</a>"
                    );
                } else {
                    if( event.status == 1 || event.status == 7){
                        element.find(".fc-time").append(
                            "<a class='fc-trash'>" +
                            "<span class='icon-delete fc-delete-appointment' title='" + $.i18n._('Appointment.Cancel_appointment') +"' data-id='" + event.appointment_id + "' data-type='visit'></span>" +
                            "</a>"
                        );
                    }
                }

                if (typeof  event.garage_name !== 'undefined') {
                    if (typeof  event.url_b !== 'undefined') {
                        const titleKey = event.img === 'icon-distributors' ? 'CRM.Distributor_profile' : 'CRM.Garage_profile';
                        element.find(".fc-time").append(
                            "<a href='" + event.url_b + "'>" +
                            "<span class='" + event.img + "' title='" + $.i18n._(titleKey) +"'></span>" +
                            "</a>"
                        );
                    }
                    element.find(".fc-content").append(
                        "<span class='fc-garage'>" + event.garage_name + "</span>");
                }

                if (event.is_event) {
                    element.find(".fc-time").append(
                        "<a href='" + event.url_a + "'>" +
                        "<span class='icon-evemt-create' title='" + $.i18n._('Appointment.Edit_event') + "' ></span>" +
                        "</a>"
                    );
                } else {
                    element.find(".fc-time").append(
                        "<a href='" + event.url_a + "'>" +
                        "<span class='icon-Visit_create' title='" + $.i18n._('Appointment.Edit_visit') + "'></span>" +
                        "</a>"
                    );
                    element.attr('id', 'A'+event.appointment_id);
                }

                return filter(event);
            },
            eventAfterAllRender: function(view ) {
                $('.fc-appointment-button').addClass('button-general tres wi');
                $('.fc-event-button').addClass('button-general tres wi');
                $('.fc-appointment-button').attr('style', 'padding: 0px !important');
                $('.fc-event-button').attr('style', 'padding: 0px !important');

                calendarDeleteAppointment();
                calendarResizeHeight(view.type);
            },
            windowResize: function(view) {
                calendarResizeHeight(view.type);
            },
            viewRender: (function () {
                var lastViewName;
                return function (view) {
                    var view = $('#calendar').fullCalendar('getView');
                    var url = $('#calendar').data('url-preference');

                    if(view.type == 'month'){
                        var data = {
                            '1' : '4'
                        }
                        PeticionAjax.post(url,data);
                    } else if(view.type == 'agendaWeek'){
                        var data = {
                            '1' : '3'
                        }
                        PeticionAjax.post(url,data);

                    } else if(view.type == 'agendaWeekLabor'){
                        var data = {
                            '1' : '2'
                        }
                        PeticionAjax.post(url,data);

                    } else if(view.type == 'agendaDay'){
                        var data = {
                            '1' : '1'
                        }
                        PeticionAjax.post(url,data);
                    }
                }
            })(),
        });

        $('.checkbox-wrapper').on('click',function(){
            $('#calendar').fullCalendar('rerenderEvents');
        });
        $('#selected-user').on('change',function(){
            $('#calendar').fullCalendar('rerenderEvents');
        });
    };

    var calendarAppointments = function(){
        var currentDay = null;
        var $calendar_appointment = $('#calendar-appointments');
        var url_appointment = $calendar_appointment.data('url_events');
        var calendar = $calendar_appointment.fullCalendar({
            lang: language_code,
            defaultView: calendar_view_preferences,
            height: 'auto',
            minTime: '08:00:00',
            maxTime: '22:00:00',
            timeFormat: 'H:mm',
            header: {
                left: 'agendaDay, agendaWeekLabor, agendaWeek, month,  prev,next',
                center: 'title',
                right: false
            },
            views: {
                agendaWeekLabor: {
                    type: 'agendaWeek',
                    hiddenDays: [0, 6],
                    buttonText: $.i18n._('Appointment.Work_week')
                }
            },
            displayEventEnd: true,
            events: url_appointment,
            eventLimit: 2,
            allDaySlot: false,
            eventRender: function(event, element) {
                if(typeof  event.garage_name !== 'undefined'){
                    element.find(".fc-content").append(event.garage_name);
                }
                return(event.user_assigned_id == $('#assigned-to-appointments').val());
            },
            dayClick: function(date) {
                $('#date-visit').val(date.format('DD-MM-YYYY'));
                $('#date-visit').attr('data-date',true);
                $('#appointment-date').val(date.format('DD-MM-YYYY')).trigger('change');
                if(currentDay != null){
                    currentDay.css('background-color', '#fff');
                }
                currentDay = $(this);
                currentDay.css('background-color', '#bf616a');
                $('a.close-modal').trigger('click');
            }
        });

        var openened = false;
        $("#modal-calendar").bind('opened', function() {
            if(!openened){
                calendar.fullCalendar('render');
                openened = true;
            } else {
                calendar.fullCalendar('rerenderEvents');
            }
        });
    };

	var calendarAppointments2 = function(){
        var currentDay = null;
        var $calendar_appointment = $('#calendar-appointments2');
        var url_appointment = $calendar_appointment.data('url_events');
        var calendar = $calendar_appointment.fullCalendar({
            lang: language_code,
            defaultView: calendar_view_preferences,
            height: 'auto',
            minTime: '08:00:00',
            maxTime: '22:00:00',
            timeFormat: 'H:mm',
            header: {
                left: 'agendaDay, agendaWeekLabor, agendaWeek, month,  prev,next',
                center: 'title',
                right: false
            },
            views: {
                agendaWeekLabor: {
                    type: 'agendaWeek',
                    hiddenDays: [0, 6],
                    buttonText: $.i18n._('Appointment.Work_week')
                }
            },
            displayEventEnd: true,
            events: url_appointment,
            eventLimit: 2,
            allDaySlot: false,
            eventRender: function(event, element) {
                    element.find(".fc-content").append(event.garage_name);
            },
            dayClick: function(date) {
                $('#appointment-date_next').val(date.format('DD-MM-YYYY')).trigger('change');

                if(currentDay != null){
                    currentDay.css('background-color', '#fff');
                }
                currentDay = $(this);
                currentDay.css('background-color', '#bf616a');
            }
        });

        var openened = false;
        $("#nextAppointmentModal").bind('opened', function() {
            if(!openened){
                calendar.fullCalendar('render');
                openened = true;
            } else {
                calendar.fullCalendar('rerenderEvents');
            }
        });
    };

    var calendarVisits = function(){
        var currentDay = null;
        var $calendar_visits = $('#calendar-visits');
        var url_appointment = $calendar_visits.data('url_events');
        var calendar = $calendar_visits.fullCalendar({
            lang: language_code,
            defaultView: calendar_view_preferences,
            height: 'auto',
            minTime: '08:00:00',
            maxTime: '22:00:00',
            timeFormat: 'H:mm',
            eventLimit: 2,
            header: {
                left: 'agendaDay, agendaWeekLabor, agendaWeek, month,  prev,next',
                center: 'title',
                right: false
            },
            views: {
                agendaWeekLabor: {
                    type: 'agendaWeek',
                    hiddenDays: [0, 6],
                    buttonText: $.i18n._('Appointment.Work_week')
                }
            },
            displayEventEnd: true,
            events: url_appointment,
            allDaySlot: false,
            eventRender: function(event, element) {
                if(typeof  event.garage_name !== 'undefined'){
                    element.find(".fc-content").append(event.garage_name);
                }
                return(event.user_assigned_id == $calendar_visits.data('user_assigned_id'));
            },
            dayClick: function(date) {
                $('#date-visit').val(date.format('DD-MM-YYYY'));
                $('#appointment-date').val(date.format('DD-MM-YYYY')).trigger('change');
                $('#date-visit').attr('data-date',true);
                $('#appointment-date').val(date.format('DD-MM-YYYY')).trigger('change');
                // $('#appointment-date').trigger('change');
                if(currentDay != null){
                    currentDay.css('background-color', '#fff');
                }
                currentDay = $(this);
                currentDay.css('background-color', '#bf616a');
                $('a.close-modal').trigger('click');
                setTimeout(function(){
                    $('#generate_visit').trigger('click');
                }, 150);
            }
        });

        var openened = false;
        $("#modal-calendar").bind('opened', function() {
            if(!openened){
                calendar.fullCalendar('render');
                openened = true;
            } else {
                calendar.fullCalendar('destroy');
                calendarVisits();
            }
        });
    };

    var filter = function(calEvent){
        var vals = [];
        if(calEvent.status != 6){
            if( calEvent.user_assigned_id == $('#selected-user').val() ){
                $('input:checkbox.check-status:checked').each(function() {
                    vals.push($(this).val());
                });
            }
        } else {
            if( calEvent.user_assigned_id == $('#selected-user').val() ){
                vals.push("6");
            }
        }
        return vals.indexOf(calEvent.status) !== -1;
    };

    var calendarDeleteAppointment = function(){
        $('.fc-delete-appointment').on('click',function(){
            var $element = $(this);
            swal({
                title: $.i18n._('Appointment.Cancel_appointment?'),
                text: $.i18n._('Alert.No_revert'),
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF5745',
                cancelButtonColor: '#d33',
                confirmButtonText: $.i18n._('General.Yes'),
                cancelButtonText: $.i18n._('General.No')
            }).then(function(result){
                if(result.value){
                    if($element.attr('data-type') == 'visit'){
                        var url = '../appointments/cancel_visit/' + $element.data('id');
                        var request = PeticionAjax.post(url);
                        request.done(function(){
                            var $calendar = $('#calendar');
                            $calendar.fullCalendar('destroy');
                            calendarHome();
                        });

                    } else if($element.attr('data-type') == 'event'){
                        var url = '../appointments/delete_event/' + $element.data('id') + '/true';
                        var request = PeticionAjax.post(url);
                        request.done(function(){
                            var $calendar = $('#calendar');
                            $calendar.fullCalendar('destroy');
                            calendarHome();
                        });
                    }
                    location.reload();
                }
            });
        });
    };

    var calendarResizeHeight = function (view) {
        if(view == 'month'){
            $('.fc-content').each(function(){
                $(this).find('.fc-garage').show();
                $(this).find('.fc-time > span').show();
            });
            $('.fc-day-grid-event').each(function(){
                $(this).find('.fc-garage').removeClass('one_line');
                $(this).find('.fc-garage').addClass('ocultar-texto');
            });
        } else {

            $('.fc-time-grid-event').each(function(){
                if($(this).height() < 30 ){
                    $(this).find('.fc-garage').addClass('one_line');
                } else {
                    $(this).find('.fc-garage').removeClass('one_line');
                }
            });
            $('.fc-content').each(function(){
                $(this).find('.fc-garage').removeClass('ocultar-texto');
                if($(this).width() < 110 ){
                    $(this).find('.fc-time > span').hide();
                } else {
                    $(this).find('.fc-time > span').show();
                }

                if($(this).width() > 150){
                    $(this).find('.fc-garage').show();
                } else {
                    $(this).find('.fc-garage').hide();
                }
            });
        }
    }

    var createButton = function (text, css_class) {
        return $('<button style="min-width: 75px;" class="button-general ' + css_class + '">' + text + '</button>');
    }

    return {
        load: function () {
            calendarHome();
            calendarAppointments();
            calendarAppointments2();
            calendarVisits();
        }
    }
})();
