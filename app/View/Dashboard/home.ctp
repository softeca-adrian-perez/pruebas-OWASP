<?php
echo $this->Html->script('dashboard.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->css('../js/lib/dataTables/css/dataTables.foundation.min.css', array('block' => 'script'));
echo $this->Html->script('lib/dataTables/js/jquery.dataTables.min.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
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
            __t('General.Dashboard')
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
    </div>
</div>
<div class="cnt-data aag-padding">
    <?php
    echo $this->element('Comun/loader');
    $config = CakeSession::read('Auth.User.Config');
    ?>
    <div class="cnt-widgets-crm">
        <div>
            <div class="aag-title m-bottom-1">
                <?php echo __t('CRM.Next_appointment_visit') ?>
            </div>
            <div class="medium-12 columns m-top-1 p-0">
                <div class="o-auto">
                    <table class="table-tracking">
                        <thead>
                            <tr>
                                <th class="ta-center" width="100"><?php echo __t('CRM.Follow'); ?></th>
                                <th width="100"><?php echo __t('CRM.Appointment_type'); ?></th>
                                <th class="ta-center" width="100"><?php echo __t('CRM.Date') . '<br>' . __t('Event.Start_time'); ?></th>
                                <th><?php echo __t('CRM.Name'); ?></th>
                                <th><?php echo __t('Garage.Contact'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visits as $visit) {
                                if ($visit['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
                                    $action = 'edit_event';
                                } else {
                                    $action = 'edit';
                                } ?>
                                <tr>
                                    <td class="ta-center">
                                        <?php
                                        if ($visit['Appointment']['requires_follow_up']) {
                                        ?> <span class="icon-follow" style="color: #eba216"></span> <?php
                                                                                                    } else {
                                                                                                        ?> <span class="icon-follow"></span> <?php
                                                                                                    }
                                                                                    ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($visit['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::EVENT) {
                                            echo __t('Appointment.Appointment');
                                        } else {
                                            echo __t('Appointment.Event');
                                        }
                                        ?>
                                    </td>
                                    <td class="ta-center">
                                        <?php
                                        if ($visit['Appointment']['date'] != '0000-00-00') {
                                            echo Fecha::toFormatoVistaFecha($visit['Appointment']['date']) . '<br>' . $visit['Appointment']['start_time'];
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($visit['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) { ?>
                                            <?php echo $this->Html->link(
                                                $visit['Appointment']['title'],
                                                array(
                                                    'controller' => 'appointments',
                                                    'action' => $action,
                                                    $visit['Appointment']['id']
                                                ),
                                                array(
                                                    'class' => 'c-primary'
                                                )
                                            ); ?>
                                        <?php } else { ?>
                                            <?php if (!empty($visit['Appointment']['garage_id'])) { ?>
                                                <span class="d-inline-block icon-garages"></span>
                                                <?php
                                                if (strlen($visit['Garage']['name']) > 30) {
                                                    echo $this->Html->link(
                                                        substr($visit['Garage']['name'], 0, 30) . '...',
                                                        array(
                                                            'controller' => 'appointments',
                                                            'action' => $action,
                                                            $visit['Appointment']['id']
                                                        ),
                                                        array(
                                                            'class' => 'c-primary d-inline'
                                                        )
                                                    );
                                                } else {
                                                    echo $this->Html->link(
                                                        $visit['Garage']['name'],
                                                        array(
                                                            'controller' => 'appointments',
                                                            'action' => $action,
                                                            $visit['Appointment']['id']
                                                        ),
                                                        array(
                                                            'class' => 'c-primary'
                                                        )
                                                    );
                                                }
                                            }
                                            if (!empty($visit['Appointment']['distributor_id'])) {
                                                ?> <span class='icon-distributors'></span> <?php
                                                                                        if (strlen($visit['Distributor']['name']) > 30) {
                                                                                            echo $this->Html->link(
                                                                                                substr($visit['Distributor']['name'], 0, 30) . '...',
                                                                                                array(
                                                                                                    'controller' => 'appointments',
                                                                                                    'action' => $action,
                                                                                                    $visit['Appointment']['id']
                                                                                                ),
                                                                                                array(
                                                                                                    'class' => 'c-primary'
                                                                                                )
                                                                                            );
                                                                                        } else {
                                                                                            echo $this->Html->link(
                                                                                                $visit['Distributor']['name'],
                                                                                                array(
                                                                                                    'controller' => 'appointments',
                                                                                                    'action' => $action,
                                                                                                    $visit['Appointment']['id']
                                                                                                ),
                                                                                                array(
                                                                                                    'class' => 'c-primary'
                                                                                                )
                                                                                            );
                                                                                        }
                                                                                    }
                                                                                        ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($visit['Appointment']['visit_contact_id']) {
                                            echo h($visit['Contact'][$visit['Appointment']['visit_contact_id']]);
                                        } else if ($visit['Appointment']['visit_contact_name']) {
                                            echo h($visit['Appointment']['visit_contact_name']);
                                        } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="cnt-button-see-all">
                <?php
                echo $this->Html->link(
                    __t('CRM.See_all'),
                    array(
                        'controller' => 'appointments',
                        'action' => 'home_list',
                    ),
                    array('class' => 'aag-button small three')
                );
                ?>
            </div>
        </div>
        <div>
            <div class="aag-title m-bottom-1">
                <?php echo __t('CRM.My_open_task') ?>
            </div>
            <div class="aag-subtabs">
                <ul class="aag-subtabs darken">
                    <li>
                        <input id="tab-assigned" class="d-none" name="tabs-task" checked="" type="radio">
                        <label for="tab-assigned">
                            <?php echo __t('Task.Assigned_to_me'); ?>
                            <div> <?php echo $assigned_open_tasks; ?> </div>
                        </label>
                    </li>
                    <li>
                        <input id="tab-created" class="d-none" name="tabs-task" type="radio">
                        <label for="tab-created">
                            <?php echo __t('Task.Created_by_me'); ?>
                            <div> <?php echo $created_open_tasks; ?> </div>
                        </label>
                    </li>
                    <?php
                    if ($config[ConstantsConfig::ASSIGN_TO_GROUP]) {
                    ?>
                        <li>
                            <input id="tab-assigned-group" class="d-none" name="tabs-task" type="radio">
                            <label for="tab-assigned-group">
                                <?php echo __t('Task.Assigned_to_my_group'); ?>
                                <div>
                                    <?php echo $assigned_group_open_tasks; ?>
                                </div>
                            </label>
                        </li>
                    <?php
                    }
                    ?>
                    <li>
                        <input id="tab-assigned-customers" class="d-none" name="tabs-task" type="radio">
                        <label for="tab-assigned-customers">
                            <?php echo __t('Task.Assigned_to_my_customers') ?>
                            <div> <?php echo $assigned_customers_open_tasks; ?> </div>
                            <span class="tooltip_help">
                                <a href="javascript:;" title="<?php echo __t('Task.Manager_to_customer'); ?>">
                                    <span class="ion-help-circled"></span>
                                </a>
                            </span>
                        </label>
                    </li>
                </ul>

                <div class="medium-12 columns p-0 p-top-1" id="form-assigned">
                    <div class="medium-12 columns p-0 ta-right">
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-sort_by_exp_date c-primary"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'title' => __t('CRM.Sort_by_recently_added'),
                                    'id' => 'sort-recently-added',
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_assigned_tasks_by_recently_added',
                                        $user
                                    )),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_to' => $user,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'tab' => 'tab-assigned-task'
                                        ),
                                    ))
                                )
                            ); ?>
                        </div>
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-sort_by_due_this_week"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'id' => 'sort-due-this-week',
                                    'title' => __t('CRM.Sort_by_due_this_week'),
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_assigned_tasks_by_due_week',
                                        $user
                                    )),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_to' => $user,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'limit_date_from' => Fecha::toFormatoVistaFecha(date('Y-m-d', strtotime('monday this week'))),
                                            'limit_date_to' => Fecha::toFormatoVistaFecha(date('Y-m-d', strtotime('sunday this week'))),
                                            'tab' => 'tab-assigned-task'
                                        )
                                    ))
                                )
                            ); ?>
                        </div>
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-calendar_sbd"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'id' => 'sort-due-today',
                                    'title' => __t('CRM.Sort_by_due_today'),
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_assigned_tasks_by_due_today',
                                        $user
                                    )),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_to' => $user,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'limit_date_from' => Fecha::toFormatoVistaFecha(date('Y-m-d')),
                                            'limit_date_to' => Fecha::toFormatoVistaFecha(date('Y-m-d')),
                                            'tab' => 'tab-assigned-task'
                                        )
                                    ))
                                )
                            );
                            ?>
                        </div>
                    </div>
                    <div id="table-assigned">
                        <?php echo $this->element('../Dashboard/Elements/results_table_assigned'); ?>
                    </div>
                </div>

                <div class="medium-12 columns p-0 p-top-1 d-none" id="form-created">
                    <div class="medium-12 columns p-0 ta-right">
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-sort_by_exp_date c-primary"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'title' => __t('CRM.Sort_by_recently_added'),
                                    'id' => 'sort-recently-added-created',
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_created_tasks_by_recently_added',
                                        $user
                                    )),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_to' => $user,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'tab' => 'tab-created-task'
                                        ),
                                    ))
                                )
                            ); ?>
                        </div>
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-sort_by_due_this_week"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'id' => 'sort-due-this-week-created',
                                    'title' => __t('CRM.Sort_by_due_this_week'),
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_created_tasks_by_due_week',
                                        $user
                                    )),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_to' => $user,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'limit_date_from' => Fecha::toFormatoVistaFecha(date('Y-m-d', strtotime('monday this week'))),
                                            'limit_date_to' => Fecha::toFormatoVistaFecha(date('Y-m-d', strtotime('sunday this week'))),
                                            'tab' => 'tab-created-task'
                                        )
                                    ))
                                )
                            ); ?>
                        </div>
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-calendar_sbd"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'id' => 'sort-due-today-created',
                                    'title' => __t('CRM.Sort_by_due_today'),
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_created_tasks_by_due_today',
                                        $user
                                    )),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_to' => $user,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'limit_date_from' => Fecha::toFormatoVistaFecha(date('Y-m-d')),
                                            'limit_date_to' => Fecha::toFormatoVistaFecha(date('Y-m-d')),
                                            'tab' => 'tab-created-task'
                                        )
                                    ))
                                )
                            );
                            ?>
                        </div>
                    </div>
                    <div id="table-created">
                        <?php echo $this->element('../Dashboard/Elements/results_table_created'); ?>
                    </div>
                </div>

                <div class="medium-12 columns p-0 d-none p-top-1" id="form-assigned-group">

                    <div class="medium-12 columns p-0 ta-right">
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-sort_by_exp_date c-primary"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'title' => __t('CRM.Sort_by_recently_added'),
                                    'id' => 'sort-recently-added-group',
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_assigned_group_tasks_by_recently_added',
                                        $user
                                    )),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_group' => ConstantsBooleans::ACTIVE,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'tab' => 'tab-assigned-group-task'
                                        ),
                                    ))
                                )
                            ); ?>
                        </div>
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-sort_by_due_this_week"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'id' => 'sort-due-this-week-group',
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_assigned_group_tasks_by_due_week',
                                        $user
                                    )),
                                    'title' => __t('CRM.Sort_by_due_this_week'),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_group' => ConstantsBooleans::ACTIVE,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'limit_date_from' => Fecha::toFormatoVistaFecha(date('Y-m-d', strtotime('monday this week'))),
                                            'limit_date_to' => Fecha::toFormatoVistaFecha(date('Y-m-d', strtotime('sunday this week'))),
                                            'tab' => 'tab-assigned-group-task'
                                        ),
                                    ))
                                )
                            ); ?>
                        </div>
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-calendar_sbd"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'id' => 'sort-due-today-group',
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_assigned_group_tasks_by_due_today',
                                        $user
                                    )),
                                    'title' => __t('CRM.Sort_by_due_today'),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_group' => ConstantsBooleans::ACTIVE,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'limit_date_from' => Fecha::toFormatoVistaFecha(date('Y-m-d')),
                                            'limit_date_to' => Fecha::toFormatoVistaFecha(date('Y-m-d')),
                                            'tab' => 'tab-assigned-group-task'
                                        ),
                                    ))
                                )
                            );
                            ?>
                        </div>
                    </div>
                    <div id="table-assigned-group">
                        <?php echo $this->element('../Dashboard/Elements/results_table_assigned_group'); ?>
                    </div>
                </div>

                <div class="medium-12 columns p-0 d-none p-top-1" id="form-assigned-customers">

                    <div class="medium-12 columns p-0 ta-right">
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-sort_by_exp_date c-primary"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'title' => __t('CRM.Sort_by_recently_added'),
                                    'id' => 'sort-recently-added-customer',
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_assigned_customer_tasks_by_recently_added',
                                        $user
                                    )),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_customers' => ConstantsBooleans::ACTIVE,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'tab' => 'tab-assigned-customers-task'
                                        ),
                                    ))
                                )
                            ); ?>
                        </div>
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-sort_by_due_this_week"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'title' => __t('CRM.Sort_by_due_this_week'),
                                    'id' => 'sort-due-this-week-customer',
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_assigned_customer_tasks_by_due_week',
                                        $user
                                    )),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_customers' => ConstantsBooleans::ACTIVE,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'limit_date_from' => Fecha::toFormatoVistaFecha(date('Y-m-d', strtotime('monday this week'))),
                                            'limit_date_to' => Fecha::toFormatoVistaFecha(date('Y-m-d', strtotime('sunday this week'))),
                                            'tab' => 'tab-assigned-customers-task'
                                        ),
                                    ))
                                )
                            ); ?>
                        </div>
                        <div class="d-inline-block p-right-1">
                            <?php echo $this->Html->link(
                                '<span class="icon-calendar_sbd"></span>',
                                array(),
                                array(
                                    'escape' => false,
                                    'title' => __t('CRM.Sort_by_due_today'),
                                    'id' => 'sort-due-today-customer',
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_sort_assigned_customer_tasks_by_due_today',
                                        $user
                                    )),
                                    'data-url-search' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'home',
                                        '?' => array(
                                            'assigned_customers' => ConstantsBooleans::ACTIVE,
                                            'task_status_id' => ConstantsStatusTasks::PENDING,
                                            'limit_date_from' => Fecha::toFormatoVistaFecha(date('Y-m-d')),
                                            'limit_date_to' => Fecha::toFormatoVistaFecha(date('Y-m-d')),
                                            'tab' => 'tab-assigned-customers-task'
                                        ),
                                    ))
                                )
                            );
                            ?>
                        </div>
                    </div>
                    <div id="table-assigned-customer">
                        <?php echo $this->element('../Dashboard/Elements/results_table_assigned_customers'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="aag-title m-bottom-1">
                <?php echo __t('CRM.Visits_requiring_feedback') ?>
            </div>
            <div class="medium-12 columns m-top-1 p-0">
                <div class="o-auto">
                    <table class="table-tracking">
                        <thead>
                            <tr>
                                <th class="ta-center" width="100"><?php echo __t('CRM.Follow'); ?></th>
                                <th class="ta-center" width="100"><?php echo __t('CRM.Date'); ?></th>
                                <th><?php echo __t('CRM.Name'); ?></th>
                                <th><?php echo __t('Garage.Contact'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visits_without_feedback as $visit_feedback) { ?>
                                <tr>
                                    <td class="ta-center">
                                        <?php
                                        if ($visit_feedback['Appointment']['requires_follow_up']) {
                                        ?><span class="icon-follow" style="color: #eba216"></span> <?php
                                                                                                        } else {
                                                                                                            ?> <span class="icon-follow"></span> <?php
                                                                                                        }
                                                                                        ?>
                                    </td>
                                    <td class="ta-center">
                                        <?php
                                        if ($visit_feedback['Appointment']['date'] != '0000-00-00') {
                                            echo Fecha::toFormatoVistaFecha($visit_feedback['Appointment']['date']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($visit_feedback['Garage']['name']) {
                                        ?>
                                            <span class="d-inline-block icon-garages"></span>
                                            <?php
                                            if (strlen($visit_feedback['Garage']['name']) > 30) {
                                                echo $this->Html->link(
                                                    substr($visit_feedback['Garage']['name'], 0, 30) . '...',
                                                    array(
                                                        'controller' => 'appointments',
                                                        'action' => 'edit',
                                                        $visit_feedback['Appointment']['id']
                                                    ),
                                                    array(
                                                        'class' => 'c-primary '
                                                    )
                                                );
                                            } else {
                                                echo $this->Html->link(
                                                    $visit_feedback['Garage']['name'],
                                                    array(
                                                        'controller' => 'appointments',
                                                        'action' => 'edit',
                                                        $visit_feedback['Appointment']['id']
                                                    ),
                                                    array(
                                                        'class' => 'c-primary'
                                                    )
                                                );
                                            }
                                        }
                                        if ($visit_feedback['Distributor']['name']) {
                                            ?> <span class='icon-distributors'></span> <?php
                                                                                        if (strlen($visit_feedback['Distributor']['name']) > 30) {
                                                                                            echo $this->Html->link(
                                                                                                substr($visit_feedback['Distributor']['name'], 0, 30) . '...',
                                                                                                array(
                                                                                                    'controller' => 'appointments',
                                                                                                    'action' => 'edit',
                                                                                                    $visit_feedback['Appointment']['id']
                                                                                                ),
                                                                                                array(
                                                                                                    'class' => 'c-primary'
                                                                                                )
                                                                                            );
                                                                                        } else {
                                                                                            echo $this->Html->link(
                                                                                                $visit_feedback['Distributor']['name'],
                                                                                                array(
                                                                                                    'controller' => 'appointments',
                                                                                                    'action' => 'edit',
                                                                                                    $visit_feedback['Appointment']['id']
                                                                                                ),
                                                                                                array(
                                                                                                    'class' => 'c-primary'
                                                                                                )
                                                                                            );
                                                                                        }
                                                                                    }
                                                                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($visit_feedback['Appointment']['visit_contact_id']) {
                                            echo h($visit_feedback['Contact'][$visit_feedback['Appointment']['visit_contact_id']]);
                                        } else if ($visit_feedback['Appointment']['visit_contact_name']) {
                                            echo h($visit_feedback['Appointment']['visit_contact_name']);
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="cnt-button-see-all">
                <?php
                echo $this->Html->link(
                    __t('CRM.See_all'),
                    array(
                        'controller' => 'appointments',
                        'action' => 'home_list',
                        '?' => array(
                            'feedback_fill_up' => ConstantsBooleans::NO,
                            'user_assigned_id' => CakeSession::read('Auth.User.id'),
                            'appointment_status_id' => [ConstantsStatusAppointments::PENDING]
                        )
                    ),
                    array('class' => 'aag-button small three')
                );
                ?>
            </div>
        </div>
        <div>
            <div class="aag-title m-bottom-1">
                <?php echo __t('CRM.Visits_appointmets_requiring_follow_up') ?>
            </div>
            <div class="medium-12 columns m-top-1 p-0">
                <div class="o-auto">
                    <table class="table-tracking">
                        <thead>
                            <tr>
                                <th class="ta-center" width="100"><?php echo __t('CRM.Follow'); ?></th>
                                <th width="100"><?php echo __t('CRM.Appointment_type'); ?></th>
                                <th class="ta-center" width="100"><?php echo __t('CRM.Date'); ?></th>
                                <th><?php echo __t('CRM.Name'); ?></th>
                                <th><?php echo __t('Garage.Contact'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visits_requires as $visit_require) {

                                if ($visit_require['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
                                    $action = 'edit_event';
                                } else {
                                    $action = 'edit';
                                }
                            ?>
                                <tr>
                                    <td class="ta-center">
                                        <?php
                                        if ($visit_require['Appointment']['requires_follow_up']) {
                                        ?> <span class="icon-follow" style="color: #eba216"></span> <?php
                                                                                                        } else {
                                                                                                            ?> <span class="icon-follow"></span> <?php
                                                                                                        }
                                                                                        ?>
                                    </td>
                                    <td class="ta-center">
                                        <?php
                                        if ($visit_require['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::EVENT) {
                                            echo __t('Appointment.Appointment');
                                        } else {
                                            echo __t('Appointment.Event');
                                        }
                                        ?>
                                    </td>
                                    <td class="ta-center">
                                        <?php
                                        if ($visit_require['Appointment']['date'] != '0000-00-00') {
                                            echo Fecha::toFormatoVistaFecha($visit_require['Appointment']['date']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($visit_require['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) { ?>
                                            <?php echo $this->Html->link(
                                                $visit_require['Appointment']['title'],
                                                array(
                                                    'controller' => 'appointments',
                                                    'action' => $action,
                                                    $visit_require['Appointment']['id']
                                                ),
                                                array(
                                                    'class' => 'c-primary'
                                                )
                                            ); ?>
                                        <?php } else { ?>
                                            <?php if ($visit_require['Garage']['name']) { ?>
                                                <span class="d-inline-block icon-garages"></span>
                                                <?php if (strlen($visit_require['Garage']['name']) > 30) {
                                                    echo $this->Html->link(
                                                        substr($visit_require['Garage']['name'], 0, 30) . '...',
                                                        array(
                                                            'controller' => 'appointments',
                                                            'action' => $action,
                                                            $visit_require['Appointment']['id']
                                                        ),
                                                        array(
                                                            'class' => 'c-primary'
                                                        )
                                                    );
                                                } else {
                                                    echo $this->Html->link(
                                                        $visit_require['Garage']['name'],
                                                        array(
                                                            'controller' => 'appointments',
                                                            'action' => $action,
                                                            $visit_require['Appointment']['id']
                                                        ),
                                                        array(
                                                            'class' => 'c-primary'
                                                        )
                                                    );
                                                }
                                            }
                                            if ($visit_require['Distributor']['name']) { ?>
                                                <span class='icon-distributors'></span> <?php
                                                                                        if (strlen($visit_require['Distributor']['name']) > 30) {
                                                                                            echo $this->Html->link(
                                                                                                substr($visit_require['Distributor']['name'], 0, 30) . '...',
                                                                                                array(
                                                                                                    'controller' => 'appointments',
                                                                                                    'action' => $action,
                                                                                                    $visit_require['Appointment']['id']
                                                                                                ),
                                                                                                array(
                                                                                                    'class' => 'c-primary'
                                                                                                )
                                                                                            );
                                                                                        } else {
                                                                                            echo $this->Html->link(
                                                                                                $visit_require['Distributor']['name'],
                                                                                                array(
                                                                                                    'controller' => 'appointments',
                                                                                                    'action' => $action,
                                                                                                    $visit_require['Appointment']['id']
                                                                                                ),
                                                                                                array(
                                                                                                    'class' => 'c-primary'
                                                                                                )
                                                                                            );
                                                                                        }
                                                                                    }
                                                                                        ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if (isset($visit_require['Appointment']['visit_contact_id']) && !empty($visit_require['Appointment']['visit_contact_id'])) {
                                            echo h($visit_require['Contact'][$visit_require['Appointment']['visit_contact_id']]);
                                        } elseif (isset($visit_require['Appointment']['visit_contact_name']) && !empty($visit_require['Appointment']['visit_contact_name'])) {
                                            echo h($visit_require['Appointment']['visit_contact_name']);
                                        } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="cnt-button-see-all">
                <?php
                echo $this->Html->link(
                    __t('CRM.See_all'),
                    array(
                        'controller' => 'appointments',
                        'action' => 'home_list',
                        '?' => array(
                            'requires_follow_up' => ConstantsBooleans::YES
                        )
                    ),
                    array('class' => 'aag-button small three')
                );
                ?>
            </div>
        </div>
        <?php
        if ($user_bd['User']['role_id'] != ConstantsRoles::GPC_LOGISTICS_BDM) {
        ?>
            <div>
                <div class="aag-title m-bottom-1">
                    <?php echo __t('CRM.Garages_by_last_visit_date') ?>
                </div>
                <div class="medium-12 columns m-top-1 p-0">
                    <div class="o-auto">
                        <table class="table-tracking">
                            <thead>
                                <tr>
                                    <th><?php echo __t('Garage.Name'); ?></th>
                                    <th><?php echo __t('Garage.Town'); ?></th>
                                    <th><?php echo __t('Garage.Address'); ?></th>
                                    <th><?php echo __t('Visit.Last_visit'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($garages_last_visited as $garage) {
                                    $action = 'report';

                                ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <?php echo $this->Html->link(
                                                    $garage['Garage']['name'],
                                                    array(
                                                        'controller' => 'clients',
                                                        'action' => $action,
                                                        $garage['Garage']['id']
                                                    ),
                                                    array(
                                                        'class' => 'c-primary'
                                                    )
                                                ); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <?php echo h($garage['Garage']['town']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <?php echo h($garage['Garage']['address1']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <?php echo (Fecha::toFormatoVista($garage[0]['max_appointment_date'])) ? Fecha::toFormatoVista($garage[0]['max_appointment_date']) : '--'; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="cnt-button-see-all">
                    <?php
                    echo $this->Html->link(
                        __t('CRM.See_all'),
                        array(
                            'controller' => 'clients',
                            'action' => 'home',
                        ),
                        array('class' => 'aag-button small three')
                    );
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
        <div>
            <div class="aag-title m-bottom-1">
                <?php echo __t('CRM.Distributors_by_last_visit_date') ?>
            </div>
            <div class="medium-12 columns m-top-1 p-0">
                <div class="o-auto">
                    <table class="table-tracking">
                        <thead>
                            <tr>
                                <th><?php echo __t('Distributor.Name'); ?></th>
                                <th><?php echo __t('Distributor.Town'); ?></th>
                                <th><?php echo __t('Distributor.Address'); ?></th>
                                <th><?php echo __t('Visit.Last_visit'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($distributors_last_visited as $distributor) {
                                $action = 'report_distributor'; ?>
                                <tr>
                                    <td>
                                        <div>
                                            <?php echo $this->Html->link(
                                                $distributor['Distributor']['name'],
                                                array(
                                                    'controller' => 'clients',
                                                    'action' => $action,
                                                    $distributor['Distributor']['id']
                                                ),
                                                array(
                                                    'class' => 'c-primary'
                                                )
                                            ); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?php echo h($distributor['Distributor']['town']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?php echo h($distributor['Distributor']['address1']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?php echo (Fecha::toFormatoVista($distributor[0]['max_appointment_date'])) ? Fecha::toFormatoVista($distributor[0]['max_appointment_date']) : '--'; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="cnt-button-see-all">
                <?php echo $this->Html->link(
                    __t('CRM.See_all'),
                    array(
                        'controller' => 'clients',
                        'action' => 'home_distributors',
                    ),
                    array('class' => 'aag-button small three')
                ); ?>
            </div>
        </div>
        <?php


        ?>
    </div>
</div>
<!-- <div style="display: none;" id="myModal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <div id="modal_task" class="medium-12 columns p-right-0">
        <?php // echo $this->element('../Tasks/Elements/ajax_create_task');
        ?>
    </div>
    <a class="close-modal" data-close aria-label="Close">&#215;</a>
</div> -->