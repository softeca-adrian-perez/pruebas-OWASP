<?php
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' .  __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('appointments_calendar.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('CRM.Crm'),
                array(
                    'controller' => 'dashboard',
                    'action' => 'home'
                )
            ),
            __t('Home.Calendar'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        echo $this->Html->link(
            '<span class="icon-agenda_list"></span>' . __t('Appointment.Agenda_list'),
            array(
                'controller' => 'appointments',
                'action' => 'home_list'
            ),
            array(
                'escape' => false,
                'class' => 'aag-button medium one'
            )
        );
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
            echo $this->Html->link(
                '<span class="icon-Visit_create"></span>' . __t('Appointment.Create_visit'),
                array(
                    'controller' => 'appointments',
                    'action' => 'add',
                    '?' => array(
                        'user' => CakeSession::read('Auth.User.id')
                    )
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium one'
                )
            );
            echo $this->Html->link(
                '<span class="icon-evemt-create"></span>' . __t('Appointment.Create_event'),
                array(
                    'controller' => 'appointments',
                    'action' => 'add_event',
                    CakeSession::read('Auth.User.id')
                ),
                array(
                    'escape' => false,
                    'class' => 'aag-button medium one'
                )
            );
        }
        ?>
    </div>
</div>
<?php if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) { ?>
    <div class="is_superAdmin-js"></div>
<?php } ?>

<div class="cnt-data aag-padding">
    <?php
    if (isset($this->request->query['date'])) {
        echo $this->Form->hidden(
            'default_date',
            array(
                'id' => 'default_date',
                'value' => Fecha::toFormatoBd($this->request->query['date'])
            )
        );
    }
    echo $this->Form->hidden(
        '',
        array(
            'id' => 'date_format',
            'data-fecha_hora' => 'DD-MM-YYYY HH:mm',
            'data-fecha' => 'DD-MM-YYYY',
        )
    );
    ?>
    <?php echo $this->element('Comun/loader'); ?>
    <div class="aag-title">
        <?php echo __t('Home.Calendar'); ?>
    </div>
    <div class="">
        <div class="background-color-primary m-bottom-1 o-x-scroll" style="padding-top: 0.9375rem;">
            <div class="row">
                <?php if (count($users) > 1) { ?>
                    <div class="columns medium-3 cnt-select-calendar">
                        <?php
                        echo $this->Form->input(
                            'Select_User',
                            array(
                                'label' => __t('Appointment.Assign_to'),
                                'type' => 'select',
                                'class' => 'select2-multiple',
                                'options' => $users,
                                'id' => 'selected-user',
                                'default' => isset($this->request->query['user']) ? $this->request->query['user'] : CakeSession::read('Auth.User.id')
                            )
                        );
                        ?>
                    </div>
                    <div class="columns medium-9 p-form">
                    <?php } else {
                    echo $this->Form->hidden(
                        'Select_User',
                        array(
                            'id' => 'selected-user',
                            'default' => isset($this->request->query['user']) ? $this->request->query['user'] : CakeSession::read('Auth.User.id')
                        )
                    ); ?>
                        <div class="columns medium-12 p-form">
                        <?php } ?>
                        <div class="cnt-checks-legend f-right">
                            <div class="f-left cnt-check-color">
                                <span class="checkbox-wrapper cw2 checked">
                                    <input type="checkbox" id="check-planned" value="<?php echo ConstantsStatusAppointments::PLANNED; ?>" class="check-status" checked>
                                    <div class="checkbox"></div>
                                </span>
                                <label for="check-planned"><?php echo __t('CRM.Planned') ?></label>
                            </div>
                            <div class="f-left cnt-check-color">
                                <span class="checkbox-wrapper cw6 checked">
                                    <input type="checkbox" id="check-pending" value="<?php echo ConstantsStatusAppointments::PENDING; ?>" class="check-status" checked>
                                    <div class="checkbox"></div>
                                </span>
                                <label for="check-pending"><?php echo __t('CRM.Pending') ?></label>
                            </div>
                            <div class="f-left cnt-check-color">
                                <span class="checkbox-wrapper cw4 checked">
                                    <input type="checkbox" id="check-accomplished" value="<?php echo ConstantsStatusAppointments::ACCOMPLISHED; ?>" class="check-status" checked>
                                    <div class="checkbox"></div>
                                </span>
                                <label for="check-accomplished"><?php echo __t('CRM.Accomplished') ?></label>
                            </div>
                                <div class="f-left cnt-check-color">
                                    <span class="checkbox-wrapper cw1 checked">
                                        <input type="checkbox" id="check-rescheduled" value="<?php echo ConstantsStatusAppointments::RESCHEDULED; ?>" class="check-status" checked>
                                        <div class="checkbox"></div>
                                    </span>
                                    <label for="check-rescheduled"><?php echo __t('CRM.Rescheduled') ?></label>
                                </div>
                            <div class="f-left cnt-check-color">
                                <span class="checkbox-wrapper cw3">
                                    <input type="checkbox" id="check-cancelled" value="<?php echo ConstantsStatusAppointments::CANCELED; ?>" class="check-status">
                                    <div class="checkbox"></div>
                                </span>
                                <label for="check-cancelled"><?php echo __t('CRM.Cancelled') ?></label>
                            </div>
                            <div class="f-left cnt-check-color d-none">
                                <span class="checkbox-wrapper cw5 checked">
                                    <input type="checkbox" id="check-event" value="<?php echo ConstantsStatusAppointments::EVENT; ?>" class="check-status" checked>
                                    <div class="checkbox"></div>
                                </span>
                                <label for="check-event"><?php echo __t('Event.Events') ?></label>
                            </div>


                        </div>
                        </div>
                    </div>
                    <div style="min-width: 750px" class="p-1" id="calendar" data-url_events="<?php echo Router::url(array('controller' => 'appointments', 'action' => 'ajax_events_list')); ?>" data-url_edit_event="<?php echo Router::url(array('controller' => 'appointments', 'action' => 'ajax_edit_event')); ?>" data-url_selection="<?php echo Router::url(array('controller' => 'appointments', 'action' => 'add')); ?>" data-url_selection_event="<?php echo Router::url(array('controller' => 'appointments', 'action' => 'add_event')); ?>" data-url-preference="<?php echo Router::url(array('controller' => 'users', 'action' => 'ajax_save_preferences')); ?>"></div>
            </div>
        </div>
    </div>