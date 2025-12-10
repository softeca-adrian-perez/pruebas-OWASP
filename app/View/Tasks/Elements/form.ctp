<?php
echo $this->Html->script('tasks.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

$action = $this->request->action;
if ($action == ConstantsActionsNames::ADD) {
    $task_users = null;
    $task_contact_lists = null;
}

echo $this->Form->create(
    'Task',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'task-form'
    )
);

echo $this->Form->hidden(
    'Task.check_send',
    array(
        'id' => 'check_send'
    )
);
echo $this->Form->hidden(
    'FormController',
    array(
        'id' => 'form-controller',
        'value' => $this->request->controller
    )
);
?>
<div class="row">
    <div class="columns medium-12 title-big">
        <?php echo __t('Task.Tasks'); ?>
    </div>
</div>
<div class="row">
    <div class="medium-12 columns m-top-1 ta-right cnt-buttons-v2">
        <div class="cnt-buttons-v2">
            <?php
            echo $this->element(
                'Comun/form_actions',
                $cancel_action
            );
            ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="columns medium-12 cnt-form-animate">
        <fieldset class="columns fieldset-garage-list" style="padding: 0 0 .5rem 0;">

            <?php
            $config = CakeSession::read('Auth.User.Config');
            if ($config[ConstantsConfig::CHECK_SEND_NOTIFICATION]) { ?>
                <label id="generate_alerts" class="label-w-auto p-1 f-right">
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
            <?php } ?>

            <div class="columns medium-7 p-horizontal-0 p-top-1">
                <?php echo $this->Form->hidden(
                    'appointment_id',
                    array(
                        'label' => __t('Task.Reason'),
                        'type' => 'textarea',
                        'id' => 'appointment_id'
                    )
                );
                if (isset($task[0]['full_name'])) { ?>
                    <div class="d-inline-block w-100p clear">
                        <div class="columns medium-6">
                            <?php echo $this->Form->input(
                                'user_creation_id',
                                array(
                                    'type' => 'text',
                                    'required' => true,
                                    'value' => $task[0]['full_name'],
                                    'readonly' => true,
                                    'label' => __t('Task.Task_creator'),
                                )
                            ); ?>
                        </div>
                        <div class="columns medium-6">
                            <?php echo $this->Form->input(
                                'creation_date',
                                array(
                                    'type' => 'text',
                                    'required' => true,
                                    'id' => 'creation-date',
                                    'readonly' => true,
                                    'label' => __t('Task.Creation_date'),
                                )
                            ); ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="columns medium-6">
                    <?php echo $this->Form->input(
                        'user_assigned_id',
                        array(
                            'label' => __t('Appointment.Assign_to'),
                            'class' => 'select2-multiple',
                            'id' => 'assigned-to',
                            'type' => 'select',
                            'multiple' => false,
                            'default' => CakeSession::read('Auth.User.id'),
                            'empty' => false,
                            'options' => $users,
                        )
                    ); ?>
                </div>
                <div class="columns medium-3">
                    <?php echo $this->Form->input(
                        'task_status_id',
                        array(
                            'label' => __t('Task.Status'),
                            'type' => 'select',
                            'class' => 'select2-multiple select',
                            'options' => $task_status_list,
                            'id' => 'select-status-js',
                            'default' => ConstantsStatusTasks::PENDING
                        )
                    ); ?>
                </div>
                <div class="columns medium-3">
                    <?php echo $this->Form->input(
                        'limit_date',
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
                    ); ?>
                </div>
                <div class="columns medium-12 clear">
                    <?php echo $this->Form->input(
                        'body',
                        array(
                            'label' => __t('Task.Body'),
                            'type' => 'textarea',
                            'id' => 'body'
                        )
                    ); ?>
                </div>
                <div id="reason-js" class="columns medium-12 clear">
                    <?php
                    echo $this->Form->input(
                        'reason',
                        array(
                            'label' => __t('Task.Reason'),
                            'type' => 'text',
                            'id' => 'reason'
                        )
                    );
                    ?>
                </div>
                <div class="columns medium-12">
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ConstantsFiles::MAX_FILE_SIZE ?>" />
                    <?php
                    echo $this->Form->input(
                        'Task.files.',
                        array(
                            'id' => 'files',
                            'class' => 'dragdrop-js dragdrop-multiple-js',
                            'label' => false,
                            'capture' => 'camera',
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
                <div class="row">
                    <div class="medium-12 columns p-normal">
                        <div id="file-list-js">
                            <?php if (isset($task_files) && !empty($task_files)) {
                                echo $this->element('../Tasks/Elements/form_attached_files');
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="columns medium-5 p-horizontal-0">
                <div class="columns medium-12">
                    <div class="title-in-fieldset">
                        <?php echo __t('Task.Notify') . ' '; ?>
                        <span class="highlight ion-person-stalker" style="color: #9EA4B2;"></span>
                    </div>
                </div>
                <div class="columns medium-12 m-top-1">
                    <?php echo __t('Task.Users'); ?>
                </div>
                <div class="columns medium-12 m-bottom-1">
                    <?php
                    echo $this->Form->input(
                        'User.user_id',
                        array(
                            'label' => false,
                            'type' => 'select',
                            'class' => 'select2-multiple',
                            'options' => $users,
                            'multiple' => true,
                            'empty' => true,
                            'default' => $task_users,
                            'id' => 'users',
                            'required' => true
                        )
                    );
                    ?>
                </div>
                <div class="columns medium-12 m-0">
                    <?php echo __t('Task.Contact_lists'); ?>
                </div>
                <div class="columns medium-12 ">
                    <?php echo $this->Form->input(
                        'ContactList.contact_list_id',
                        array(
                            'label' => false,
                            'type' => 'select',
                            'class' => 'select2-multiple',
                            'options' => $contact_lists,
                            'multiple' => true,
                            'empty' => true,
                            'default' => $task_contact_lists,
                            'id' => 'contact_list'
                        )
                    ); ?>
                </div>
                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) { ?>
                    <?php if ($this->request->action == ConstantsActionsNames::EDIT) { ?>
                        <div class="columns medium-12 ta-center m-top-1">
                            <?php
                            echo $this->Html->link(
                                "<span class='icon-delete'></span>" . __t('Task.Delete_task'),
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'delete_task',
                                    $task['Task']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'title' => __t('Task.Delete_task'),
                                    'id' => 'task_delete',
                                    'class' => 'button-general conImg cuatro',
                                )
                            );

                            ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </fieldset>
    </div>
</div>
<div class="row">
    <div class="medium-12 columns ta-right cnt-buttons-v2">
        <div class="cnt-buttons-v2">
            <?php
            echo $this->element(
                'Comun/form_actions',
                $cancel_action
            );
            ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>