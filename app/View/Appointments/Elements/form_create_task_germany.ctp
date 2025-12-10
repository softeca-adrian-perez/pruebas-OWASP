<?php
echo $this->Html->script('tasks_germany.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

echo $this->Form->create('Task',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'task-form'
    )
);
    echo $this->Form->hidden('reason');
    echo $this->Form->hidden('User.user_id', array('value' => null));
    echo $this->Form->hidden(
        'user_assigned_id',
        array(
            'id' => 'user_assigned_id',
            'value' => isset($this->request->data['Task']['user_assigned_id']) ? $this->request->data['Task']['user_assigned_id'] : ''
        )
    );
    echo $this->Form->hidden('Task.id');
    echo $this->Form->hidden(
        'TaskId',
        array(
            'id' => 'task_id',
            'value' => isset($this->request->data['Task']['id']) ? $this->request->data['Task']['id'] : ''
        )
    );
    echo $this->Form->hidden(
        'Task.send_to',
        array(
            'id' => 'send_to',
            'value' => ConstantsTasks::USER
        )
    );
    ?>
    <div class="row">
        <div class="cnt-form-animate">
            <fieldset class="columns fieldset-garage-list p-1">
                <div class="p-0">
                    <div class="text-button">
                        <div class="aag-title m-bottom-1">
                            <?php echo __t('Task.Assigned_to'); ?>
                        </div>
                        <?php
                        if(isset($task['Task']['id'])) {
                            ?>
                            <div class="ta-right">
                                <?php echo $this->Html->link(
                                    __t('Task.Delete_task'),
                                    'javascript:void(0);',
                                    array(
                                        'escape' => false,
                                        'id' => 'delete_task',
                                        'class' => 'aag-button outlined red',
                                        'data-url' => Router::url(array(
                                                'controller' => 'tasks',
                                                'action' => 'ajax_delete_task'
                                            )
                                        )
                                    )
                                ); ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="d-inline-block m-bottom-1" data-action="<?php echo $this->request->action?>">
                        <label class="aag-button medium three outlined">
                            <input type="radio" class="tasks_types d-none" name="task_type" id="task_type_user" checked data-mindate="<?php echo date('d-m-Y'); ?>">
                            <?php echo __t('User.Users'); ?>
                        </label>
                        <label class="aag-button medium three outlined">
                            <input type="radio" class="tasks_types d-none" name="task_type" id="task_type_garage">
                            <?php echo __t('Garage.Garages'); ?>
                        </label>
                        <label class="aag-button medium three outlined">
                            <input type="radio" class="tasks_types d-none" name="task_type" id="task_type_distributor">
                            <?php echo __t('Distributor.Distributors'); ?>
                        </label>
                    </div>
                    <div class="m-bottom-1 d-none cnt-assigned_to_garage" id="assigned_to_garage">
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
                                        'class' => 'clear_field select2Dinamico_distributor cargar_distributors',
                                        'type' => 'select',
                                        'multiple' => false,
                                        'empty' => true,
                                        'id' => 'garage_filter_branch',
                                    )
                                );
                                if( $this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID || $this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID){
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
                                        // 'default' => $default_value,
                                        // 'disabled' => $disabled_value,
                                        'options' => $garage_rsm,
                                        'id' => 'garage_filter_rsm',
                                    )
                                );
                                if( $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG || $this->Session->read('Auth.User.roles_id') == ConstantsRoles::BDM_TG){
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
                                        // 'default' => $default_value,
                                        // 'disabled' => $disabled_value,
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
                        <div class="p-0 m-top-1">
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
                                        'options' => isset($task_garages) ? $task_garages : array(),
                                        'value' => $value,
                                        'id' => 'garage_name_assigned_to',
                                        'data-url' => Router::url(array(
                                            'controller' => 'garages',
                                            'action' => 'ajax_get_garages',
                                            $user_aag_region_id
                                        ))
                                    )
                                ); ?>
                        </div>
                    </div>
                    <div class="m-bottom-1 p-0 p-top-1" id="assigned_to_contact">
                        <div class="columns medium-6 m-bottom-1">
                            <div id="assigned_to">
                                <?php echo $this->element('../Tasks/Elements/form_assigned_task', array('user_assigned_id' => isset($array_user_name) ? $array_user_name : array())); ?>
                            </div>
                        </div>
                    </div>
                    <div class="m-bottom-1 d-none cnt-assigned_to_distributor" id="assigned_to_distributor">
                        <div class="cnt-form-search m-0-i">
                            <div class="cnt-form-search-title">
                                <?php echo __t('Garage.Filters'); ?>
                            </div>
                            <div class="cnt-form-inputs">
                                <?php
                                if( $this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID || $this->Session->read('Auth.User.Contact.position_id') == ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID){
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
                                        // 'default' => $default_value,
                                        // 'disabled' => $disabled_value,
                                        'options' => $garage_rsm,
                                        'id' => 'distributor_filter_rsm',
                                    )
                                );
                                if( $this->Session->read('Auth.User.role_id') == ConstantsRoles::BDM_AAG || $this->Session->read('Auth.User.roles_id') == ConstantsRoles::BDM_TG){
                                    $default_value = $this->Session->read('Auth.User.contact_id');
                                    $disabled_value = true;
                                } else {
                                    $default_value = null;
                                    $disabled_value = false;
                                }
                                echo $this->Form->input(
                                    'DistributorFilter.BDM',
                                    array(
                                        'label' => __t('Garage.BDM'),
                                        'class' => 'select2-multiple',
                                        'type' => 'select',
                                        'multiple' => false,
                                        'empty' => true,
                                        // 'default' => $default_value,
                                        // 'disabled' => $disabled_value,
                                        'options' => isset($distributor_bdm) ? $distributor_bdm : array(),
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
                        <div class="p-0 m-top-1">
                            <?php
                            $value = array();
                            if (!empty($task_distributors)) {
                                foreach ($task_distributors as $key => $distributor) {
                                    $value[] = $key;
                                }
                            } else if(!isset($task_distributors)){
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
                                    ))
                                )
                            ); ?>
                        </div>
                    </div>
                    <div class="cnt-form-inputs">
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
                        if(isset($this->request->data['Task']) && $this->request->data['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED){
                            echo $this->Form->input(
                            'Task.limit_date',
                                array(
                                    'type' => 'text',
                                    'required' => true,
                                    'class' => 'fecha-js from-today',
                                    'id' => 'due-date',
                                    'disabled' => true,
                                    'div' => array(
                                        'class' => 'datepicker datepicker-label-block',
                                    ),
                                    'label' => __t('Task.Deadline'),
                                )
                            );
                            echo $this->Form->hidden('Task.limit_date');
                        }
                        else {
                            echo $this->Form->input(
                            'Task.limit_date',
                                array(
                                    'type' => 'text',
                                    'required' => true,
                                    'class' => 'fecha-js from-today',
                                    'id' => 'due-date',
                                    'div' => array(
                                        'class' => 'datepicker datepicker-label-block',
                                    ),
                                    'label' => __t('Task.Deadline'),
                                )
                            );
                        }
                        ?>
                        <div class="m-bottom-1" id="task-status-no-expired">
                            <?php echo $this->Form->input(
                                'TaskStatusId',
                                array(
                                    'label' => __t('General.Status'),
                                    'type' => 'select',
                                    'class' => 'select2-multiple select',
                                    'options' => isset($task_status_list) ? $task_status_list : array(),
                                    'id' => 'select-status-js',
                                    'default' => isset($this->request->data['Task']) ? $this->request->data['Task']['task_status_id'] : '',
                                    'disabled' => false,
                                )
                            ); ?>
                        </div>
                        <div class="m-bottom-1" style="display: none;" id="task-status-expired">
                            <?php
                            echo $this->Form->input(
                                'TaskStatusId',
                                array(
                                    'label' => __t('General.Status'),
                                    'type' => 'select',
                                    'class' => 'select2-multiple',
                                    'options' =>isset($task_status_list) ? $task_status_list : array(),
                                    'default' => ConstantsStatusTasks::EXPIRED,
                                    'disabled' => true,
                                )
                            );
                            ?>
                        </div>
                    </div>
                    <div class="m-vertical-1" id="cnt-mandatory">
                        <?php echo $this->Form->input(
                            'Task.mandatory',
                            array(
                                'label' => __t('Task.Mandatory'),
                                'type' => 'checkbox',
                                'id' => 'mandatory'
                            )
                        ); ?>
                    </div>
                    <?php echo $this->Form->hidden(
                        'task_status_id',
                        array(
                            'id' => 'task-status-hidden',
                            'value' => ConstantsStatusTasks::PENDING,
                            'data-status-expired' => ConstantsStatusTasks::EXPIRED
                        )
                    ) ?>

                    <div class="m-bottom-1">
                        <?php echo $this->Form->input(
                            'Task.body',
                            array(
                                'label' => __t('Task.Body'),
                                'type' => 'textarea',
                                'id' => 'body'
                            )
                        ); ?>
                    </div>
                    <div class="p-0">
                        <div class="columns medium-12">
                            <div class="title-in-fieldset">
                                <?php echo __t('General.Files') . ' '; ?>
                            </div>
                        </div>
                        <div class="ta-center">
                            <?php echo $this->Form->input(
                                'Task.files.',
                                array(
                                    'id' => 'files',
                                    'class' => 'dragdrop-js dragdrop-multiple-js task-files',
                                    'label' => false,
                                    'type' => 'file',
                                    'multiple' => true,
                                    'accept' => '.' . ConstantsFileType::PDF,
                                    'before' => '<div class="text-drop">' . __t('General.Drop_files') . '</div>',
                                    'div' => array(
                                        'class' => 'field_file cont-fileWrapper fileWrapperMultiple',
                                    ),
                                )
                            ); ?>
                        </div>
                        <div class="medium-12 columns m-bottom-1">
                            <div id="file-list-js">
                                <?php if (isset($task_files) && !empty($task_files)) {
                                    echo $this->element('../Tasks/Elements/form_attached_files');
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row" id="cnt-save-task">
        <div class="medium-12 columns m-bottom-1 ta-center">
            <div class="cnt-buttons-v2">
                <?php echo $this->Form->button(
                    __t('Task.Save_task'),
                    array(
                        'class' => 'aag-button large green save_task',
                        'id' => 'save_task_form',
                        'type' => 'button',
                        'data-url' => Router::url(
                            array(
                                'controller' => 'tasks',
                                'action' => 'ajax_submit_form'
                            )
                        )
                    )
                ); ?>
            </div>
        </div>
    </div>
<?php echo $this->Form->end(); ?>
<style> #session-msg { padding: 0 !important; } </style>