<?php
$action = $this->request->action;
echo $this->Html->script('tasks_germany.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Task',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'task-form'
    )
);
echo $this->Form->hidden(
    'FormController',
    array(
        'id' => 'form-controller',
        'value' => $this->request->controller
    )
);
echo $this->Form->hidden('reason', array());
echo $this->Form->hidden('User.user_id', array('value' => null));
echo $this->Form->hidden('ContactContactList.contact_list_id');
echo $this->Form->hidden('TaskGarage.garage_id');
echo $this->Form->hidden(
    'Task.user_assigned_id',
    array(
        'id' => 'user_assigned_id',
        'value' => isset($this->request->data['Task']['user_assigned_id']) ? $this->request->data['Task']['user_assigned_id'] : ''
    )
);
echo $this->Form->hidden(
    'TaskId',
    array(
        'id' => 'task_id',
        'value' => isset($this->request->data['Task']['id']) ? $this->request->data['Task']['id'] : ''
    )
);
echo $this->Form->hidden('Task.send_to', array('id' => 'send_to', 'value' => ConstantsTasks::USER));
$default_check = '';
$dis_check = '';
$gar_check = '';
$user_check = '';
if ($action == ConstantsActionsNames::ADD) {
    $default_check = 'checked';
} else {
    if ($action == ConstantsActionsNames::EDIT && (isset($task_distributors) && !empty($task_distributors))) {
        // check var
        $dis_check = 'checked';
    } elseif ($action == ConstantsActionsNames::EDIT && (isset($task_garages) && !empty($task_garages))) {
        // check var
        $gar_check = 'checked';
    } elseif ($action == ConstantsActionsNames::EDIT) {
        // check var
        $user_check = 'checked';
    }
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
                    __t('Task.Tasks'),
                    array(
                        'controller' => 'tasks',
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
                    __t('Task.Tasks'),
                    array(
                        'controller' => 'tasks',
                        'action' => 'home'
                    )
                ),
                __t('General.Edit'),
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
        <div class="f-right ta-right cont-checkbox">
            <?php
            if (isset($task['Task']['id']) && $task['Task']['task_status_id'] != ConstantsStatusTasks::COMPLETED) {
                if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
                    echo $this->Html->link(
                        "<span class='aag-icon-papelera'></span>" . __t('Task.Delete_task'),
                        array(
                            'controller' => 'tasks',
                            'action' => 'delete_task',
                            $task['Task']['id']
                        ),
                        array(
                            'escape' => false,
                            'title' => __t('Task.Delete_task'),
                            'id' => 'task_delete',
                            'class' => 'aag-button medium red outlined f-right m-left-1',
                        )
                    );
                }
            }
            $config = CakeSession::read('Auth.User.Config');
            if ($config[ConstantsConfig::CHECK_SEND_NOTIFICATION]) {
            ?>
                <label id="generate_alerts" class="label-w-auto f-right">
                    <?php echo $this->Form->input(
                        'Notification.generate_alerts',
                        array(
                            'type' => 'checkbox',
                            'label' => false,
                            'checked' => true,
                            'div' => false,
                            'hidden' => true
                        )
                    ); ?>
                    <span class="generate_alerts unselectable"><?php echo __t('Task.Generate_alerts'); ?></span>
                </label>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<div class="cnt-data" style="background: none;">
    <div class="aag-title">
        <?php echo __t('Task.Tasks') ?>
    </div>
    <div class="aag-subtitle">
        <?php echo __t('Task.Assigned_to'); ?>
    </div>
</div>
<div class="aag-tabs" data-action="<?php echo $this->request->action ?>">
    <ul>
        <li>
            <input type="radio" class="tasks_types d-none" name="task_type" id="task_type_user" data-mindate="<?php echo date('d-m-Y'); ?>" <?php echo $default_check;
                                                                                                                                            echo $user_check; ?>>
            <label for="task_type_user"><?php echo __t('User.Users'); ?></label>
        </li>
        <li>
            <input type="radio" class="tasks_types d-none" name="task_type" id="task_type_garage" <?php echo $gar_check; ?>>
            <label for="task_type_garage"><?php echo __t('Garage.Garages'); ?></label>
        </li>
        <li>
            <input type="radio" class="tasks_types d-none" name="task_type" id="task_type_distributor" <?php echo $dis_check; ?>>
            <label for="task_type_distributor"><?php echo __t('Distributor.Distributors'); ?></label>
        </li>
    </ul>
</div>
<div class="cnt-data aag-padding position-relative">
    <div class="columns medium-12 m-bottom-1 d-none cnt-assigned_to_distributor " id="assigned_to_distributor">
        <div class="cnt-form-search m-0-i">
            <div class="cnt-form-search-title">
                <?php echo __t('Garage.Filters'); ?>
            </div>
            <div class="cnt-form-inputs">
                <?php
                if ($this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID || $this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID) {
                    $default_value = $this->Session->read('Auth.User.contact_id');
                    $disabled_value = true;
                } else {
                    $default_value = null;
                    $disabled_value = false;
                }
                echo $this->Form->input(
                    'DistributorFilter.RSM',
                    array(
                        'label' => __t('Garage.RSM'),
                        'class' => 'select2-multiple',
                        'type' => 'select',
                        'multiple' => false,
                        'empty' => true,
                        'options' => $distributor_rsm,
                        'id' => 'distributor_filter_rsm',
                    )
                );
                echo $this->Form->input(
                    'DistributorFilter.BDM',
                    array(
                        'label' => __t('Garage.BDM'),
                        'class' => 'select2-multiple',
                        'type' => 'select',
                        'multiple' => false,
                        'empty' => true,
                        'options' => $distributor_bdm,
                        'id' => 'distributor_filter_bdm',
                    )
                );
                echo $this->Form->input(
                    'trading_group_id',
                    array(
                        'label' => __t('Distributor.Trading_groups'),
                        'type' => 'select',
                        'class' => 'select2-multiple clear_field',
                        'options' => isset($trading_groups) ? $trading_groups : '',
                        'empty' => true,
                        'id' => 'trading_group_id'
                    )
                );
                echo $this->Form->input(
                    'activity_id',
                    array(
                        'label' => __t('Distributor.Activity'),
                        'type' => 'select',
                        'class' => 'select2-multiple clear_field',
                        'options' => isset($distributor_activities) ? $distributor_activities : '',
                        'empty' => true,
                        'id' => 'activity_id'
                    )
                );
                ?>
            </div>
        </div>
        <div class="columns medium-12 p-0 m-top-1">
            <?php
            $value = array();
            if (!empty($task_distributors)) {
                foreach ($task_distributors as $key => $distributor) {
                    $value[] = $key;
                }
            } else if (!isset($task_distributors)) {
                $task_distributors = array();
            }
            echo $this->Form->input(
                'TaskDistributor.distributor_id',
                array(
                    'label' => array(
                        'text' => __t('Distributor.Distributor'),
                        'style' => 'position: relative; top: -3px;'
                    ),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => true,
                    'empty' => true,
                    'options' => $task_distributors,
                    'value' => $value,
                    'id' => 'distributor_name_assigned_to',
                    'data-url' => Router::url(array(
                        'controller' => 'distributors',
                        'action' => 'ajax_get_distributors'
                    )),
                )
            );
            ?>
        </div>
    </div>
    <div class="columns medium-12 m-bottom-1 d-none cnt-assigned_to_garage" id="assigned_to_garage">
        <div class="cnt-form-search m-0-i">
            <div class="cnt-form-search-title">
                <?php echo __t('Garage.Filters'); ?>
            </div>
            <div class="cnt-form-inputs">
                <?php
                echo $this->Form->input(
                    'GarageFilter.branch',
                    array(
                        'label' => __t('Distributor.Distributors'),
                        'class' => 'clear_field dynamicSelect2_distributors',
                        'type' => 'select',
                        'multiple' => false,
                        'empty' => true,
                        'id' => 'garage_filter_branch',
                    )
                );
                if ($this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID || $this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID) {
                    $default_value = $this->Session->read('Auth.User.contact_id');
                    $disabled_value = true;
                } else {
                    $default_value = null;
                    $disabled_value = false;
                }
                echo $this->Form->input(
                    'GarageFilter.RSM',
                    array(
                        'label' => __t('Garage.RSM'),
                        'class' => 'select2-multiple',
                        'type' => 'select',
                        'multiple' => false,
                        'empty' => true,
                        'options' => $garage_rsm,
                        'id' => 'garage_filter_rsm',
                    )
                );
                if (
                    $this->Session->read('Auth.User.role_id') == ConstantsRoles::GPC_LOGISTICS_BDM ||
                    $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG ||
                    $this->Session->read('Auth.User.roles_id') == ConstantsRoles::BDM_TG
                ) {
                    $default_value = $this->Session->read('Auth.User.contact_id');
                    $disabled_value = true;
                } else {
                    $default_value = null;
                    $disabled_value = false;
                }
                echo $this->Form->input(
                    'GarageFilter.BDM',
                    array(
                        'label' => __t('Garage.BDM'),
                        'class' => 'select2-multiple',
                        'type' => 'select',
                        'multiple' => false,
                        'empty' => true,
                        'options' => $garage_bdm,
                        'id' => 'garage_filter_bdm',
                    )
                );
                echo $this->Form->input(
                    'GarageFilter.Customer_status',
                    array(
                        'label' => __t('Garage.Customer_status'),
                        'class' => 'select2-multiple',
                        'type' => 'select',
                        'multiple' => false,
                        'empty' => true,
                        'options' => $garage_customer_status,
                        'id' => 'garage_filter_customer_status',
                    )
                );
                ?>
            </div>
        </div>
        <div class="columns medium-12 p-0 m-top-1">
            <?php
            $value = array();
            if (!empty($task_garages)) {
                foreach ($task_garages as $key => $garage) {
                    $value[] = $key;
                }
            }
            echo $this->Form->input(
                'TaskGarage.garage_id',
                array(
                    'label' => array(
                        'text' => __t('Garage.Garage'),
                        'style' => 'position: relative; top: -3px;'
                    ),
                    'class' => 'select2-multiple',
                    'type' => 'select',
                    'multiple' => true,
                    'empty' => true,
                    'options' => $task_garages,
                    'value' => $value,
                    'id' => 'garage_name_assigned_to',
                    'data-url' => Router::url(array(
                        'controller' => 'garages',
                        'action' => 'ajax_get_garages',
                        $user_aag_region_id
                    ))
                )
            );
            ?>
        </div>
    </div>
    <div id="assigned_to_contact">
        <?php
        $config = CakeSession::read('Auth.User.Config');
        if ($config[ConstantsConfig::ASSIGN_TO_GROUP]) {
        ?>
            <div class="columns medium-6 m-bottom-1">
                <?php
                echo $this->Form->input(
                    'ContactList.contact_list_id',
                    array(
                        'label' => __t('Task.Contact_lists'),
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'options' => $contact_lists,
                        'empty' => true,
                        'required' => true,
                        'id' => 'contact_list',
                        'data-url' => Router::url(array(
                            'controller' => 'tasks',
                            'action' => 'ajax_get_users'
                        ))
                    )
                );
                ?>
            </div>
        <?php
        }
        ?>
        <div class="columns medium-6 m-bottom-1 m-top-1 end">
            <div id="assigned_to" class="fields_titles_forms p-top-1">
                <?php echo $this->element('../Tasks/Elements/form_assigned_task', array('user_assigned_id' => isset($array_user_name) ? $array_user_name : array())); ?>
            </div>
        </div>
    </div>
    <div class="columns medium-6 m-bottom-1 required clear">
        <?php
        echo $this->Form->input(
            'Task.title',
            array(
                'type' => 'text',
                'required' => true,
                'id' => 'title',
                'label' => __t('Task.Title'),
            )
        );
        ?>
    </div>
    <div class="columns medium-3 m-bottom-1">
        <?php
        if (isset($this->request->data['Task']) && $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED) {
            echo $this->Form->input(
                'Task.limit_date',
                array(
                    'type' => 'text',
                    'required' => true,
                    'class' => 'fecha-js',
                    'id' => 'due-date',
                    'disabled' => true,
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Task.Deadline'),
                )
            );
            echo $this->Form->hidden('Task.limit_date');
        } else {
            echo $this->Form->input(
                'Task.limit_date',
                array(
                    'type' => 'text',
                    'required' => true,
                    'class' => 'fecha-js',
                    'id' => 'due-date',
                    'div' => array(
                        'class' => 'datepicker datepicker-label-block',
                    ),
                    'label' => __t('Task.Deadline'),
                )
            ); ?>
        <?php
        }
        ?>
    </div>
    <?php
    if ($this->request->action == 'add') {
        echo $this->Form->hidden(
            'task_status_id',
            array(
                'id' => 'task-status-hidden',
                'value' => ConstantsStatusTasks::PENDING,
                'data-status-expired' => ConstantsStatusTasks::EXPIRED
            )
        );
    ?>
        <div class="columns medium-3 m-bottom-1" id="task-status-no-expired">
            <?php
            echo $this->Form->input(
                'TaskStatusId',
                array(
                    'label' => __t('General.Status'),
                    'type' => 'select',
                    'class' => 'select2-multiple select',
                    'options' => $task_status_list,
                    'id' => 'select-status-js',
                    'default' => ConstantsStatusTasks::PENDING,
                    'disabled' => true,
                )
            );
            ?>
        </div>
        <div class="columns medium-3 m-bottom-1 d-none" id="task-status-expired">
            <div class="medium-4 columns p-0">
                <label>
                    <?php echo __t('Task.Status'); ?>
                </label>
            </div>
            <?php
            echo $this->Form->input(
                'TaskStatusId',
                array(
                    'label' => false,
                    'type' => 'select',
                    'class' => 'select2-multiple',
                    'options' => $task_status_expired,
                    'default' => 1,
                    'disabled' => true,
                )
            );
            ?>
        </div>
        <?php
    } else {
        if ($task['Task']['task_status_id'] == ConstantsStatusTasks::EXPIRED) {
            echo $this->Form->hidden(
                'task_status_id',
                array(
                    'id' => 'task-status-hidden',
                    'value' => ConstantsStatusTasks::EXPIRED,
                    'data-status-expired' => ConstantsStatusTasks::EXPIRED,
                    'data-status-pending' => ConstantsStatusTasks::PENDING
                )
            );
        ?>
            <div class="columns medium-3 m-bottom-1 d-none" id="task-status-no-expired">
                <?php
                echo $this->Form->input(
                    'TaskStatusId',
                    array(
                        'label' => __t('General.Status'),
                        'type' => 'select',
                        'class' => 'select2-multiple select',
                        'options' => $task_status_list,
                        'id' => 'select-status-js',
                        'default' => ConstantsStatusTasks::PENDING,
                        'disabled' => true
                    )
                );
                ?>
            </div>
            <div class="columns medium-3 m-bottom-1" id="task-status-expired">
                <div class="medium-4 columns p-0">
                    <label>
                        <?php echo __t('Task.Status'); ?>
                    </label>
                </div>
                <?php
                echo $this->Form->input(
                    'TaskStatusId',
                    array(
                        'label' => false,
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'options' => $task_status_expired,
                        'default' => 1,
                        'disabled' => true,
                    )
                );
                ?>
            </div>
        <?php
        } else {
            echo $this->Form->hidden(
                'task_status_id',
                array(
                    'id' => 'task-status-hidden',
                    'value' => $this->request->data['Task']['task_status_id'],
                    'data-status-expired' => ConstantsStatusTasks::EXPIRED,
                    'data-status-pending' => ConstantsStatusTasks::PENDING
                )
            );
        ?>
            <div class="columns medium-3 m-bottom-1" id="task-status-no-expired">
                <?php
                echo $this->Form->input(
                    'TaskStatusId',
                    array(
                        'label' => __t('General.Status'),
                        'type' => 'select',
                        'class' => 'select2-multiple select',
                        'options' => $task_status_list,
                        'id' => 'select-status-js',
                        'default' => $this->request->data['Task']['task_status_id'],
                        'disabled' => true
                    )
                );
                ?>
            </div>
            <div class="columns medium-3 m-bottom-1 d-none" id="task-status-expired">
                <div class="medium-4 columns p-0">
                    <label>
                        <?php echo __t('Task.Status'); ?>
                    </label>
                </div>
                <?php
                echo $this->Form->input(
                    'TaskStatusId',
                    array(
                        'label' => false,
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'options' => $task_status_expired,
                        'default' =>  ConstantsStatusTasks::COMPLETED,
                        'disabled' => true,
                    )
                );
                ?>
            </div>
    <?php
        }
    }
    ?>
    <div class="columns medium-12 m-bottom-1" id="cnt-mandatory">
        <?php
        echo $this->Form->input(
            'Task.mandatory',
            array(
                'label' => __t('Task.Mandatory'),
                'type' => 'checkbox',
                'id' => 'mandatory'
            )
        );
        ?>
    </div>
    <div class="columns medium-12 m-bottom-1">
        <label class="fs-small validation_field">
            <?php echo __t('Task.Body'); ?>
        </label>
        <?php
        echo $this->Form->input(
            'Task.body',
            array(
                'label' => false,
                'type' => 'textarea',
                'id' => 'body'
            )
        );
        ?>
    </div>
    <div class="columns medium-12 p-0">
        <div class="columns medium-12">
            <div class="aag-subtitle" style="padding: 1rem 0 .5rem;">
                <?php echo __t('General.Files') . ' '; ?>
            </div>
        </div>
        <div class="columns medium-12 ta-center">
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
            <?php
            echo $this->Form->input(
                'Task.files.',
                array(
                    'id' => 'files',
                    'class' => 'dragdrop-js dragdrop-multiple-js',
                    'label' => false,
                    'type' => 'file',
                    'multiple' => true,
                    'accept' => '.' . ConstantsFileType::PDF,
                    'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                    'div' => array(
                        'class' => 'field_file cont-fileWrapper fileWrapperMultiple',
                    ),
                )
            );
            ?>
        </div>
        <div class="medium-12 columns m-bottom-1">
            <div id="file-list-js">
                <?php
                if (isset($task_files) && !empty($task_files)) {
                    echo $this->element('../Tasks/Elements/form_attached_files', array(
                        'data_form' => 'no-task'
                    ));
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<script>
    <?php
    if (isset($debrief_tasks_list)) {
    ?> tasks_list_array_js = <?php echo json_encode($debrief_tasks_list); ?>;
    <?php
    }
    if (isset($debrief_topics_list)) {
    ?> topics_list_array_js = <?php echo json_encode($debrief_topics_list); ?>;
    <?php
    }
    if (isset($debrief_tasks)) {
    ?> tasks_array_js = <?php echo json_encode($debrief_tasks); ?>;
    <?php
    }
    if (isset($debrief_topics)) {
    ?> topics_array_js = <?php echo json_encode($debrief_topics); ?>;
    <?php
    }
    ?>
</script>