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
                        var events = $calendar.fullCalendar('clientEvents').filter(function (event) {
                            return !event.rendering;
                        });
                        var opening_1 = null;
                        var opening_2 = null;
                        var closed_1 = null;
                        var closed_2 = null;
                        var start_date = null;
                        var event = null;
                        events.forEach(function (event) {
                            if (!event.rendering && (event.start.format('dddd').toLowerCase() == $.i18n._('Garage.Monday').toLowerCase())) {
                                start_date = event.start._d;
                                opening_1 == null ? opening_1 = event.start.format('HH:mm') : opening_2 = event.start.format('HH:mm');
                                closed_1 == null ? closed_1 = event.end.format('HH:mm') : closed_2 = event.end.format('HH:mm');
                            }
                        });
                        events.forEach(function (event) {
                            if (!event.rendering && event.day != 'monday') {
                                $calendar.fullCalendar('removeEvents', event.id);
                            }
                        });

                        $('#slots_inputs-js').find('input').each(function () {
                            if(!$(this).attr('id').includes('monday')){
                                $(this).val('');
                                $(this).attr('disabled', true);
                            }
                        });

                        if (start_date != null) {
                            var date_tmp = new Date();
                            for (var i = 0; i < 7; i++) {
                                date_tmp = addDays(start_date.toISOString().split('T')[0], i);
                                var event_date = date_tmp.getFullYear() + '-' + (("0" + (date_tmp.getMonth() + 1)).slice(-2)) + '-' + ("0" + date_tmp.getDate()).slice(-2);
                                var day_tmp = date_tmp.getDay();
                                if (opening_1 != null && closed_1 != null) {
                                    var inputDayId ='day-' + days[day_tmp] + '-slot-1';
                                    if(document.getElementById(inputDayId) != null){
                                        event = {
                                            start: event_date + ' ' + opening_1,
                                            end: event_date + ' ' + closed_1,
                                            color: '#6a99ff',
                                            id: new Date().getUTCMilliseconds(),
                                            open: opening_1,
                                            closed: closed_1,
                                            day: day_tmp
                                        };
                                        $calendar.fullCalendar('renderEvent', event);
                                        $('#' + inputDayId).attr('disabled', false);
                                    }
                                }
                                if (opening_2 != null && closed_2 != null) {
                                    var inputDayId ='day-' + days[day_tmp] + '-slot-2';
                                    if(document.getElementById(inputDayId) != null){
                                        event = {
                                            start: event_date + ' ' + opening_2,
                                            end: event_date + ' ' + closed_2,
                                            color: '#6a99ff',
                                            id: new Date().getUTCMilliseconds(),
                                            open: opening_2,
                                            closed: closed_2,
                                            day: day_tmp
                                        };
                                        $calendar.fullCalendar('renderEvent', event);
                                        $('#' + inputDayId).attr('disabled', false);
                                    }
                                }
                            }
                        }
                        responsiveOpeningHours();
                    }
                },
                empty_board: {
                    text: $.i18n._('Garage.Empty_board'),
                    click: function () {
                        var events = $calendar.fullCalendar('clientEvents');

                        events.forEach(function (event) {
                            if (!event.rendering) {
                                $calendar.fullCalendar('removeEvents', event.id);
                            }
                        });
                        $('#slots_inputs-js').find('input').each(function () {
                            $(this).val('');
                            $(this).attr('disabled', true);
                        });
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
                if(element[0].nodeName == 'A') {
                    element.append('<span class="removeEvent ion-close-round" data-id="' + event.id + '"></span>');
                }
            },
            eventDrop: function (event, delta, revertFunc) {
                var backgroundEvent = $calendar.fullCalendar('clientEvents').filter(function (event) {
                    return event.rendering;
                });
                var event_start = event.start.format('HH:mm');
                var event_end = event.end.format('HH:mm');
                let event_start_day = event.start.day()

                var isInEventHours = backgroundEvent.some(function (event) {
                    return (
                        event.day === event_start_day &&
                        event_start >= event.open &&
                        event_end <= event.closed
                    );
                });
                if (!isInEventHours ||delta._days) {
                    revertFunc();
                } else {
                    event.open = event.start.format('HH:mm');
                    event.closed = event.end.format('HH:mm');
                    $calendar.fullCalendar('updateEvent', event);
                }
                responsiveOpeningHours();
            },
            select: function (start, end) {
                if (!($('.is_superAdmin-js')[0])) {
                    var current_day = start.format('DD');
                    var day = days[new Date(start).getDay()];
                    var event_start = start.format('HH:mm');
                    var event_end = end.format('HH:mm');
                    var error = false;
                    var total_events = 0;

                    var backgroundEvent = $calendar.fullCalendar('clientEvents').filter(function (event) {
                        return event.rendering;
                    });

                    var isInEventHours = backgroundEvent.some(function (event) {
                        let dayNumber = event.day
                        if (dayNumber == 7) {
                            day = days[0]
                            dayNumber = 0
                        }
                        return (
                            dayNumber === start.day() &&
                            event_start >= event.open &&
                            event_end <= event.closed
                        );
                    });
                    if (!isInEventHours) {
                        error = true;
                        swal($.i18n._('Garage.Error_planner_times'), '', 'question');
                    }

                    var openingHourEvent = $calendar.fullCalendar('clientEvents').filter(function (event) {
                        return !event.rendering;
                    });

                    openingHourEvent.forEach(function (event) {
                        if (!event.rendering && event.start.format('DD') == current_day) {
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
                        } else if (open_1.length > 0 && open_2.length == 0) {
                            max_events = 2;
                        } else if (open_1.length > 0 || open_2.length > 0) {
                            max_events = 1;
                        }

                        let inputDayId = '';
                        if (total_events == 0 || (total_events == 1 && $('#day-' + day + '-slot-2').attr('disabled') !== 'disabled')) {
                            inputDayId ='#day-' + day + '-slot-1';
                        } else if (total_events == 1 && $('#day-' + day + '-slot-1').attr('disabled') !== 'disabled') {
                            inputDayId ='#day-' + day + '-slot-2';
                        }

                        $(inputDayId).prop('disabled', false);

                        if (total_events < max_events) {
                            var event = {
                                start: start.format('YYYY-MM-DD HH:mm'),
                                end: end.format('YYYY-MM-DD HH:mm'),
                                color: '#6a99ff',
                                id: new Date().getUTCMilliseconds(),
                                open: start.format('HH:mm'),
                                closed: end.format('HH:mm'),
                                day: start.day(),
                                inputDayId : inputDayId,
                            };
                            $calendar.fullCalendar('renderEvent', event);
                        } else {
                            swal($.i18n._('Garage.Two_openings'), '', 'question');
                        }
                        var slotEventsActives = $calendar.fullCalendar('clientEvents').filter(function (event) {
                            return !event.rendering;
                        });
                        disableSlots(slotEventsActives, day, start.day());
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
                    $calendar.fullCalendar('clientEvents').filter(function (event) {
                        return !event.rendering;
                    });
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

                var daysInput = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                for (var i = 0; i < 7; i++) {
                    $('.fc-content-skeleton>table>tbody>tr>td:nth-child(' + (i + 2) + ') div.fc-event-container a:first-child span.removeEvent').attr('data-input-id', '#day-' + daysInput[i] + '-slot-1');
                    $('.fc-content-skeleton>table>tbody>tr>td:nth-child(' + (i + 2) + ') div.fc-event-container a:nth-child(2) span.removeEvent').attr('data-input-id', '#day-' + daysInput[i] + '-slot-2');
                }
            }
        });
    };

    var saveOpeningHours = function(){
        $('#btn-guardar').on('click',function(e){
            e.preventDefault();
            var $calendar = $('#calendar');
            var events = $calendar.fullCalendar('clientEvents').filter(function (event) {
                return !event.rendering;
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
            days.forEach(function(day){
                $('#' + day + '-open-1').val('');
                $('#' + day + '-closed-1').val('');
                $('#' + day + '-open-2').val('');
                $('#' + day + '-closed-2').val('');
            });
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


                if(second_openings[event.day] == false){
                    open_1.val(event.open);
                    closed_1.val(closeHour);
                    second_openings[event.day] = true;
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
            var events = $calendar.fullCalendar('clientEvents').filter(function (event) {
                return !event.rendering;
            });
            logAndStore(events);
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
                var open_1 = $('#' + days[event.day] + '-open-1');
                var closed_1 = $('#' + days[event.day] + '-closed-1');
                var open_2 = $('#' + days[event.day] + '-open-2');
                var closed_2 = $('#' + days[event.day] + '-closed-2');
                if(second_openings[event.day] == false){
                    open_1.val(event.open);
                    closed_1.val(event.closed);
                    second_openings[event.day] = true;
                } else {
                    if(event.open > open_1.val()){
                        open_2.val(event.open);
                        closed_2.val(event.closed);
                    } else {
                        open_2.val(open_1.val());
                        closed_2.val(closed_1.val());
                        open_1.val(event.open);
                        closed_1.val(event.closed);
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

    var disableSlots = function(calendar, day_name, day_number) {
        array_calendar = [];
        calendar.forEach(function (calendars, index) {
            if (calendars.day == day_number) {
                array_calendar.push(calendars);
            }
        });
        if (array_calendar.length == 1) {
            $('#day-' + day_name + '-slot-1').prop("disabled", false);
        } else if (array_calendar.length == 2) {
            $('#day-' + day_name + '-slot-2').prop("disabled", false);
        }
    };

    $(document).on('click', '.removeEvent', function () {
        var span = $(this).data('input-id');
        dividedText = span.split('-');  // [#day, monday, slot, 1]
        //if there are two enable slot in the same col and you try to remove slot 1 -> now, slot 2 is slot 1
        if(dividedText[3] == 1 && $('#day-' + dividedText[1] + '-slot-2').attr('disabled') !== 'disabled'){
            $('#day-' + dividedText[1] + '-slot-2').prop("disabled", true);
            $('#day-' + dividedText[1] + '-slot-1').val($('#day-' + dividedText[1] + '-slot-2').val());
        } else {
            $(span).prop("disabled", true);
            var eventId = $(this);
            $('#calendar').fullCalendar('removeEvents', eventId);
        }
        disabledHours();
    });


    var disabledHours = function () {
        $('.cnt-max-date-per-day').find('input').each(function() {
            if ($(this).attr('disabled')) {
                $(this).val('');
            }
        });
    }

    return {
        load: function () {
            disabledHours();
            loadOpeningHours();
            saveOpeningHours();
        }
    }
})();