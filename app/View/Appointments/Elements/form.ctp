<?php
echo $this->Html->script('select2.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('/js/appointments.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/fullcalendar-3.10.5/fullcalendar.min.css', array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/lib/moment.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/fullcalendar.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/fullcalendar-3.10.5/locale/' . __l() . '.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('appointments_calendar.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('lib/purify.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

$hide_save = CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ? true : false;

if (
    isset($appointment) && isset($appointment['Appointment']) &&
    !in_array($appointment['Appointment']['appointment_status_id'], array(ConstantsStatusAppointments::CANCELED, ConstantsStatusAppointments::ACCOMPLISHED))
) {
    echo $this->Html->script('auto_save.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
}
echo $this->Form->create('Appointment', array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'id' => 'appointment-form'));
echo $this->Form->hidden('Appointment.id', array('id' => 'appointment-id'));
echo $this->Form->hidden('FormController', array('id' => 'form-controller', 'value' => $this->request->controller));
echo $this->Form->hidden('User.id', array('value' => CakeSession::read('Auth.User.id')));
echo $this->Form->hidden('Appointment.check_send', array('id' => 'check_send'));

if (
    isset($this->request->data['Appointment']) && isset($this->request->data['Appointment']['appointment_status_id']) &&
    in_array($this->request->data['Appointment']['appointment_status_id'], array(ConstantsStatusAppointments::ACCOMPLISHED, ConstantsStatusAppointments::CANCELED, ConstantsStatusAppointmentsDe::RUNNING))
) {
    echo $this->Form->hidden('completed_appointment', array('id' => 'completed_appointment', 'data-class' => 'disabled_fields'));
}
$action = $this->request->action;
$user = CakeSession::read('Auth.User');
$config = CakeSession::read('Auth.User.Config');
if ($config[ConstantsConfig::START_VISIT_LOCATION]) {
    echo $this->Form->hidden('StartVisitLocation', array('id' => 'start_visit_location',));
}
if ($config[ConstantsConfig::TASK_DEADLINE]) {
    echo $this->Form->hidden('TaskDeadlineMandatory', array('id' => 'task_deadline_mandatory',));
}
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('CRM.Crm'),
                    array(
                        'controller' => 'dashboard',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Appointment.Appointments'),
                    array(
                        'controller' => 'appointments',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('CRM.Crm'),
                    array(
                        'controller' => 'dashboard',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Appointment.Appointments'),
                    array(
                        'controller' => 'appointments',
                        'action' => 'home'
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php
        $config = CakeSession::read('Auth.User.Config');
        if (!$hide_save && $action == ConstantsActionsNames::EDIT && $config[ConstantsConfig::AUTO_SAVE]) {
        ?>
            <div id="auto-save" class="d-inline-block fs-small p-0" style="margin: auto 0 !important;" data-url="<?php echo $url; ?>"></div>
        <?php
        }
        if (isset($appointment) && $appointment['Appointment']['id']) {
            echo $this->Html->link(
                __t('Appointment.Export_pdf'),
                array(
                    'controller' => 'appointments',
                    'action' => 'export_pdf',
                    $appointment['Appointment']['id']

                ),
                array(
                    'class' => 'aag-button medium',
                    'title' => __t('Appointment.Export_pdf'),
                    'target' => '_blank',
                )
            );
        }
        echo $this->element('Comun/form_actions_appointment', $cancel_action);
        ?>
    </div>
</div>
<div class="d-none">
    <?php
    echo $this->Form->input(
        'users',
        array(
            'label' => false,
            'type' => 'select',
            'class' => 'select2-multiple',
            'options' => $users,
            'empty' => true,
            'required' => true,
            'id' => 'users_list',
        )
    );
    ?>
</div>
<?php if (isset($garage) && !empty($garage)) { ?>
    <div class="cnt-estilo-nuevo-label" id="ajax_garage_tasks_url" data-url="<?php echo Router::url(array('controller' => 'tasks', 'action' => 'ajax_garage_tasks')); ?>"></div>
<?php

}
if (isset($distributor) && !empty($distributor)) {
?>
    <div id="ajax_distributor_tasks_url" data-url="<?php echo Router::url(array('controller' => 'tasks', 'action' => 'ajax_distributor_tasks')); ?>"></div>
<?php } ?>
<div id="get_data_garage_info" data-url="<?php echo Router::url(array('controller' => 'appointments', 'action' => 'ajax_get_garage_data')); ?>"></div>
<div id="get_data_distributor_info" data-url="<?php echo Router::url(array('controller' => 'appointments', 'action' => 'ajax_get_distributor_data')); ?>"></div>
<div id="get_distributor_objectives" data-url="<?php echo Router::url(array('controller' => 'distributors_objectives', 'action' => 'ajax_get_distributor_objectives')); ?>"></div>
<div style="display: none;" id="modal-calendar" class="reveal-modal background-color-primary" data-reveal aria-hidden="true" role="dialog" data-user-id="<?php echo CakeSession::read('Auth.User.id'); ?>">
    <div id="calendar-appointments" data-url_events="<?php echo Router::url(array('controller' => 'appointments', 'action' => 'ajax_events_list')); ?>"></div>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div>
<div class="d-none" id="confirm-appointment" data-title="<?php echo __t('Appointment.No_customer?'); ?>" data-yes="<?php echo __t('General.Yes'); ?>" data-no="<?php echo __t('General.No'); ?>"></div>
<div class="d-none" id="confirm-task" data-title="<?php echo __t('Appointment.Check_task?'); ?>" data-yes="<?php echo __t('General.Yes'); ?>" data-no="<?php echo __t('General.No'); ?>"></div>
<div class="d-none" id="confirm-uncheck-task" data-title="<?php echo __t('Appointment.Uncheck_task?'); ?>" data-yes="<?php echo __t('General.Yes'); ?>" data-no="<?php echo __t('General.No'); ?>"></div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php echo $action == ConstantsActionsNames::ADD ? __t('Appointment.Add_appointment') : __t('Appointment.Edit_appointment'); ?>
    </div>
    <div class="columns medium-12">
        <div class="columns medium-12">
            <div class="medium-5 p-top-1 p-left-0 columns title-in-fieldset">
                <?php
                if (!empty($appointment_created_by)) {
                    echo __t('Appointment.Created_by') . ': ' . $appointment_created_by['User']['full_name'];
                }
                ?>
            </div>
            <div class="medium-7 columns follow_up ta-right p-right-0 cnt-buttons-v2" style="padding-top: 12px; padding-bottom: 11px;">
                <?php
                if (
                    !$hide_save && $action == ConstantsActionsNames::EDIT &&
                    !in_array($appointment['Appointment']['appointment_status_id'], array(ConstantsStatusAppointments::CANCELED, ConstantsStatusAppointments::ACCOMPLISHED))
                ) {
                    echo $this->Html->link(
                        __t('Appointment.Cancel_appointment'),
                        array(
                            'controller' => 'appointments',
                            'action' => 'cancel_visit',
                            $appointment['Appointment']['id']
                        ),
                        array(
                            'escape' => false,
                            'id' => 'cancel_appointment',
                            'title' => __t('Appointment.Cancel_appointment'),
                            'class' => 'aag-button small red'
                        )
                    );
                }
                ?>
            </div>
        </div>
        <div class="medium-7 columns p-0 cnt-general-data">
            <div class="medium-12 columns" style="padding: 0 !important;">
                <div class="cnt-garage-distributor-hide columns medium-12">
                    <?php
                    if ($action == ConstantsActionsNames::ADD) {
                        if (
                            CakeSession::read('Auth.User.role_id') != ConstantsRoles::GPC_LOGISTICS_BDM &&
                            CakeSession::read('Auth.User.garage_type') != strval(ConstantsBooleans::NO) &&
                            CakeSession::read('Auth.User.distributor_type') == strval(ConstantsBooleans::NO)
                        ) {
                    ?>
                            <input type="radio" class="appointments_types disabled_fields d-none" name="appointment_type" id="appointment_type_garage" <?php echo !empty($garage) ? 'checked' : ''; ?> />
                        <?php } else { ?>
                            <input type="radio" class="appointments_types disabled_fields" name="appointment_type" id="appointment_type_garage" <?php echo !empty($garage) ? 'checked' : ''; ?> />
                            <label for="appointment_type_garage">
                                <?php echo __t('Garage.Garage'); ?>
                            </label>
                        <?php
                        }
                        if (
                            CakeSession::read('Auth.User.distributor_type') != strval(ConstantsBooleans::NO) &&
                            CakeSession::read('Auth.User.garage_type') == strval(ConstantsBooleans::NO)
                        ) {
                        ?>
                            <input type="radio" class="appointments_types d-none" name="appointment_type" id="appointment_type_distributor" <?php echo !empty($distributor) ? 'checked' : ''; ?> />
                        <?php } else { ?>
                            <input type="radio" class="appointments_types disabled_fields" name="appointment_type" id="appointment_type_distributor" <?php echo !empty($distributor) ? 'checked' : ''; ?> />
                            <label for="appointment_type_distributor">
                                <?php echo __t('Distributor.Distributor'); ?>
                            </label>
                        <?php
                        }
                    } else {
                        if (!empty($garage)) {
                        ?>
                            <input type="radio" class="appointments_types disabled_fields d-none" name="appointment_type" id="appointment_type_garage" <?php echo !empty($garage) ? 'checked' : ''; ?> />
                        <?php
                        }
                        if (!empty($distributor)) {
                        ?>
                            <input type="radio" class="appointments_types d-none" name="appointment_type" id="appointment_type_distributor" <?php echo !empty($distributor) ? 'checked' : ''; ?> />
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="medium-12 columns p-right-0 <?php echo ''; ?>">
                <label class="fs-small">
                    <?php echo __t('Appointment.Customer'); ?>
                </label>
                <br />
                <?php
                echo $this->Form->hidden('Appointment.garage_id', array('type' => 'text', 'id' => 'garage_id'));
                echo $this->Form->hidden('Appointment.distributor_id', array('type' => 'text', 'id' => 'distributor_id'));
                $medium = 'medium-10 small-10';
                ?>
                <div class="<?php echo $medium; ?> columns p-left-0" style="margin-top: 0.5em;">
                    <div id="cnt_garage_name">
                        <?php
                        if ($action == ConstantsActionsNames::ADD) {
                            //Garages dynamic filter
                            echo $this->Form->input(
                                'Appointment.garage_id',
                                array(
                                    'label' => false,
                                    'class' => 'dynamicSelect2_garages clear_field',
                                    'type' => 'select',
                                    'multiple' => false,
                                    'empty' => true,
                                    'options' => isset($garages_list) ? $garages_list : array(),
                                    'id' => 'garage_name',
                                    'data-url' => Router::url(array(
                                        'controller' => 'garages',
                                        'action' => 'ajax_get_garages',
                                        $user_aag_region_id
                                    ))
                                )
                            );
                        } elseif ($garage) {
                        ?>
                            <h4 class="p-bottom-1"> <?php echo __t('Appointment.Garage_name') . ' : ' . $garage['Garage']['name'] . ' - ' . $garage['Garage']['g_number_id'] . ' - ' . $garage['Garage']['town']; ?> </h4>
                        <?php } ?>
                    </div>
                    <div id="cnt_distributor_name">
                        <?php
                        if ($action == ConstantsActionsNames::ADD) {
                            //Distributors dynamic filter
                            echo $this->Form->input(
                                'Appointment.distributor_id',
                                array(
                                    'label' => false,
                                    'class' => 'clear_field select2Dinamico_distributor cargar_distributors',
                                    'type' => 'select',
                                    'multiple' => false,
                                    'options' => isset($distributors_list) ? $distributors_list : array(),
                                    'empty' => true,
                                    'id' => 'distributors_selector_id-js',
                                    'data-url-distributors' => Router::url(array(
                                        'controller' => 'appointments',
                                        'action' => 'ajax_get_list_distributors'
                                    )),
                                )
                            );
                        } elseif ($distributor) {
                        ?>
                            <h4 class="p-bottom-1"> <?php echo __t('Appointment.Distributor_name') . ' : ' . $distributor['Distributor']['name'] . ' - ' . $distributor['Distributor']['account_number'] . ' - ' . $distributor['Distributor']['town']; ?> </h4>
                        <?php } ?>
                    </div>
                    <span class="b-bottom-1 d-block" id="no_customer">
                        <?php echo __t('Appointment.Select_customer'); ?>
                    </span>
                </div>
            </div>
            <div class="medium-11 columns p-0 end">
                <div class="medium-12 columns obligatorio">
                    <?php echo $this->element('../Appointments/Elements/ajax_assigned_to'); ?>
                </div>
                <div class="medium-12 columns p-0">
                    <div class="medium-4 columns">
                        <?php
                        echo $this->Form->input(
                            'Appointment.date',
                            array(
                                'type' => 'text',
                                'data-open' => 'modal-calendar',
                                'required' => true,
                                'class' => 'disabled_fields',
                                'id' => 'appointment-date',
                                'div' => array(
                                    'class' => 'datepicker datepicker-label-block',
                                ),
                                'label' => __t('Appointment.Date'),
                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            )
                        );
                        ?>
                    </div>
                    <div class="medium-4 columns cnt-datp obligatorio">
                        <?php
                        echo $this->Form->input(
                            'Appointment.start_time',
                            array(
                                'type' => 'text',
                                'required' => true,
                                'class' => 'timepicker disabled_fields',
                                'id' => 'start_time',
                                'label' => __t('Appointment.Start_time'),
                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            )
                        );
                        ?>
                    </div>
                    <div class="medium-4 columns cnt-datp obligatorio">
                        <?php
                        echo $this->Form->input(
                            'Appointment.end_time',
                            array(
                                'type' => 'text',
                                'class' => 'timepicker disabled_fields',
                                'id' => 'end_time',
                                'required' => true,
                                'label' => __t('Appointment.End_time'),
                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="medium-12 columns p-0">
                    <div id="cnt_contact_visits" data-url="<?php echo Router::url(array('controller' => 'appointments', 'action' => 'ajax_get_contact_visit')) ?>">
                        <?php echo $this->element('../Appointments/Elements/contact_visits', array('contacts_visits' => $contacts_visits)); ?>
                    </div>
                </div>
                <div class="medium-12 columns p-0">
                    <div class="medium-4 columns m-bottom-1 ">
                        <?php
                        $opt_disabled = ConstantsBooleans::YES;
                        $opt_default = ConstantsStatusAppointments::PLANNED;
                        if (isset($this->request->data['Appointment']['appointment_status_id']) && !empty($this->request->data['Appointment']['appointment_status_id'])) {
                            if ($this->request->data['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::PLANNED) {
                                $opt_default = $this->request->data['Appointment']['appointment_status_id'];
                            } else {
                                $opt_disabled = ConstantsBooleans::NO;
                                $appointment_status = array(
                                    ConstantsStatusAppointments::PLANNED => __t('CRM.Planned'),
                                    ConstantsStatusAppointments::RESCHEDULED => __t('CRM.Rescheduled'),
                                    ConstantsStatusAppointments::PENDING => __t('CRM.Pending')
                                );
                            }
                        }
                        echo $this->Form->hidden('Appointment.appointment_status_id', array('value' => $opt_default, 'id' => 'appointment-status-hidden'));
                        echo $this->Form->input(
                            'Appointment.appointment_status_id',
                            array(
                                'label' => __t('General.Status'),
                                'type' => 'select',
                                'class' => 'select2-multiple disabled_fields',
                                'id' => 'appointment-status',
                                'options' => $appointment_status,
                                'multiple' => false,
                                'empty' => false,
                                'required' => true,
                                'default' => $opt_default,
                                'disabled' => $opt_disabled,
                                'data-pending' => ConstantsStatusAppointments::PENDING,
                            )
                        );
                        ?>
                    </div>
                    <?php $classStyle = 'class="medium-8 columns obligatorio"'; ?>
                    <div <?php echo $classStyle; ?>>
                        <?php
                        // Este select descuadra la página.
                        echo $this->Form->input(
                            'Appointment.appointment_type_id',
                            array(
                                'label' => __t('Appointment.Type'),
                                'type' => 'select',
                                'class' => 'select2-multiple disabled_fields',
                                'id' => 'appointment_type_id',
                                'options' => $appointment_types,
                                'multiple' => false,
                                'empty' => false,
                                'required' => true,
                                'default' => 1,
                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                            )
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="medium-5 columns p-left-0 p-right-0">
            <div class="columns">
                <div id="garage_info" class="d-inline-block w-100p" style="box-shadow: 0 0 5px #f6f7f8; border: 1px solid #f6f7f8;">
                    <?php echo $this->element('../Appointments/Elements/garage_info'); ?>
                </div>
            </div>
            <div class="d-none fieldset_advanced_search p-left-0 p-right-0" id="advanced_search" style="min-height: 465px;">
                <div id="advanced_search_garage" class="<?php echo (empty($garage)) ? 'd-none' : ''; ?>">
                    <?php echo $this->element('../Appointments/Elements/advance_search_garage'); ?>
                </div>
                <div id="advanced_search_distributor" class="<?php echo (empty($distributor)) ? 'd-none' : ''; ?>">
                    <?php echo $this->element('../Appointments/Elements/advance_search_distributor'); ?>
                </div>
                <div id="advanced_search_select" class="p-1 <?php echo (empty($distributor) && empty($garage)) ? '' : 'd-none' ?>">
                    <h1 style="color: #FFF">
                        <?php echo __t('Appointment.Select_customer'); ?>
                    </h1>
                </div>
            </div>
        </div>
        <div id="distributor_objectives" class="<?php echo (empty($distributor)) ? 'd-none' : ''; ?>">
            <div class="medium-12 columns">
                <b class="fs-large" style="padding-bottom: .5rem;">
                    <?php echo __t('Appointment.Visit_objectives'); ?>
                </b>
                <hr />
            </div>
            <div class="medium-12 columns">
                <h2 class="obligatorio">
                    <?php echo __t('Appointment.Customer_performance_summary'); ?>
                </h2>
                <?php
                echo $this->Form->input(
                    'customer_performance_summary',
                    array(
                        'label' => false,
                        'id' => 'summary_area',
                        'type' => 'textarea',
                        'value' => isset($this->request->data['Appointment']['customer_performance_summary']) ? $this->request->data['Appointment']['customer_performance_summary'] : '',
                        'rows' => 3,
                        'class' =>  'disabled_fields'
                    )
                );
                ?>
            </div>
            <hr class="aag-negative-margin hr-divisor" />
            <h2>
                <?php echo __t('Appointment.Management_objectives'); ?>
            </h2>
            <div id="distributor_management_objectives" class="aag-negative-margin">
                <?php echo $this->element('../Appointments/Elements/management_objectives'); ?>
            </div>
            <hr class="aag-negative-margin hr-divisor" />
            <div class="title-with-buttons">
                <h2>
                    <?php echo __t('Appointment.Personal_objectives'); ?>
                </h2>
                <div>
                    <?php
                    echo $this->Form->input(
                        'DistributorName',
                        array(
                            'label' => __t('Appointment.Objective'),
                            'type' => 'select',
                            'class' => 'select2-multiple',
                            'required' => true,
                            'id' => 'distributor_name2',
                            'options' => $personal_objectives
                        )
                    );
                    if ($action == ConstantsActionsNames::ADD) {
                        echo $this->Html->link(
                            __t('Contact.New_objective'),
                            array(),
                            array(
                                'escape' => false,
                                'class' => 'aag-button small green btn-guardar',
                                'title' => __t('Distributor.New_distributor'),
                                'id' => 'btn_add_visual_objective'
                            )
                        );
                    } else {
                        echo $this->Html->link(
                            __t('Contact.New_objective'),
                            array(),
                            array(
                                'escape' => false,
                                'class' => 'aag-button small green btn-guardar',
                                'title' => __t('Distributor.New_distributor'),
                                'id' => 'btn_add_bdm',
                                'data-url' => Router::url(
                                    array(
                                        'controller' => 'appointments',
                                        'action' => 'ajax_add_personal_objective'
                                    )
                                )
                            )
                        );
                    }
                    ?>
                </div>
            </div>
            <div class="o-auto aag-negative-margin" disabled>
                <table class="table-tracking">
                    <thead>
                        <tr>
                            <th><?php echo __t('Appointment.Objective'); ?></th>
                            <th><?php echo __t('Appointment.Objective_status'); ?></th>
                            <th><?php echo __t('Appointment.Post_visit_comment'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="sort-contacts" class="test" data-page="<?php echo ConstantsPagination::FIRST_PAGE; ?>">
                        <?php
                        if ($action == ConstantsActionsNames::ADD) {
                            foreach ($personal_objectives as $key => $personal_objective) {
                        ?>
                                <tr class="item-distributor objective-hidden" data-objective-id="<?php echo $key ?>" hidden>
                                    <td>
                                        <?php
                                        echo h($personal_objective);
                                        echo $this->Form->hidden('PersonalObjective.' . $key . '.id', array('label' => false, 'value' => $key, 'id' => 'personalNameObjectiveKey' . $key));
                                        ?>
                                    </td>
                                    <td class="c-defecto">
                                        <?php
                                        echo $this->Form->input(
                                            'PersonalObjective.' . $key . '.status',
                                            array(
                                                'label' => false,
                                                'type' => 'select',
                                                'class' => 'select2-multiple',
                                                'id' => 'personalNameObjectiveStatus' . $key,
                                                'options' => $objectives_status,
                                                'multiple' => false,
                                                'empty' => false,
                                                'required' => true,
                                                'style' => 'margin-top: -17px;',
                                                'disabled' => 'disabled'
                                            )
                                        );
                                        ?>
                                    </td>
                                    <td class="c-defecto">
                                        <?php
                                        echo $this->Form->input(
                                            'PersonalObjective.' . $key . '.comment',
                                            array(
                                                'label' => false,
                                                'id' => 'personalNameComment' . $key,
                                                'type' => 'textarea',
                                                'rows' => 1,
                                                'disabled' => 'disabled'
                                            )
                                        );
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            $var_name_objective = null;
                            if (isset($appointment_objectives_personal_assoc)) {
                                foreach ($appointment_objectives_personal_assoc as $key => $objective_personal) {
                                    $var_name_objective = $objective_personal['objective_id'] == null ? $objective_personal['objective'] : $personal_objectives_list[$objective_personal['objective_id']];
                                ?>
                                    <tr class="item-distributor" data-garages-contacts-bdm-id="<?php echo $key ?>">
                                        <td>
                                            <?php
                                            echo h($var_name_objective);
                                            if ($objective_personal['objective_id'] == null) {
                                                echo $this->Form->hidden(
                                                    'Appointment.objectives_personal.' . $key . '.objective',
                                                    array(
                                                        'value' => $key,
                                                        'id' => 'personalNameObjectiveKey' . $key,
                                                    )
                                                );
                                            } else {
                                                echo $this->Form->hidden(
                                                    'Appointment.objectives_personal.' . $key . '.objective_id',
                                                    array(
                                                        'value' => $objective_personal['objective_id'],
                                                        'id' => 'personalNameObjectiveKey' . $key,
                                                    )
                                                );
                                            }
                                            ?>
                                        </td>
                                        <td class="c-defecto">
                                            <?php
                                            if (!isset($appointment) || ($appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::CANCELED && $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::ACCOMPLISHED)) {
                                                echo $this->Form->input(
                                                    'Appointment.objectives_personal.' . $key . '.status',
                                                    array(
                                                        'label' => false,
                                                        'type' => 'select',
                                                        'class' => 'select2-multiple',
                                                        'id' => 'personalNameObjectiveStatus' . $key,
                                                        'options' => $objectives_status,
                                                        'multiple' => false,
                                                        'empty' => false,
                                                        'required' => true,
                                                        'value' => $objective_personal['status'],
                                                        'style' => 'margin-top: -17px;',
                                                        'disabled' => !isset($appointment) ? 'disabled' : '',
                                                    )
                                                );
                                            } else {
                                                echo $objectives_status[$objective_personal['status']];
                                            }
                                            ?>
                                        </td>
                                        <td class="c-defecto">
                                            <?php
                                            if (!isset($appointment) || ($appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::CANCELED && $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::ACCOMPLISHED)) {
                                                echo $this->Form->input(
                                                    'Appointment.objectives_personal.' . $key . '.comment',
                                                    array(
                                                        'label' => false,
                                                        'id' => 'personalNameComment' . $key,
                                                        'type' => 'textarea',
                                                        'value' => $objective_personal['comment'],
                                                        'rows' => 1,
                                                        'disabled' => !isset($appointment) ? 'disabled' : '',
                                                    )
                                                );
                                            } else {
                                                echo $objective_personal['comment'];
                                            }
                                            ?>
                                        </td>
                                    </tr>
                        <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <hr class="aag-negative-margin hr-divisor" />
        </div>
    </div>
    <?php $display_none = (isset($appointment) && !empty($appointment['Appointment']['feedback'])) ? '' : 'display: none'; ?>
    <div id="cnt_appointments_notify_to" style="<?php echo $display_none ?>">
        <?php if ($action == ConstantsActionsNames::ADD) { ?>
            <div id="cnt_appointments_notify_to_1">
                <div class="columns medium-12">
                    <fieldset class="columns p-horizontal-0 fieldset-garage-list p-0">
                        <div class="columns medium-7 p-0">
                            <fieldset class="columns p-0 fieldset-garage-list">
                                <div class="columns medium-11 p-top-1" style="margin-bottom: .5rem;">
                                    <div>
                                        <span id="flecha_abajo_feedback" class="ion-chevron-down d-none cursor-pointer fields_titles_forms">
                                            <?php echo __t('Appointment.Feedback'); ?><span style="color:red"> *</span>
                                        </span>
                                        <span id="flecha_arriba_feedback" class="ion-chevron-up d-none cursor-pointer fields_titles_forms">
                                            <?php echo __t('Appointment.Feedback'); ?><span style="color:red"> *</span>
                                        </span>
                                    </div>
                                    <div id="div_feedback" style="padding-top: .5rem;">
                                        <?php
                                        $desactivate = (!isset($appointment) || $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::ACCOMPLISHED) ? '' : 'readonly';
                                        echo $this->Form->input(
                                            'feedback',
                                            array(
                                                'label' => false,
                                                'id' => 'feedback_area',
                                                'type' => 'textarea',
                                                'value' => isset($this->request->data['Appointment']['feedback']) ? $this->request->data['Appointment']['feedback'] : '',
                                                'rows' => 8,
                                                'readonly' => $desactivate,
                                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                                            )
                                        );
                                        ?>
                                    </div>
                                </div>
                                <?php if (!$hide_save) { ?>
                                    <div class="columns medium-12">
                                        <div class="aag-subtitle p-top-1">
                                            <?php echo __t('Appointment.Next_appointment'); ?>
                                            <button class="aag-button medium" type="button" id='next_appointment_create'>
                                                <?php echo __t('Appointment.Create_visit'); ?>
                                            </button>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div style="display: none;" id="nextAppointmentModal" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-user-id="<?php echo CakeSession::read('Auth.User.id'); ?>">
                                    <div id="calendar-appointments2" data-url_events="<?php echo Router::url(array('controller' => 'appointments', 'action' => 'ajax_events_list')); ?>">
                                    </div>
                                    <div class="columns medium-3 p-top-1">
                                        <?php
                                        echo $this->Form->input(
                                            'Appointment.date_next',
                                            array(
                                                'type' => 'text',
                                                'required' => true,
                                                'id' => 'appointment-date_next',
                                                'div' => array('class' => 'datepicker datepicker-label-block obligatorio'),
                                                'label' => __t('Appointment.Date'),
                                                'disabled' => 'disabled'
                                            )
                                        );
                                        ?>
                                    </div>
                                    <div class="columns medium-3 p-top-1 obligatorio">
                                        <?php
                                        echo $this->Form->input(
                                            'Appointment.start_time_next',
                                            array(
                                                'type' => 'text',
                                                'required' => true,
                                                'class' => 'timepicker',
                                                'id' => 'start_time_next',
                                                'label' => __t('Appointment.Start_time'),
                                                'value' => '00:00'
                                            )
                                        );
                                        ?>
                                    </div>
                                    <div class="columns medium-3 p-top-1 obligatorio">
                                        <?php
                                        echo $this->Form->input(
                                            'Appointment.end_time_next',
                                            array(
                                                'type' => 'text',
                                                'class' => 'timepicker',
                                                'id' => 'end_time_next',
                                                'required' => true,
                                                'label' => __t('Appointment.End_time'),
                                                'value' => '00:00'
                                            )
                                        );
                                        ?>
                                    </div>
                                    <div class="columns medium-3 p-top-1" style="margin-top: 2em;">
                                        <?php
                                        echo $this->Form->input(
                                            'Appointment.create_reminder',
                                            array(
                                                'type' => 'checkbox',
                                                'label' => __t('Crm.Create_reminder'),
                                                'checked' => false,
                                                'div' => false,
                                                'id' => 'reminder_next'
                                            )
                                        );
                                        ?>
                                    </div>
                                    <div class="columns medium-12 p-top-1 ta-center">
                                        <?php
                                        echo $this->Html->link(
                                            __t('General.Save'),
                                            'javascript:;',
                                            array(
                                                'data-url' => Router::url(
                                                    array(
                                                        'controller' => 'appointments',
                                                        'action' => 'add_appointment_next'
                                                    )
                                                ),
                                                'class' => 'aag-button large green btn-guardar-exit',
                                                'id' => 'create_next_appointment',
                                                'data-error' => __t('Crm.Empty_fields_next_appointment'),
                                                'data-user' => $user['id'],
                                                'data-garage_id' => isset($appointment) ? $appointment['Appointment']['garage_id'] : '',
                                                'data-distributor_id' => isset($appointment) ? $appointment['Appointment']['distributor_id'] : ''
                                            )
                                        );
                                        ?>
                                    </div>
                                    <a class="close-modal" data-close aria-label="Close">&#215;</a>
                                </div>
                                <div class="columns medium-12">
                                    <div class="columns medium-12 p-top-1 p-left-0" style="padding-bottom: 6px;">
                                        <div class="aag-subtitle">
                                            <?php echo __t('Appointment.Notify_to') . ' ' . __t('Task.Contact_lists'); ?><span style="color:red"> *</span>
                                        </div>
                                    </div>
                                    <div class="columns medium-12 cnt-select-notify p-left-0 p-right-0" style="padding-bottom: .5rem;">
                                        <?php
                                        if (
                                            isset($appointment) && isset($appointment['Appointment']) &&
                                            (
                                                $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::CANCELED ||
                                                $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::ACCOMPLISHED
                                            )
                                        ) {
                                            echo '<ul>';
                                            if (isset($appointment['Appointment']['appointment_contact_lists'])) {
                                                foreach ($appointment['Appointment']['appointment_contact_lists'] as $key => $list) {
                                                    echo '<li>' . $contact_lists[$list] . '</li>';
                                                }
                                            }
                                            echo '<ul>';
                                        } else {
                                            echo $this->Form->input(
                                                'Appointment.appointment_contact_lists',
                                                array(
                                                    'label' => false,
                                                    'class' => 'select2-multiple',
                                                    'type' => 'select',
                                                    'multiple' => true,
                                                    'empty' => true,
                                                    'options' => $contact_lists,
                                                    'id' => 'notify_to',
                                                    'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                                                )
                                            );
                                        }
                                        ?>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="columns medium-5 p-bottom-1 p-top-0 p-left-0 p-right-0 cnt-generate-alerts">
                            <div class="columns medium-12 p-top-1">
                                <div class="aag-subtitle">
                                    <?php echo __t('Appointment.Feeling'); ?><span style="color:red"> *</span>
                                </div>
                                <div class=" d-inline-block cont-radio-image w-100p p-vertical-1 cont-radio" style="padding-top: .5rem;">
                                    <?php
                                    if (
                                        (
                                            isset($appointment) && isset($appointment['Appointment']) &&
                                            in_array($appointment['Appointment']['appointment_status_id'], array(ConstantsStatusAppointments::CANCELED, ConstantsStatusAppointments::ACCOMPLISHED))
                                        ) ||
                                        CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN
                                    ) {
                                        foreach ($appointment_feelings as $feeling) {
                                            if ($appointment['Appointment']['appointment_feeling_id'] == $feeling['AppointmentFeeling']['id']) {
                                    ?>
                                                <div class="d-inline-block end appointment_feelings" style="padding: 0; width: 55px;">
                                                    <label class="ta-center" style="color: #9ea4b2; font-size: 14px;white-space: nowrap;margin: 0;">
                                                        <span class="d-block ta-center">
                                                            <?php echo $this->Html->image(FilePaths::ICONS_IMAGES_RELATIVE . $feeling['AppointmentFeeling']['icon'], array('style' => 'padding: 0; max-width: 35px;')); ?>
                                                        </span>
                                                        <div>
                                                            <?php echo $feeling['AppointmentFeeling']['name' . __s()]; ?>
                                                        </div>
                                                    </label>
                                                </div>
                                            <?php
                                            }
                                        }
                                    } else {
                                        foreach ($appointment_feelings as $feeling) {
                                            ?>
                                            <div class="d-inline-block end appointment_feelings" style="padding: 0; width: 55px;">
                                                <label class="ta-center" style="color: #9ea4b2; font-size: 14px; white-space: nowrap; margin: 0;">
                                                    <?php
                                                    echo $this->Form->input(
                                                        'Appointment.appointment_feeling_id',
                                                        array(
                                                            'type' => 'radio',
                                                            'label' => false,
                                                            'options' => array($feeling['AppointmentFeeling']['id'] => ''),
                                                            'title' => $feeling['AppointmentFeeling']['name' . __s()],
                                                            'id' => 'radioImage',
                                                            'class' => 'radio_feelings',
                                                            'div' => false,
                                                            'hiddenField' => false,
                                                        )
                                                    );
                                                    ?>
                                                    <span class="d-block ta-center">
                                                        <?php echo $this->Html->image(FilePaths::ICONS_IMAGES_RELATIVE . $feeling['AppointmentFeeling']['icon'], array('style' => 'padding: 0; max-width: 35px;')); ?>
                                                    </span>
                                                    <div>
                                                        <?php echo $feeling['AppointmentFeeling']['name' . __s()]; ?>
                                                    </div>
                                                </label>
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="columns medium-12">
                                <div class="aag-subtitle">
                                    <?php echo __t('General.Files'); ?>
                                </div>
                                <?php
                                if (
                                    !isset($appointment) ||
                                    !in_array($appointment['Appointment']['appointment_status_id'], array(ConstantsStatusAppointments::CANCELED, ConstantsStatusAppointments::ACCOMPLISHED))
                                ) {
                                ?>
                                    <div class="columns medium-12 p-0">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
                                        <?php
                                        echo $this->Form->input(
                                            'Appointments.files.',
                                            array(
                                                'id' => 'appointment_files',
                                                'class' => 'dragdrop-js dragdrop-multiple-js',
                                                'label' => false,
                                                'type' => 'file',
                                                'multiple' => true,
                                                'accept' => '.' . ConstantsFileType::PDF,
                                                'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                                                'div' => array(
                                                    'class' => 'field_file cont-fileWrapper fileWrapperMultiple',
                                                ),
                                                'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                                            )
                                        );
                                        ?>
                                    </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="medium-12 columns p-normal" style="padding-bottom: 1rem;">
                                        <div id="file-list-js">
                                            <?php if (isset($appointment_files) && $appointment_files) {
                                                echo $this->element('../Appointments/Elements/form_attached_files');
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 20px; align-items: center;">
                                <div class="cnt-general-data">
                                    <div class="cont-services follow_up clear end" style="padding: 0; height: max-content;">
                                        <label class="m-0-i">
                                            <?php
                                            if (!isset($appointment) || ($appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::CANCELED && $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::ACCOMPLISHED)) {
                                                $checked = isset($appointment['Appointment']['requires_follow_up']) && $appointment['Appointment']['requires_follow_up'] == ConstantsBooleans::YES ? true : false;

                                                echo $this->Form->input(
                                                    'Appointment.requires_follow_up',
                                                    array(
                                                        'type' => 'checkbox',
                                                        'id' => 'requires_follow_up',
                                                        'class' => 'disabled_fields',
                                                        'label' => false,
                                                        'div' => false,
                                                        'value' => 1,
                                                        'checked' => $checked,
                                                        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
                                                    )
                                                );
                                            ?>
                                                <span class="icon-follow unselectable" id="span_requires_follow_up"></span>
                                                <?php
                                                echo __t('Appointment.Requires_follow_up');
                                            } else {
                                                if ($appointment['Appointment']['requires_follow_up'] == ConstantsBooleans::YES) {
                                                ?>
                                                    <input type="checkbox" id="requires_follow_up" readonly disabled checked="checked" />
                                                    <span class="icon-follow unselectable c-primary" id="span_requires_follow_up"></span>
                                                <?php
                                                    echo __t('Appointment.Requires_follow_up');
                                                } else {
                                                ?>
                                                    <input type="checkbox" id="requires_follow_up" readonly disabled />
                                                    <span class="icon-follow unselectable" id="span_requires_follow_up"></span>
                                            <?php
                                                    echo __t('Appointment.Requires_follow_up');
                                                }
                                            }
                                            ?>
                                        </label>
                                    </div>
                                </div>
                                <?php
                                $checked_tmp_ = $config[ConstantsConfig::CHECK_SEND_NOTIFICATION] ? false : true;
                                $class_hidden = ($config[ConstantsConfig::CHECK_SEND_NOTIFICATION] && isset($appointment) && $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PENDING) ? '' : 'd-none';
                                ?>
                                <?php if (!$hide_save) { ?>
                                    <div id="generate_alerts-id" class="columns medium-12 cont-checkbox">
                                        <label id="generate_alerts" class="label-w-auto position-relative">
                                            <?php
                                            echo $this->Form->input(
                                                'Notification.generate_alerts',
                                                array(
                                                    'type' => 'checkbox',
                                                    'label' => false,
                                                    'checked' => $checked_tmp_,
                                                    'div' => false,
                                                    'hidden' => true
                                                )
                                            );
                                            ?>
                                            <span class="generate_alerts unselectable ws-nowrap" style="bottom: unset; position: static;">
                                                <?php echo __t('Task.Generate_alerts'); ?>
                                            </span>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        <?php } else { ?>
            <div class="row p-top-1" id="cnt_appointments_notify_to_2">
                <div class="columns medium-12">
                    <fieldset class="columns p-horizontal-0 p-bottom-0 fieldset-garage-list">
                        <div class="columns medium-7 p-0">
                            <fieldset class="columns p-0 fieldset-garage-list">
                                <div class="columns medium-11 p-top-1" style="margin-bottom: .5rem;">
                                    <div>
                                        <span id="flecha_abajo_feedback" class="ion-chevron-down d-none cursor-pointer fields_titles_forms">
                                            <?php echo __t('Appointment.Feedback'); ?><span style="color:red"> *</span>
                                        </span>
                                        <span id="flecha_arriba_feedback" class="ion-chevron-up d-none cursor-pointer fields_titles_forms">
                                            <?php echo __t('Appointment.Feedback'); ?><span style="color:red"> *</span>
                                        </span>
                                    </div>
                                    <div id="div_feedback" style="padding-top: .5rem;">
                                        <?php
                                        $desactivate = !isset($appointment) || $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::ACCOMPLISHED ? '' : 'readonly';
                                        echo $this->Form->input(
                                            'feedback',
                                            array(
                                                'label' => false,
                                                'id' => 'feedback_area',
                                                'type' => 'textarea',
                                                'value' => isset($this->request->data['Appointment']['feedback']) ? $this->request->data['Appointment']['feedback'] : '',
                                                'rows' => 8,
                                                'readonly' => $desactivate,
                                            )
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="columns medium-12 p-top-1" style="padding-bottom: 6px;">
                                    <b class="fs-large" style="margin-bottom: .5rem;">
                                        <?php echo __t('Appointment.Notify_to') . ' ' . __t('Task.Contact_lists'); ?><span style="color:red"> *</span>
                                    </b>
                                </div>
                                <div class="columns medium-12 cnt-select-notify" style="padding-bottom: .5rem;">
                                    <?php
                                    echo $this->Form->input(
                                        'Appointment.appointment_contact_lists',
                                        array(
                                            'label' => false,
                                            'class' => 'select2-multiple',
                                            'type' => 'select',
                                            'multiple' => true,
                                            'empty' => true,
                                            'options' => $contact_lists,
                                            'id' => 'notify_to'
                                        )
                                    );
                                    ?>
                                </div>
                            </fieldset>
                        </div>
                        <div class="columns medium-5 p-top-0 p-left-0 p-right-0 cnt-generate-alerts">
                            <div class="columns medium-12 p-top-1">
                                <b class="fs-large">
                                    <?php echo __t('Appointment.Feeling'); ?><span style="color:red"> *</span>
                                </b>
                                <div class="d-inline-block cont-radio-image w-100p cont-radio" style="padding-top: .5rem;">
                                    <?php foreach ($appointment_feelings as $feeling) { ?>
                                        <div class="d-inline-block end appointment_feelings" style="padding: 0;width: 55px">
                                            <label class="ta-center" style="color: #9ea4b2; font-size: 14px; white-space: nowrap;margin: 0;">
                                                <?php
                                                echo $this->Form->input(
                                                    'Appointment.appointment_feeling_id',
                                                    array(
                                                        'type' => 'radio',
                                                        'label' => false,
                                                        'options' => array($feeling['AppointmentFeeling']['id'] => ''),
                                                        'title' => $feeling['AppointmentFeeling']['name' . __s()],
                                                        'id' => 'radioImage',
                                                        'class' => 'radio_feelings',
                                                        'div' => false,
                                                        'hiddenField' => false,
                                                    )
                                                );
                                                ?>
                                                <span class="d-block ta-center">
                                                    <?php echo $this->Html->image(FilePaths::ICONS_IMAGES_RELATIVE . $feeling['AppointmentFeeling']['icon'], array('style' => 'padding: 0; max-width: 35px;')); ?>
                                                </span>
                                                <div>
                                                    <?php echo $feeling['AppointmentFeeling']['name' . __s()]; ?>
                                                </div>
                                            </label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="columns medium-12">
                                <b class="fs-large">
                                    <?php echo __t('General.Files'); ?>
                                </b>
                                <div class="columns medium-12 p-0">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
                                    <?php
                                    echo $this->Form->input(
                                        'Appointments.files.',
                                        array(
                                            'id' => 'appointment_files',
                                            'class' => 'dragdrop-js dragdrop-multiple-js',
                                            'label' => false,
                                            'type' => 'file',
                                            'multiple' => true,
                                            'accept' => '.' . ConstantsFileType::PDF,
                                            'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                                            'div' => array('class' => 'field_file cont-fileWrapper fileWrapperMultiple')
                                        )
                                    );
                                    ?>
                                </div>
                                <div class="row">
                                    <div class="medium-12 columns p-normal" style="padding-bottom: 1rem;">
                                        <div id="file-list-js">
                                            <?php
                                            if (isset($appointment_files) && $appointment_files) {
                                                echo $this->element('../Appointments/Elements/form_attached_files');
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <?php
                                    $checked_tmp_ = $config[ConstantsConfig::CHECK_SEND_NOTIFICATION] ? false : true;
                                    $class_hidden = ($config[ConstantsConfig::CHECK_SEND_NOTIFICATION] && isset($appointment) && $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PENDING) ? '' : 'd-none';
                                    ?>
                                    <div id="generate_alerts-id" class="columns medium-6 p-0 cont-checkbox <?php echo $class_hidden ?>">
                                        <label id="generate_alerts" class="label-w-auto">
                                            <?php
                                            echo $this->Form->input(
                                                'Notification.generate_alerts',
                                                array(
                                                    'type' => 'checkbox',
                                                    'label' => false,
                                                    'checked' => $checked_tmp_,
                                                    'div' => false,
                                                    'hidden' => true
                                                )
                                            );
                                            ?>
                                            <span class="generate_alerts unselectable generate-alerte-browser">
                                                <?php echo __t('Task.Generate_alerts'); ?>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                    </fieldset>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="columns medium-12 m-vertical-1 d-none" id="cnt-topics">
    <b class="fs-large">
        <?php echo __t('Task.Topics'); ?>
    </b>
    <?php
    if (isset($appointment_topics)) {
        foreach ($appointment_topics as $topic) {
    ?>
            <div class="topic-item" data-topic-id="<?php echo $topic['AppointmentTopic']['topic_id'] ?>">
                <input type="hidden" name="data[AppointmentTopic][]" class="topic-item-input" data-topic-id="<?php echo $topic['AppointmentTopic']['topic_id'] ?>" value="<?php echo $topic['AppointmentTopic']['topic_id'] ?>" />
                <span style="padding-left: 7px;">
                    <?php echo $topics[$topic['AppointmentTopic']['topic_id']]; ?>
                </span>
                <i class="ion-close-circled"></i>
            </div>
    <?php
        }
    }
    ?>
</div>
<div class="columns medium-12 m-vertical-1 d-none" id="cnt-tasks">
    <b class="fs-large">
        <?php echo __t('Task.Debrief'); ?>
    </b>
    <?php
    if (isset($debrief_tasks)) {
        foreach ($debrief_tasks as $task) {
    ?>
            <div class="task-item" data-task-id="<?php echo $task['DebriefTask']['id'] ?>">
                <input type="hidden" name="data[DebriefTask][]" class="task-item-input" data-task-id="<?php echo $task['DebriefTask']['id'] ?>" value="<?php echo $task['DebriefTask']['id'] ?>" />
                <span style="padding-left: 7px;">
                    <?php echo $task['DebriefTask']['id']; ?>
                </span>
                <i class="ion-close-circled"></i>
            </div>
    <?php
        }
    }
    ?>
</div>
<?php
echo $this->Form->end();
if (isset($appointment)) {
?>
    <div class="cnt-data aag-padding">
        <div class="columns medium-12">
            <fieldset class="columns fieldset-garage-list p-0">
                <?php
                if (isset($tasks_garages_all) && !empty($tasks_garages_all) || ($tasks_garages_total > 0)) {
                ?>
                    <div class="row m-top-1" id="cnt_task_garage_parent">
                        <div class="m-1" id="cnt_task_garage"></div>
                    </div>
                <?php } else { ?>
                    <div class="row m-top-1 d-none" id="cnt_task_garage_parent">
                        <div class="m-1" id="cnt_task_garage"></div>
                    </div>
                <?php } ?>
            </fieldset>
            <fieldset class="columns fieldset-garage-list p-0">
                <div class="columns medium-12 p-0">
                    <fieldset class="background-primary-color">
                        <div class="columns medium-12" style="padding-top: 4px; padding-bottom: 8px;">
                            <?php if (!empty($debrief_topics)) { ?>
                                <div class="columns medium-3 p-left-0">
                                    <b class="fs-large">
                                        <?php echo __t('Task.Debrief') . ' / ' . __t('Task.Topics'); ?>
                                    </b>
                                </div>
                                <?php if (!$hide_save) { ?>
                                    <button id="add-word-cloud-topic" class="aag-button medium f-right" data-url="<?php echo Router::url(array('controller' => 'tasks', 'action' => 'ajax_add_debrief_topic')); ?>">
                                        <span class="aag-icon-editar"></span>
                                        <?php echo __t('Appointment.Create_topic'); ?>
                                    </button>
                                <?php } ?>
                        </div>
                        <?php if (!$hide_save) { ?>
                            <div class="columns cnt-word-cloud-topic listado-word-cloud">
                                <?php
                                    $count_debrief_topic = 0;
                                    foreach ($debrief_topics as $debrief_topic) {
                                        foreach ($appointment_topics as $appointment_topic) {
                                            if ($appointment_topic['AppointmentTopic']['topic_id'] == $debrief_topic['DebriefTopic']['id']) {
                                                $count_debrief_topic++; ?>
                                            <div class="columns medium-3 end">
                                                <div class="word-cloud word-cloud-topic" data-topic-id="<?php echo $debrief_topic['DebriefTopic']['id'] ?>" data-topic-title="<?php echo $debrief_topic['DebriefTopic']['name' . __s()] ?>">
                                                    <div class="word-cloud-title">
                                                        <?php echo $debrief_topic['DebriefTopic']['name' . __s()]; ?>
                                                    </div>
                                                    <i class="ion-checkmark-circled"></i>
                                                </div>
                                            </div>
                                        <?php
                                            }
                                        }
                                    }
                                    foreach ($debrief_topics as $debrief_topic) {
                                        $topic_tmp = true;
                                        foreach ($appointment_topics as $appointment_topic) {
                                            if ($appointment_topic['AppointmentTopic']['topic_id'] == $debrief_topic['DebriefTopic']['id']) {
                                                $topic_tmp = false;
                                            }
                                        }
                                        if ($topic_tmp && $count_debrief_topic < 8) {
                                            $count_debrief_topic++; ?>
                                        <div class="columns medium-3 end">
                                            <div class="word-cloud word-cloud-topic" data-topic-id="<?php echo $debrief_topic['DebriefTopic']['id'] ?>" data-topic-title="<?php echo $debrief_topic['DebriefTopic']['name' . __s()] ?>">
                                                <div class="word-cloud-title">
                                                    <?php echo $debrief_topic['DebriefTopic']['name' . __s()]; ?>
                                                </div>
                                                <i class="ion-checkmark-circled"></i>
                                            </div>
                                        </div>
                                <?php
                                        }
                                    }
                                ?>
                            </div>
                        <?php } ?>
                    </fieldset>
                </div>
            <?php } ?>
            </fieldset>
        </div>
    </div>
    <div class="cnt-data aag-padding">
        <div class="columns medium-12">
            <fieldset class="columns fieldset-garage-list p-0">
                <div class="columns medium-12 p-0">
                    <fieldset class="background-primary-color">
                        <div class="columns medium-12" style="padding-top: 5px;padding-bottom: 10px;">
                            <?php if (!empty($debrief_tasks)) { ?>
                                <div class="columns medium-3 p-left-0">
                                    <b class="fs-large">
                                        <?php echo __t('Task.Debrief') . ' / ' . __t('Task.Tasks'); ?>
                                    </b>
                                </div>
                            <?php
                            }
                            if (!$hide_save) {
                                if ($appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::CANCELED && $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::ACCOMPLISHED) {
                                    echo $this->Form->button(
                                        "<span class='aag-icon-editar'></span>" . __t('Appointment.Create_task'),
                                        array(
                                            'escape' => false,
                                            'id' => 'create_task',
                                            'data-open' => "taskModal",
                                            'title' => __t('Appointment.Create_task'),
                                            'class' => 'aag-button medium f-right',
                                        )
                                    );
                                }
                            }
                            ?>
                        </div>
                        <?php
                        if (!empty($debrief_tasks) && !$hide_save) { ?>
                            <div class="columns medium-12 cnt-word-cloud-task listado-word-cloud">
                                <?php
                                for ($i = 0; $i < 8; $i++) {
                                    if (isset($debrief_tasks[$i])) {
                                ?>
                                        <div class="columns medium-3 end">
                                            <div class="word-cloud world-cloud-task" data-task-id="<?php echo $debrief_tasks[$i]['DebriefTask']['id']; ?>" data-no-promt="<?php echo $debrief_tasks[$i]['DebriefTask']['specific_user']; ?>" data-url="<?php echo Router::url(array(
                                                                                                                                                                                                                                                            'controller' => 'tasks',
                                                                                                                                                                                                                                                            'action' => 'ajax_add_debrief_task'
                                                                                                                                                                                                                                                        )); ?>
                                                ">
                                                <div class="word-cloud-title" style="color: #de7b39;" <?php
                                                                                                        echo ' data-garage="' . $debrief_tasks[$i]['DebriefTaskGarage']['id'] . '"';
                                                                                                        echo ' data-contact_list="' . $debrief_tasks[$i]['DebriefTask']['contact_list_id'] . '"';
                                                                                                        echo ' data-user_assigned="' . $debrief_tasks[$i]['DebriefTask']['user_assigned_id'] . '"';
                                                                                                        ?>>
                                                    <?php if ($debrief_tasks[$i]['DebriefTaskGarage']['id'] != null) { ?>
                                                        <span class="ion-android-car" style="padding-right: 5px; color: #de7b39;"></span>
                                                    <?php } elseif ($debrief_tasks[$i]['DebriefTask']['contact_list_id'] != null || $debrief_tasks[$i]['DebriefTask']['user_assigned_id'] != null) { ?>
                                                        <span class="ion-android-person" style="padding-right: 5px; color: #de7b39;"></span>
                                                    <?php } ?>
                                                    <span style="color: #de7b39;">
                                                        <?php echo $debrief_tasks[$i]['DebriefTask']['title' . __s()]; ?>
                                                    </span>
                                                </div>
                                                <i class="ion-checkmark-circled"></i>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </fieldset>
                </div>
                <?php if ($action == ConstantsActionsNames::EDIT) { ?>
                    <div class="row m-top-1 <?php echo empty($tasks) ? "d-none" : ''; ?>" id="my_tasks">
                        <div class="columns medium-12">
                            <fieldset class="columns fieldset-garage-list" style="padding: 0 1em;">
                                <b class="fs-large">
                                    <?php echo __t('Appointment.Tasks'); ?>
                                </b>
                                <div id="cnt_task">
                                    <?php echo $this->element('../Appointments/Elements/tasks'); ?>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                <?php } ?>
            </fieldset>
        </div>
    </div>
    <div class="cnt-data aag-padding" id="cnt_appointments_comments">
        <div class="columns medium-12 p-bottom-1">
            <div class="columns medium-12 p-0">
                <fieldset class="columns fieldset-garage-list p-horizontal-0">
                    <div class="columns medium-12">
                        <div class="aag-subtitle">
                            <?php echo __t('Appointment.Comments'); ?>
                        </div>
                    </div>
                    <?php if ($action == ConstantsActionsNames::EDIT) { ?>
                        <div class="columns medium-2 ta-center">
                            <div class="cnt-avatar-usuario">
                                <?php
                                $image = false;
                                foreach ($users_images as $user_image) {
                                    if (CakeSession::read('Auth.User.id') == $user_image['UserImage']['user_id']) {
                                        echo $this->Html->image(
                                            Router::url(
                                                array(
                                                    'controller' => 'users_images',
                                                    'action' => 'download_file',
                                                    'plugin' => false,
                                                    $user_image['UserImage']['id']
                                                )
                                            ),
                                            array('class' => "img_comment_big")
                                        );
                                        $image = true;
                                    }
                                }
                                if (empty($users_images) || !$image) {
                                    $user = CakeSession::read('Auth.User');
                                    $first = mb_substr($user['Contact']['first_name'], 0, 1, 'UTF-8');
                                    $second = mb_substr($user['Contact']['last_name'], 0, 1, 'UTF-8');
                                    echo $first . $second;
                                }
                                ?>
                            </div>
                        </div>
                        <div class="columns medium-10">
                            <div class="d-inline-block w-100p">
                                <div class="autor-comentario f-left">
                                    <?php echo __t('Appointment.Write_your_comment'); ?>
                                </div>
                            </div>
                            <div class="columns medium-12 p-0">
                                <?php
                                echo $this->Form->input(
                                    'TaskComment.own',
                                    array(
                                        'label' => false,
                                        'type' => 'textarea',
                                        'id' => 'comment-body',
                                        'style' => 'border-radius: 5px;',
                                    )
                                );
                                ?>
                            </div>
                        </div>
                        <?php if (!$hide_save) { ?>
                            <div class="columns medium-12 ta-right m-top-1">
                                <button id="send-comment" class="aag-button medium f-right" data-appointment="<?php echo $appointment['Appointment']['id'] ?>" data-url="<?php echo Router::url(array('controller' => 'appointments_comments', 'action' => 'ajax_add')); ?>">
                                    <span class="ion-paper-airplane"></span>
                                    <?php echo __t('Appointment.Send_comment'); ?>
                                </button>
                            </div>
                        <?php } ?>
                        <span class="ion-chevron-down cursor-pointer m-left-1 <?php echo empty($comments) ? 'd-none' : ''; ?> " id="span-comments">
                            <?php echo __t('Appointment.Show_comments'); ?>
                        </span>
                        <div id="comment-ajax">
                            <?php echo $this->element('../Appointments/Elements/comments'); ?>
                        </div>
                    <?php } ?>
                </fieldset>
            </div>
        </div>
    </div>
<?php } ?>
<div style="display: none;" id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div id="modal_follow_up_visit" class="medium-12 columns p-right-0">
        <?php echo $this->element('../Appointments/Elements/modal_follow_up_visit'); ?>
    </div>
    <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
</div>
<div style="display: none;" id="completedGarageTaskModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div id="modal_completed_garage_task" class="medium-12 columns p-right-0">
        <?php echo $this->element('../Appointments/Elements/modal_completed_garage_task'); ?>
    </div>
    <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
</div>
<div style="display: none;" id="taskModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div class="medium-12 p-1" id="session-msg"></div>
    <div id="modal_form_task" class="medium-12 columns p-right-0">
        <?php echo $this->element('../Appointments/Elements/form_create_task_germany'); ?>
    </div>
    <a class="close-modal zi-100" data-close aria-label="Close">&#215;</a>
</div>
<script>
    <?php if (isset($debrief_tasks_list)) { ?>
        tasks_list_array_js = <?php echo json_encode($debrief_tasks_list) . ';';
                            }
                            if (isset($debrief_topics_list)) {
                                ?>topics_list_array_js = <?php echo json_encode($debrief_topics_list) . ';';
                                                        }
                                                        if (isset($debrief_tasks)) {
                                                            ?> tasks_array_js = <?php echo json_encode($debrief_tasks) . ';';
                                                                            }
                                                                            if (isset($debrief_topics)) {
                                                                                ?> topics_array_js = <?php echo json_encode($debrief_topics) . ';';
                                                                                                    }
                                                                                                        ?>
        $(document).ready(function() {
            $(".objectives_personal_name").select2({
                tags: true
            });
            $("#distributor_name2").select2({
                tags: true
            });
        });
</script>