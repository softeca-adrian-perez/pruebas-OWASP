<div class="o-auto">
    <table class="table-tracking">
        <thead>
            <tr>
                <th width="300"><?php echo __t('Task.Task'); ?></th>
                <th width="200"><?php echo __t('Task.Created_by'); ?></th>
                <th width="200"><?php echo __t('Task.Assigned_to'); ?></th>
                <th width="150" class="ta-center"><?php echo $this->Paginator->sort('Task.creation_date', __t('Task.Creation_date')); ?></th>
                <th width="150" class="ta-center"><?php echo $this->Paginator->sort('Task.limit_date', __t('Task.Deadline'));?></th>
                <th width="300" class="ta-center"><?php echo __t('Task.Appointment');?></th>
                <th width="150" class="ta-center"><?php echo __t('Task.Status');?></th>
                <th width="150" class="ta-center"><?php echo __t('Task.Complete');?></th>
                <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN){ ?>
                    <th class="ta-center" width="100"><?php echo __t('General.Actions');?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task) {
            echo $this->Form->hidden(
                '',
                array(
                    'id' => 'garages-task-' . $task['Task']['id'],
                    'value' => !empty($task['TaskGarage']['total']),
                    'data-url' => Router::url(
                        array(
                            'controller' => 'tasks',
                            'action' => 'subtasks_home',
                            '?' => array(
                                'id' => $task['Task']['id']
                            )
                        )
                    )
                )
            );
            echo $this->Form->hidden(
                '',
                array(
                    'id' => 'distributors-task-' . $task['Task']['id'],
                    'value' => !empty($task['TaskDistributor']['total']),
                    'data-url' => Router::url(
                        array(
                            'controller' => 'tasks',
                            'action' => 'subtasks_home',
                            '?' => array(
                                'id' => $task['Task']['id']
                            )
                        )
                    )
                )
            );?>
            <tr>
                <td class="c-primary">
                    <?php
                    $task_name = '';
                    if(strlen($task['Task']['title']) > 0){
                        if(strlen($task['Task']['title']) < 30){
                            $task_name = $task['Task']['title'];
                        } else {
                            $task_name = substr($task['Task']['title'],0,30) . '...';
                        }
                    } else {
                        if(strlen($task['Task']['body']) < 30){
                            $task_name = $task['Task']['body'];
                        } else {
                            $task_name = substr($task['Task']['body'],0,30) . '...';
                        }
                    } ?>
                    <?php echo $this->Html->link(
                        $task_name,
                        array(
                            'controller' => 'tasks',
                            'action' => 'edit',
                            $task['Task']['id'],
                        ),
                        array(
                            'escape' => false,
                            'title' => __t('General.Edit'),
                        )
                    ); ?>
                </td>
                <td class="c-defecto">
                    <?php echo h($users[$task['Task']['user_creation_id']]); ?>
                </td>
                <td class="c-defecto">
                    <?php if(isset($users[$task['Task']['user_assigned_id']])){
                        echo $users[$task['Task']['user_assigned_id']];
                    } else if(!empty($task['TaskContactList'])) {
                        echo $this->Html->link(
                            __t('Task.Multiple_recipients'),
                            array(
                                'controller' => 'tasks',
                                'action' => 'subtasks_home',
                                '?' => array(
                                    'id' => $task['Task']['id']
                                )
                            ),
                            array(
                                'class' => 'c-primary',
                            )
                        );
                    } else if(!empty($task['TaskGarage']) && $task['TaskGarage']['total'] == 1){
                        echo h($task['Garage']['Garage']['name']);
                    } else if(!empty($task['TaskGarage']) && $task['TaskGarage']['total'] > 1){
                        echo $this->Html->link(
                            __t('Task.Multiple_garages'),
                            array(
                                'controller' => 'tasks',
                                'action' => 'subtasks_home',
                                '?' => array(
                                    'id' => $task['Task']['id']
                                )
                            ),
                            array(
                                'class' => 'c-primary',
                            )
                        );
                    } else if(!empty($task['TaskDistributor']) && $task['TaskDistributor']['total'] == 1){
                        echo h($task['Distributor']['Distributor']['name']);
                    } else if(!empty($task['TaskDistributor']) && $task['TaskDistributor']['total'] > 1){
                        echo $this->Html->link(
                            __t('Task.Multiple_distributors'),
                            array(
                                'controller' => 'tasks',
                                'action' => 'subtasks_home',
                                '?' => array(
                                    'id' => $task['Task']['id']
                                )
                            ),
                            array(
                                'class' => 'c-primary',
                            )
                        );
                    } else {
                        echo '';
                    } ?>
                </td>
                <td class="ta-center">
                    <?php echo ($task['Task']['creation_date'] != '0000-00-00') ? Fecha::toFormatoVistaFecha($task['Task']['creation_date']) : ''; ?>
                </td>
                <?php if($task['Task']['limit_date'] < date('Y-m-d') && $task['Task']['task_status_id'] == ConstantsStatusTasks::PENDING){ ?>
                    <td class="c-fallo ta-center">
                        <?php if ($task['Task']['limit_date'] != '0000-00-00') {
                            echo Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                        } ?>
                    </td>
                <?php }else{ ?>
                    <td class="c-defecto ta-center">
                        <?php if ($task['Task']['limit_date'] != '0000-00-00') {
                            echo Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                        } ?>
                    </td>
                <?php } ?>
                <td class="ta-center">
                    <?php if ($task['Task']['appointment_id'] != null) { ?>
                        <?php if ($task['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) { ?>
                            <?php echo $this->Html->link(
                                '<img src="/img/iconos/events.png" style="max-height: 20px">',
                                array(
                                    'controller' => 'appointments',
                                    'action' => 'edit_event',
                                    $task['Task']['appointment_id']
                                ),
                                array(
                                    'escape' => false,
                                )
                            ); ?>
                        <?php }else{ ?>
                            <?php echo $this->Html->link(
                                '<span class="icon-Visit_create"></span>',
                                array(
                                    'controller' => 'appointments',
                                    'action' => 'edit',
                                    $task['Task']['appointment_id']
                                ),
                                array(
                                    'escape' => false,
                                )
                            ); ?>
                        <?php } ?>
                    <?php } ?>

                </td>
                <td class="ta-center status_task">
                    <?php echo h($status[$task['Task']['task_status_id']]); ?>
                </td>
                <td class="ta-center">
                    <?php if(!empty($task['TaskGarage']['total'])) {
                        echo h($task['TaskGarage']['completed']) . ' / ' . h($task['TaskGarage']['total']);
                    } else if(!empty($task['TaskDistributor']['total'])) {
                        echo h($task['TaskDistributor']['completed']) . ' / ' . h($task['TaskDistributor']['total']);
                    } ?>
                </td>
                <td class="ta-center">
                    <?php
                    //Resolve icons
                    if(empty($task['TaskGarage']) || $task['TaskGarage']['total'] <= 1)
                    {
                        if($task['Task']['task_status_id'] == ConstantsStatusTasks::PENDING)
                        {
                            echo $this->Html->link(
                                '<span class="ion-ios-checkmark-outline c-informacion" style="margin-right: .5rem !important;"></span>',
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'ajax_check_task',
                                    $task['Task']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'class' => 'check-task-js',
                                    'data-id' => $task['Task']['id'],
                                    'data-confirmmsg' => __t('Task.Confirm_checked'),
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No'),
                                    'title' => __t('Task.Mark_as_complete'),
                                    'data-title-complete' => __t('Task.Mark_as_complete'),
                                    'data-title-pending' => __t('Task.Mark_as_pending')
                                )
                            );
                        }
                        elseif($task['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED)
                        {
                            echo '<span class="ion-ios-checkmark-outline c-exito" style="margin-right !important: .5rem;"></span>';
                        }
                    }
                    else
                    {
                        if($task['Task']['task_status_id'] == ConstantsStatusTasks::PENDING)
                        {
                            echo '<span class="ion-ios-checkmark-outline c-informacion" style="margin-right: .5rem !important;"></span>';
                        }
                        elseif($task['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED)
                        {
                            echo '<span class="ion-ios-checkmark-outline c-exito" style="margin-right: .5rem !important;"></span>';
                        }
                    }
                    if($task['Task']['task_status_id'] != ConstantsStatusTasks::COMPLETED && (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN))
                    {
                        echo $this->Html->link(
                            '<span class="aag-icon-papelera c-fallo"></span>',
                            array(
                                'controller' => 'tasks',
                                'action' => 'ajax_delete_task',
                                $task['Task']['id'],
                            ),
                            array(
                                'escape' => false,
                                'class' => 'delete-task-js',
                                'data-id' => $task['Task']['id'],
                                'data-confirmmsg' => __t('Task.Confirm_delete'),
                                'data-yes' => __t('General.Yes'),
                                'data-no' => __t('General.No'),
                                'data-url_redirect' => Router::url(array(
                                    'controller' => 'tasks',
                                    'action' => 'home',
                                )),
                            )
                        );
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php echo $this->element('Comun/paginacion'); ?>