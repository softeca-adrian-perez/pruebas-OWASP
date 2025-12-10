<?php
echo $this->Html->script('debrief-tasks.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
echo $this->Form->create(
    'DebriefTask',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('DebriefTask.id');
echo $this->Form->hidden(
    'DebriefTask.DebriefAction',
    array(
        'id' => 'DebriefAction',
        'value' => $this->request->action
    )
);
echo $this->Form->hidden(
    'DebriefTask.send_to',
    array(
        'id' => 'send_to',
        'value' => ConstantsTasks::USER
    )
);
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == 'add_debrief_task') {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Maintenance.Maintenance'),
                    array(
                        'controller' => 'maintenance',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Task.Debrief') . ' ' .  __t('Task.Task'),
                    array(
                        'controller' => 'tasks',
                        'action' => 'maintenance_tasks'
                    )
                ),
                __t('General.Add')
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Maintenance.Maintenance'),
                    array(
                        'controller' => 'maintenance',
                        'action' => 'home'
                    )
                ),
                $this->Html->link(
                    __t('Task.Debrief') . ' ' .  __t('Task.Task'),
                    array(
                        'controller' => 'tasks',
                        'action' => 'maintenance_tasks'
                    )
                ),
                __t('Task.Edit_debrief_task'),
            ));
        }
        ?>
    </div>
    <div>
        <?php
        echo $this->element('Comun/form_actions', $cancel_action);
        if ($this->request->action == 'edit_debrief_task') {
            echo $this->Html->link(
                __t('General.Delete'),
                array(
                    'controller' => 'tasks',
                    'action' => 'delete_debrief_task',
                    $debrief_task_id
                ),
                array(
                    'class' => 'delete-task-js aag-button medium red',
                    'data-confirmmsg' => __t('DebriefTask.Confirm_delete'),
                    'data-yes' => __t('General.Yes'),
                    'data-no' => __t('General.No'),
                )
            );
        }
        ?>
    </div>
</div>
<div class="cnt-data p-vertical-1">
    <div class="cnt-data-element">
        <div class="aag-title">
            <?php echo __t('Task.Debrief') . ' ' . __t('Task.Task'); ?>
        </div>
        <div class="cnt-two-columns cnt-form-animate fieldset-garage-list">
            <div>
                <div class="aag-subtitle">
                    <?php echo __t('Task.Assigned_to'); ?>
                </div>
                <ul class="aag-subtabs" data-action="<?php echo $this->request->action ?>">
                    <li>
                        <input type="radio" class="tasks_types d-none" name="task_type" id="task_type_user" checked data-mindate="<?php echo date('d-m-Y'); ?>">
                        <label for="task_type_user"><?php echo __t('User.Users'); ?></label>
                    </li>
                    <li>
                        <input type="radio" class="tasks_types d-none" name="task_type" id="task_type_garage">
                        <label for="task_type_garage"><?php echo __t('Garage.Garages'); ?></label>
                    </li>
                    <li>
                        <input type="radio" class="tasks_types d-none" name="task_type" id="task_type_distributor">
                        <label for="task_type_distributor"><?php echo __t('Distributor.Distributors'); ?></label>
                    </li>
                </ul>
                <div class="d-none" id="assigned_to_distributor">
                    <div class="aag-subtitle m-top-1">
                        <?php echo __t('Garage.Filters'); ?>
                    </div>
                    <div class="cnt-two-columns">
                        <div>
                            <?php echo $this->Form->input(
                                'DistributorFilter.RSM',
                                array(
                                    'label' => __t('Garage.RSM'),
                                    'class' => 'select2-multiple',
                                    'type' => 'select',
                                    'multiple' => false,
                                    'empty' => true,
                                    'options' => $garage_rsm,
                                    'id' => 'distributor_filter_rsm',
                                )
                            ); ?>
                        </div>
                        <div>
                            <?php echo $this->Form->input(
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
                            ); ?>
                        </div>
                    </div>
                    <div class="columns medium-12 p-0 m-top-1">
                        <?php
                        $value = array();
                        if (!empty($task_distributors)) {
                            foreach ($task_distributors as $key => $distributor) {
                                $value[] = $key;
                            }
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
                        );
                        ?>
                    </div>
                </div>
                <div class="columns medium-12 m-bottom-1 d-none p-horizontal-0" id="assigned_to_garage">
                    <div class="aag-subtitle m-top-1">
                        <?php echo __t('Garage.Filters'); ?>
                    </div>
                    <div class="cnt-two-columns">
                        <div>
                            <?php echo $this->Form->input(
                                'GarageFilter.branch',
                                array(
                                    'label' => __t('Distributor.Distributors'),
                                    'class' => 'select2-multiple',
                                    'type' => 'select',
                                    'multiple' => false,
                                    'empty' => true,
                                    'options' => $garage_branch,
                                    'id' => 'garage_filter_branch',
                                )
                            ); ?>
                        </div>
                        <div>
                            <?php echo $this->Form->input(
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
                            ); ?>
                        </div>
                        <div>
                            <?php echo $this->Form->input(
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
                            ); ?>
                        </div>
                        <div>
                            <?php echo $this->Form->input(
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
                            ); ?>
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
                        ); ?>
                    </div>
                </div>
                <div id="assigned_to_contact">
                    <div class="p-vertical-1 cnt-inputs">
                        <?php
                        echo $this->Form->input(
                            'DebriefTask.specific_user',
                            array(
                                'type' => 'radio',
                                'label' => false,
                                'options' => array(
                                    '0' => __t('Task.Nobody'),
                                    '1' => __t('Task.Assign_specific_entity'),
                                    '2' => __t('Task.Assign_logged'),
                                ),
                                'legend' => false,
                                'id' => 'radioDebriefType',
                                'class' => 'radio_debrief_type',
                                'div' => false,
                                'hiddenField' => false,
                                'separator' => '</label><label>',
                                'before' => '<label>',
                                'after' => '</label>',
                            )
                        );
                        ?>
                    </div>
                    <?php
                    $config = CakeSession::read('Auth.User.Config');
                    if ($config[ConstantsConfig::ASSIGN_TO_GROUP]) {
                    ?>
                        <div class="columns medium-6 m-bottom-1" id="contact_list_cnt">
                            <?php echo $this->Form->input(
                                'DebriefTask.contact_list_id',
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
                    <?php } ?>
                    <div class="columns medium-6 m-bottom-1 end" id="assigned_to">
                        <?php echo $this->element('../Tasks/Elements/form_assigned_task', array('user_assigned_id' => null)); ?>
                    </div>
                </div>
            </div>
            <div>
                <div class="columns medium-12 p-0">
                    <?php echo $this->Form->input(
                        'DebriefTask.title_en',
                        array(
                            'required' => true,
                            'type' => 'text',
                            'label' => __t('Task.Title'),
                        )
                    ); ?>
                </div>
                <div class="fields_titles_forms mandatory fw-bold c-defecto"><?php echo __t('Task.Description'); ?></div>
                <div class="columns medium-12 p-0 p-left-0">
                    <?php echo $this->Form->input(
                        'DebriefTask.description_en',
                        array(
                            'label' => false,
                            'required' => true,
                            'type' => 'textarea',
                        )
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>