/* jshint strict:false */
/* global jQuery, jQuery.fullCalendar, jQuery.fullCalendar.moment, Tools, PeticionAjax */

jQuery(function($){
    var $calendar = $(".widget-calendar .calendar");
    var $legend, $noEvents, $eventList, $moreEvents;
    var dayCells = {}, resetDayCells = true;
    var moreEventsTemplate;
    var settings;
    if($calendar.length===0){
        return;
    }

    settings = {
        locale: $calendar.data('locale'),
        dataURL: $calendar.data('month_events_url'),
        maxEvents: 3,
        hasEventsClass: "has-events",
        eventListDataKey: "gnmcountry-events",
        dateKeyFormat: "YYYY-MM-DD"
    };
    $legend = $calendar.closest(".widget-calendar").find(".legend");
    $noEvents = $calendar.closest(".widget-calendar").find(".no-events");
    $eventList = $calendar.closest(".widget-calendar").find(".event-list");
    $moreEvents = $calendar.closest(".widget-calendar").find(".more-events");

    moreEventsTemplate = $moreEvents.text();


    /**
     * @param {object} rawData
     * @constructor
     */
    function CalendarEvent(rawData){
        if(rawData.type !='RM'){
            var $link = $("<a></a>")
                .attr("href", rawData.url)
                .text(rawData.title)
                .addClass(rawData.class_js)
                .attr("data-open", rawData.data_reveal_id);
        } else {
            var $link = $("<a></a>")
                .text(rawData.title);
        }
        this.date = rawData.start;
        this.url_rm = rawData.url_rm;
        this.html = $('<div class="event"></div>')
            .css({backgroundColor: rawData.backgroundColor})
            .append($link)
            .wrapAll('<div></div>').parent();
    }


    /**
     * @param {array} array
     */
    function fisherYatesShuffle(array){
        var i = array.length;
        while(i--){
            var j = Math.floor(Math.random() * (i + 1));
            var tmp = array[i];
            array[i] = array[j];
            array[j] = tmp;
        }
    }


    /**
     * @param {jQuery} $cell
     */
    function displayEvents($cell){
        var events, moreEventsHTML;

        if( $cell.hasClass(settings.hasEventsClass) ){
            events = $cell.data(settings.eventListDataKey);
            $eventList.html("");
            $.each(events, function(index, event){
                
                if(index<settings.maxEvents){
                    $eventList.append(event.html);
                    if(event.url_rm !== undefined){
                        $('.event').off('click').on('click', function(){
                            var win = window.open(event.url_rm, '_blank');
                            win.focus();
                        });
                    } 
                }
                
            });
            Tools.loadModalView();
            $noEvents.hide();
            $legend.show();
            $eventList.show();

            if(events.length>settings.maxEvents){
                moreEventsHTML = new Option(moreEventsTemplate).innerHTML
                    .replace("%s", "<span>" + (events.length-settings.maxEvents) + "</span>");
                $moreEvents.html(moreEventsHTML).show();
            }else{
                $moreEvents.hide();
            }
        }else{
            $moreEvents.hide();
            $eventList.hide();
            $legend.hide();
            $noEvents.show();
        }
    }


    function loadTodayEvents(){
        var today = $.fullCalendar.moment().format(settings.dateKeyFormat);
        if( dayCells[today] ){
            displayEvents(dayCells[today]);
        }
    }


    /**
     * @param {bool} isLoading
     */
    function loadingHandler(isLoading/*, view*/){
        if(isLoading){
            PeticionAjax.mostrarCargando();
        }else{
            PeticionAjax.ocultarCargando();
        }
    }


    function viewRenderHandler(/*view, element*/){
        resetDayCells = true;
        $eventList.hide();
        $legend.hide();
        $noEvents.show();
    }


    /**
     * @param {Moment} date
     * @param {jQuery} cell
     */
    function dayRenderHandler(date, cell){
        if(resetDayCells){
            dayCells = {};
            resetDayCells = false;
        }
        dayCells[date.format(settings.dateKeyFormat)] = cell;
    }


    /**
     * @param {Moment} date
     */
    function dayClickHandler(date/*, jsEvent, view*/){
        displayEvents(dayCells[date.format(settings.dateKeyFormat)]);
    }


    /**
     * @param {Moment} start
     * @param {Moment} end
     * @param timezone
     * @param callback
     */
    function eventsHandler(start, end, timezone, callback){
        var params = {
            start: start.format(settings.dateKeyFormat),
            end: end.format(settings.dateKeyFormat)
        };
        $.get(settings.dataURL, params, function(data){
            fisherYatesShuffle(data);
            $.map(data, function(eventData){
                var newEvent = new CalendarEvent(eventData);
                var $cell = dayCells[newEvent.date];
                var currentEvents;

                if(typeof $cell!=="undefined"){
                    currentEvents = $cell.data(settings.eventListDataKey) || [];
                    currentEvents.push(newEvent);
                    $cell.data(settings.eventListDataKey, currentEvents);
                    $cell.addClass(settings.hasEventsClass);
                }

                loadTodayEvents();
            });
            callback([]);
        }, 'json');
    }


    $calendar.fullCalendar({
        theme: false, // Theme totally ruins layout
        lang: settings.locale,
        timeFormat: 'H:mm',
        nextDayThreshold: "00:00",
        header: {
            left: '',
            center: 'title',
            right: 'prev,next'
        },
        height: 240,
        loading: loadingHandler,
        viewRender: viewRenderHandler,
        dayRender: dayRenderHandler,
        dayClick: dayClickHandler,
        events: eventsHandler
    });
});
