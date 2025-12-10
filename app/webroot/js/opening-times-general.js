$(document).ready(function () {
    OpeningHours.load();
});

var OpeningHours = (function () {
    var days = [
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday'
    ];

    var loadOpeningHours = function () {
        var $calendar = $('#calendar');
        var url = $calendar.data('url_events');
        $calendar.fullCalendar({
            lang: language_code,
            defaultView: 'agendaWeek',
            height: '100%',
            slotDuration: '00:15:00',
            minTime: '00:00:00',
            maxTime: '23:59:00',
            eventOverlap: false,
            editable: true,
            eventDurationEditable: true,
            allDaySlot: false,
            selectable: true,
            events: url,
            customButtons: {
                as_monday: {
                    text: $.i18n._('Garage.Mark_as_monday'),
                    click: function () {
                        var events = $calendar.fullCalendar('clientEvents');
                        var opening_1 = null;
                        var opening_2 = null;
                        var closed_1 = null;
                        var closed_2 = null;
                        var start_date = null;
                        var event = null;
                        events.forEach(function (event) {
                            if (event.start.format('dddd').toLowerCase() == $.i18n._('Garage.Monday').toLowerCase()) {
                                start_date = event.start._d;
                                opening_1 == null ? opening_1 = event.start.format('HH:mm') : opening_2 = event.start.format('HH:mm');
                                closed_1 == null ? closed_1 = event.end.format('HH:mm') : closed_2 = event.end.format('HH:mm');
                            }
                        });
                        $calendar.fullCalendar('removeEvents');
                        if (start_date != null) {
                            var date_tmp = new Date();
                            for (var i = 0; i < 5; i++) {
                                date_tmp = addDays(start_date.toISOString().split('T')[0], i);
                                var event_date = date_tmp.getFullYear() + '-' + (("0" + (date_tmp.getMonth() + 1)).slice(-2)) + '-' + ("0" + date_tmp.getDate()).slice(-2);
                                if (opening_1 != null && closed_1 != null) {
                                    event = {
                                        start: event_date + ' ' + opening_1,
                                        end: event_date + ' ' + closed_1,
                                        color: '#6a99ff',
                                        id: new Date().getUTCMilliseconds(),
                                        open: opening_1,
                                        closed: closed_1,
                                        day: date_tmp.getDay()
                                    };
                                    $calendar.fullCalendar('renderEvent', event);
                                }
                                if (opening_2 != null && closed_2 != null) {
                                    event = {
                                        start: event_date + ' ' + opening_2,
                                        end: event_date + ' ' + closed_2,
                                        color: '#6a99ff',
                                        id: new Date().getUTCMilliseconds(),
                                        open: opening_2,
                                        closed: closed_2,
                                        day: date_tmp.getDay()
                                    };
                                    $calendar.fullCalendar('renderEvent', event);
                                }
                            }
                        }
                        responsiveOpeningHours();
                    }
                },
                empty_board: {
                    text: $.i18n._('Garage.Empty_board'),
                    click: function () {
                        $calendar.fullCalendar('removeEvents');
                    }
                }
            },
            header: {
                left: '',
                center: '',
                right: 'as_monday empty_board'
            },
            eventAllow: function(dropInfo, draggedEvent) {
                return ((dropInfo.start.format('DD') === dropInfo.end.format('DD')) || dropInfo.end.format('HH:mm') == '00:00');
            },
            selectAllow: function(selectInfo){
                return (selectInfo.start.format('DD') == selectInfo.end.format('DD') || (selectInfo.end.format('HH:mm') == '00:00' && selectInfo.end.format('DD') == parseInt(selectInfo.start.format('DD')) + 1 ));
            },
            eventRender: function (event, element) {
                element.append('<span class="removeEvent ion-close-round" data-id="' + event.id + '"></span>');
            },
            eventDrop: function (event, delta, revertFunc) {
                if (delta._days) {
                    revertFunc();
                } else {
                    event.open = event.start.format('HH:mm');
                    event.closed = roundOff(new Date(event.end.format('YYYY-MM-DD HH:mm')));
                    $calendar.fullCalendar('updateEvent', event);
                }
                responsiveOpeningHours();
            },
            select: function (start, end) {
                var current_day = start.format('DD');
                var day = days[new Date(start).getDay()];
                var event_start = start.format('HH:mm');
                var event_end = end.format('HH:mm');
                var error = false;
                var total_events = 0;
                var events = $calendar.fullCalendar('clientEvents');
                events.forEach(function (event) {
                    if (event.start.format('DD') == current_day) {
                        total_events++;
                        if (event_end > event.start.format('HH:mm') && event_start < event.start.format('HH:mm')) {
                            error = true;
                        }
                        if (event_start < event.end.format('HH:mm') && event_start > event.start.format('HH:mm')) {
                            error = true;
                        }
                    }
                });
                if (!error) {
                    var garage_network_id = $('#GarageNetworkId');
                    var open_1 = $('#' + day + '-open-1');
                    var open_2 = $('#' + day + '-open-2');

                    var max_events = garage_network_id.val() !== undefined ? 0 : 2;
                    if (open_1.length > 0 && open_2.length > 0) {
                        max_events = 2;
                    } else if (open_1.length > 0 || open_2.length > 0) {
                            max_events = 1;
                    }
                    if (total_events < max_events) {
                        var event = {
                            start: start.format('YYYY-MM-DD HH:mm'),
                            end: end.format('YYYY-MM-DD HH:mm'),
                            color: '#6a99ff',
                            id: new Date().getUTCMilliseconds(),
                            open: start.format('HH:mm'),
                            closed: end.format('HH:mm'),
                            day: start.day()
                        };
                        $calendar.fullCalendar('renderEvent', event);
                    } else {
                        swal($.i18n._('Garage.Two_openings'), '', 'question');
                    }
                }
                responsiveOpeningHours();
            },
            eventResize: function (event, delta, revertFunc) {
                if (delta._days) {
                    revertFunc();
                } else {
                    event.open = event.start.format('HH:mm');
                    event.closed = event.end.format('HH:mm');
                    $calendar.fullCalendar('updateEvent', event);
                }
                responsiveOpeningHours();
            },
            eventAfterAllRender: function () {
                $('.fc-widget-header .fc-mon').html($.i18n._('Garage.Monday'));
                $('.fc-widget-header .fc-tue').html($.i18n._('Garage.Tuesday'));
                $('.fc-widget-header .fc-wed').html($.i18n._('Garage.Wednesday'));
                $('.fc-widget-header .fc-thu').html($.i18n._('Garage.Thursday'));
                $('.fc-widget-header .fc-fri').html($.i18n._('Garage.Friday'));
                $('.fc-widget-header .fc-sat').html($.i18n._('Garage.Saturday'));
                $('.fc-widget-header .fc-sun').html($.i18n._('Garage.Sunday'));
                $('.removeEvent').off('click').on('click', function () {
                    $calendar.fullCalendar('removeEvents', $(this).data('id'));
                });
                $('div#calendar.fc-ltr .fc-content span').attr('style', 'color: #fff !important');
                $('div#calendar.fc-ltr a.fc-time-grid-event').css('color: #fff');
                $("div#calendar a").hover(function () {
                    $(this).find('.removeEvent').css("color", "#fff");
                });
                $("div#calendar a .removeEvent").hover(function () {
                        $(this).css("color", "#FF0000");
                    }, function () {
                        $(this).css("color", "#FFF");
                    }
                );
                $('#calendar').find('button').addClass('button-general conSVG tres');

                responsiveOpeningHours();
            }
        });
    };

    var saveOpeningHours = function(){
        $('#btn-guardar').on('click',function(e){
            e.preventDefault();
            var $calendar = $('#calendar');
            var events = $calendar.fullCalendar('clientEvents');
            days.forEach(function(day){
                $('#' + day + '-open-1').val('');
                $('#' + day + '-closed-1').val('');
                $('#' + day + '-open-2').val('');
                $('#' + day + '-closed-2').val('');
            });
            var second_openings = [
                false,
                false,
                false,
                false,
                false,
                false,
                false
            ];
            events.forEach(function(event){
                let dayNumber = event.day
                let closeHour = event.closed
                if (dayNumber == 7) {
                    dayNumber = 0
                }
                if (closeHour == '00:00') {
                    closeHour = '23:59'
                }

                var open_1 = $('#' + days[dayNumber] + '-open-1');
                var closed_1 = $('#' + days[dayNumber] + '-closed-1');
                var open_2 = $('#' + days[dayNumber] + '-open-2');
                var closed_2 = $('#' + days[dayNumber] + '-closed-2');
                if(second_openings[dayNumber] == false){
                    open_1.val(event.open);
                    closed_1.val(closeHour);
                    second_openings[dayNumber] = true;
                } else {
                    if(event.open > open_1.val()){
                        open_2.val(event.open);
                        closed_2.val(closeHour);
                    } else {
                        open_2.val(open_1.val());
                        closed_2.val(closed_1.val());
                        open_1.val(event.open);
                        closed_1.val(closeHour);
                    }
                }
            });
            $('#opening-times-form').submit();
        });
        $('[name=request_changes]').on('click',function(e){
            var days = [
                'sunday',
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday'
            ];
            var $calendar = $('#calendar');
            var events = $calendar.fullCalendar('clientEvents');
            days.forEach(function(day){
                $('#' + day + '-open-1').val('');
                $('#' + day + '-closed-1').val('');
                $('#' + day + '-open-2').val('');
                $('#' + day + '-closed-2').val('');
            });
            var second_openings = [
                false,
                false,
                false,
                false,
                false,
                false,
                false
            ];
            events.forEach(function(event){
                let dayNumber = event.day
                let closeHour = event.closed
                if (dayNumber == 7) {
                    dayNumber = 0
                }
                if (closeHour == '00:00') {
                    closeHour = '23:59'
                }
                var open_1 = $('#' + days[dayNumber] + '-open-1');
                var closed_1 = $('#' + days[dayNumber] + '-closed-1');
                var open_2 = $('#' + days[dayNumber] + '-open-2');
                var closed_2 = $('#' + days[dayNumber] + '-closed-2');
                if(second_openings[dayNumber] == false){
                    open_1.val(event.open);
                    closed_1.val(closeHour);
                    second_openings[dayNumber] = true;
                } else {
                    if(event.open > open_1.val()){
                        open_2.val(event.open);
                        closed_2.val(closeHour);
                    } else {
                        open_2.val(open_1.val());
                        closed_2.val(closed_1.val());
                        open_1.val(event.open);
                        closed_1.val(closeHour);
                    }

                }
            });
        });
    };

    var responsiveOpeningHours = function(){
        responsive();
        $(window).resize(function () {
            responsive();
        });
    };

    var responsive = function(){
        $('div#calendar.fc-ltr a.fc-time-grid-event>div.fc-content>div.fc-time>span').css('font-size', '1.25em');
        if ($(window).width() < 1000) {
            $('.fc-content .fc-time span').hide();
        } else {
            $('.fc-content .fc-time span').show();
        }
        if ($(window).width() < 600) {
            $('.fc-day-header').each(function () {
                $(this).html($(this).html().substr(0, 2));
            });
        }
    };

    var addDays = function(date, days) {
        var result = new Date(date);
        result.setDate(result.getDate() + days);
        return result;
    };

    var roundOff = function(date) {
        let milliseconds = 1000 * 60 * 5;
        let roundedDate = new Date(Math.round(date.getTime() / milliseconds) * milliseconds);
        return [
            roundedDate.getHours().toString().padStart(2, "0"),
            roundedDate.getMinutes().toString().padStart(2, "0")
        ].join(":")
    }

    return {
        load: function () {
            loadOpeningHours();
            saveOpeningHours();
        }
    }
})();