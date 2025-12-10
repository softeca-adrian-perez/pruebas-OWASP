<?php
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' . __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('home.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

echo $this->Form->hidden(
    'Role.id',
    array(
        'id' => 'role_id',
        'value' => $this->Session->read('Auth.User.role_id')
    )
);

echo $this->Form->hidden(
    'Role.id',
    array(
        'id' => 'garage_role_id',
        'value' => ConstantsRoles::GARAGE
    )
);
echo $this->Form->hidden(
    'Role.id',
    array(
        'id' => 'distributor_role_id',
        'value' => ConstantsRoles::DISTRIBUTOR
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('General.Home'),
                array(
                    'controller' => 'home',
                    'action' => 'home'
                )
            ),
            __t('General.Home'),
        ));
        ?>
    </div>
</div>
<?php echo $this->element('../Home/Elements/home_bullets'); ?>
<div class="cnt-data fg-0">
    <div class="header-articles">
        <?php echo $this->element('../Home/Elements/home_header'); ?>
    </div>
</div>
<div class="cnt-two-columns aag-margin">
    <?php if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_CALENDAR)) { ?>
        <div class="header-articles aag-radius aag-background-container-color">
            <div style="width: 100%; height: 100%;">
                <div class="aag-title">
                    <?php
                    echo __t('Home.Calendar');
                    if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::CRM)) {
                        echo $this->Html->link(
                            '',
                            array(
                                'controller' => 'dashboard',
                                'action' => 'home'
                            ),
                            array('class' => 'ion-arrow-right-c c-primary')
                        );
                    }
                    ?>
                </div>
                <div class="cnt-checks-legend f-right">
                    <div class="hide_show_check">
                        <div class="f-left cnt-check-color">
                            <span class="checkbox-wrapper cw2 checked">
                                <input type="checkbox" id="check-planned" value="1" class="check-status" checked>
                                <div class="checkbox"></div>
                            </span>
                            <label for="check-planned"><?php echo __t('Appointment.Appointments') ?></label>
                        </div>
                    </div>
                    <div class="hide_show_check">
                        <div class="f-left cnt-check-color">
                            <span class="checkbox-wrapper cw4 checked">
                                <input type="checkbox" id="check-accomplished" value="2" class="check-status" checked>
                                <div class="checkbox"></div>
                            </span>
                            <label for="check-accomplished"><?php echo __t('Appointment.Event') ?></label>
                        </div>
                    </div>
                </div>
                <?php if ($this->Session->read('Auth.User.role_id') == ConstantsRoles::GARAGE) { ?>
                    <div class="p-1" id="calendar" data-url_events="<?php echo Router::url(array('controller' => 'home', 'action' => 'ajax_events_list_garage', $this->Session->read('Auth.User.garage_id'))); ?>"></div>
                <?php } else if ($this->Session->read('Auth.User.role_id') == ConstantsRoles::DISTRIBUTOR) { ?>
                    <div class="p-1" id="calendar" data-url_events="<?php echo Router::url(array('controller' => 'home', 'action' => 'ajax_events_list_distributor',  $this->Session->read('Auth.User.distributor_id'))); ?>"></div>
                <?php } else { ?>
                    <div class="p-1" id="calendar" data-url_events="<?php echo Router::url(array('controller' => 'home', 'action' => 'ajax_events_list',  $this->Session->read('Auth.User.id'))); ?>"></div>
                <?php } ?>
                <div class="alliance_bar"></div>
            </div>
        </div>
    <?php
    }
    if ($this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_ALERT)) {
    ?>
        <div class="header-articles aag-radius aag-background-container-color">
            <div style="width: 100%; height: 100%;">
                <div class="aag-title">
                    <?php
                    echo __t('Home.Alerts');
                    echo $this->Html->link(
                        '',
                        array(
                            'controller' => 'alerts',
                            'action' => 'home',
                        ),
                        array('class' => 'ion-arrow-right-c c-primary')
                    );
                    ?>
                </div>
                <div class="o-auto">
                    <table class="tabla-alert">
                        <thead>
                            <tr>
                                <th><?php echo __t('Alert.Date'); ?></th>
                                <th><?php echo __t('Alert.Message'); ?></th>
                                <th><?php echo __t('Alert.Type'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alerts as $alert) { ?>
                                <tr>
                                    <td>
                                        <?php
                                        $date = new DateTime($alert['Alert']['creation_date']);
                                        echo $date->format('d-m-Y');
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $this->Html->link(
                                            $alert['Alert']['body'],
                                            $alert['Alert']['url'],
                                            array('class' => 'c-blanco')
                                        );
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo h($alert_types[$alert['Alert']['alert_type_id']]); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<div style="display: none;" id="calendarModal" class="reveal-modal background-color-primary"
    data-reveal
    aria-labelledby="modalTitle"
    aria-hidden="true"
    role="dialog"
    data-options="close_on_background_click:true">
    <h1 class="ta-center" id="modal_calendar_title"></h1>
    <table class="table-tracking">
        <thead>
            <tr>
                <th><?php echo __t('General.Name'); ?></th>
                <th class="ta-center"><?php echo __t('Appointment.Start_time'); ?></th>
                <th class="ta-center"><?php echo __t('Appointment.End_time'); ?></th>
                <th class="ta-center"><?php echo __t('Alert.Status'); ?></th>
            </tr>
        </thead>
        <tbody id="modal_calendar_table"></tbody>
    </table>
    <a class="close-modal" data-close aria-label="Close" id="close_modal">&#215;</a>
</div>

</div>

<script>
    <?php if (isset($status_list)) { ?>
        status_list_js = <?php echo json_encode($status_list); ?>;
    <?php } ?>
</script>