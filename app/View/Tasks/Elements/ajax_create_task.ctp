<?php
$action = $this->request->action;

echo $this->Form->create('Task',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data',
        'id' => 'task-form'
    )
);

?>
    <div class="row">
        <div class="columns medium-12 cnt-form-animate">
            <fieldset class="columns fieldset-garage-list" style="padding: 0 0 .5rem 0;">
                <div class="columns medium-12 p-horizontal-0 p-top-1">
                    <div class="columns medium-6">
                        <?php
                        echo $this->Form->input(
                            'user_assigned_id',
                            array(
                                'label' => __t('Appointment.Assign_to'),
                                'class' => 'select2-multiple',
                                'id' => 'assigned-to',
                                'type' => 'select',
                                'multiple' => false,
                                'default' => CakeSession::read('Auth.User.id'),
                                'empty' => false,
                                'options' => $user,
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
                                'options' => isset($task_status_list) ? $task_status_list : array(),
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

                    <div class="columns medium-6 clear">
                        <div class="columns medium-12 required">
                        <?php echo $this->Form->input(
                                'title',
                                array(
                                    'label' => __t('Task.Title'),
                                    'type' => 'text',
                                    'id' => 'title',
                                )
                            ); ?>
                        </div>
                        <div class="columns medium-12">
                            <?php echo $this->Form->input(
                                'body',
                                array(
                                    'label' => __t('Task.Body'),
                                    'type' => 'textarea',
                                    'id' => 'body'
                                )
                            ); ?>
                        </div>
                    </div>
                    <div class="columns medium-6 m-top-1" id="cnt_files">
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
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row" id="cnt-save-task">
        <div class="medium-12 columns m-bottom-1 ta-right">
            <div class="cnt-buttons-v2">
                <?php echo $this->Form->button(
                    __t('Task.Save_task'),
                    array(
                        'class' => 'btn-guardar save_task',
                        'id' => 'save_submit_create_task',
                        'type' => 'button',
                        'data-url' => Router::url(
                            array(
                                'controller' => 'tasks',
                                'action' => 'ajax_submit_create_task'
                            )
                        )
                    )
                ); ?>
            </div>
        </div>
    </div>
<?php echo $this->Form->end(); ?>