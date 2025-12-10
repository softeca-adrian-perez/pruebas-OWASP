<?php
/* @var $this View */

// @todo Figure out why view block cannot be found
$asset_options = array(
//    'plugin' => false,
//    'block' => 'script',
);
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css?v='.Configure::read('VERSION_CACHE'), $asset_options);
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v='.Configure::read('VERSION_CACHE'), $asset_options);
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v='.Configure::read('VERSION_CACHE'), $asset_options);
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' . __l() . '.js?v=' . Configure::read('VERSION_CACHE'), $asset_options);
echo $this->Html->script('/panel/js/widget_calendar.js?v='.Configure::read('VERSION_CACHE'), $asset_options);
?>

<div class="widget-calendar">
    <div class="calendar" data-locale="<?php echo h(__get_locale_jquery_ui()); ?>" data-month_events_url="<?php echo Router::url(
        array(
            'plugin' => false,
            'controller' => 'calendar',
            'action' => 'ajax_widget_get_events_by_date'
        )
    ); ?>"></div>
    <div style="font-size: 13px;">
        <div class="no-events"><?php echo h(__t('Widget.CalendarNoEvents')); ?></div>
        <div class="event-list"></div>
        <div class="more-events"><?php echo h(__t('Widget.CalendarMoreEvents')); ?></div>
    </div>
    <hr style="border-color: #eef0f7; margin: .5rem 0 0;" />
    <div style="display: none;" id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
        <div></div>
        <a class="close-modal" data-close aria-label="Close">&#215;</a>
    </div>
</div>
