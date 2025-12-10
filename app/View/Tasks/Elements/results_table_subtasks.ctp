<?php $action = $this->request->action; ?>
<input class="d-none" id="subtask_id_query" value="<?php echo $this->request->query['id'];?>"></input>
<div class="o-auto">
    <table class="table-tracking">
        <thead>
        <tr>
            <th><?php echo __t('Task.Task'); ?></th>
            <th><?php echo $this->Paginator->sort('Task.user_creation_id', __t('Task.Created_by')); ?></th>
            <th><?php echo $this->Paginator->sort('Task.user_assigned_id', __t('Task.Assigned_to')); ?></th>
            <th class="ta-center"><?php echo $this->Paginator->sort('Task.creation_date', __t('Task.Creation_date')); ?></th>
            <th class="ta-center" width="100"><?php echo $this->Paginator->sort('Task.limit_date', __t('Task.Deadline')); ?></th>
            <th class="ta-center"><?php echo __t('Task.Appointment'); ?></th>
            <th class="ta-center" width="100"><?php echo __t('General.Actions'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tasks as $task) { ?>
            <tr>
                <td class="c-primary">
                    <?php echo $this->Html->link(
                        !empty($task['Task']['title']) ? h($task['Task']['title']) : h($task['Task']['body']),
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
                <td>
                    <?php echo h($users[$task['Task']['user_creation_id']]); ?>
                </td>
                <td class="c-primary">
                    <?php
                    if($task[0]['full_name'] != null){
                        echo $this->Html->link(
                            $task[0]['full_name'],
                            array(
                                'controller' => 'clients',
                                'action' => 'report',
                                $task['Garage']['id']
                            )
                        );
                    }if($task['Distributor']['name'] != null){
                        echo $this->Html->link(
                            $task['Distributor']['name'],
                            array(
                                'controller' => 'clients',
                                'action' => 'report_distributor',
                                $task['Distributor']['id']
                            )
                        );
                    } else {
                        echo $this->Html->link(
                            $task['Garage']['name'],
                            array(
                                'controller' => 'clients',
                                'action' => 'report',
                                $task['Garage']['id']
                            )
                        );
                    } ?>
                </td>
                <td class="ta-center">
                    <?php echo ($task['Task']['creation_date'] != '0000-00-00') ? Fecha::toFormatoVistaFecha($task['Task']['creation_date']) : ''; ?>
                </td>
                <td class="ta-center">
                    <?php echo ($task['Task']['limit_date'] != '0000-00-00') ? Fecha::toFormatoVistaFecha($task['Task']['limit_date']) : ''; ?>

                </td>
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
                <td class="ta-center">
                    <?php
                    if($task['Garage']['id'] != null){
                        echo $this->Form->hidden(
                            'GarageDataCheck',
                            array(
                                'class' => 'garage-data-check',
                                'data-garage-id' => $task['Garage']['id'],
                                'data-url' => Router::url(
                                    array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_check_task_garage',
                                    )
                                )
                            )
                        );

                        echo $this->Form->hidden(
                            'GarageDataUnCheck',
                            array(
                                'class' => 'garage-data-uncheck',
                                'data-garage-id' => $task['Garage']['id'],
                                'data-url' => Router::url(
                                    array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_uncheck_task_garage',
                                    )
                                )
                            )
                        );

                        if ($task['TaskGarage']['completed'] == ConstantsBooleans::NO) {
                            echo $this->Html->link(
                                '<span class="ion-ios-checkmark-outline c-informacion"></span>',
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'ajax_check_task_garage',
                                    $task['Task']['id'],
                                    $task['Garage']['id']

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
                        } else {
                            echo $this->Html->link(
                                '<span class="ion-ios-checkmark-outline c-exito"></span>',
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'ajax_uncheck_task_garage',
                                    $task['Task']['id'],
                                    $task['Garage']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'class' => 'uncheck-task-js',
                                    'data-id' => $task['Task']['id'],
                                    'data-confirmmsg' => __t('Task.Confirm_unchecked'),
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No'),
                                    'title' => __t('Task.Mark_as_pending'),
                                    'data-title-complete' => __t('Task.Mark_as_complete'),
                                    'data-title-pending' => __t('Task.Mark_as_pending')
                                )
                            );
                        }
                    } else if($task['Distributor']['id'] != null) {
                        echo $this->Form->hidden(
                            'DistributorDataCheck',
                            array(
                                'class' => 'distributor-data-check',
                                'data-distributor-id' => $task['Distributor']['id'],
                                'data-url' => Router::url(
                                    array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_check_task_distributor',
                                    )
                                )
                            )
                        );

                        echo $this->Form->hidden(
                            'DistributorDataUnCheck',
                            array(
                                'class' => 'distributor-data-uncheck',
                                'data-distributor-id' => $task['Distributor']['id'],
                                'data-url' => Router::url(
                                    array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_uncheck_task_distributor',
                                    )
                                )
                            )
                        );

                        if ($task['TaskDistributor']['completed'] == ConstantsBooleans::NO) {
                            echo $this->Html->link(
                                '<span class="ion-ios-checkmark-outline c-informacion"></span>',
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'ajax_check_task_distributor',
                                    $task['Task']['id'],
                                    $task['Distributor']['id']

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
                        } else {
                            echo $this->Html->link(
                                '<span class="ion-ios-checkmark-outline c-exito"></span>',
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'ajax_uncheck_task_distributor',
                                    $task['Task']['id'],
                                    $task['Distributor']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'class' => 'uncheck-task-js',
                                    'data-id' => $task['Task']['id'],
                                    'data-confirmmsg' => __t('Task.Confirm_unchecked'),
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No'),
                                    'title' => __t('Task.Mark_as_pending'),
                                    'data-title-complete' => __t('Task.Mark_as_complete'),
                                    'data-title-pending' => __t('Task.Mark_as_pending')
                                )
                            );
                        }
                    }else{
                        echo $this->Form->hidden(
                            'SubTaskType',
                            array(
                                'class' => 'subtask-type',
                            )
                        );

                        if ($task['Task']['task_status_id'] == ConstantsStatusTasks::PENDING) {
                            echo $this->Html->link(
                                '<span class="ion-ios-checkmark-outline c-informacion"></span>',
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'ajax_check_task',
                                    $task['Task']['id'],

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
                            ?>
                            <?php
                        } else if ($task['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED) {
                            echo $this->Html->link(
                                '<span class="ion-ios-checkmark-outline c-exito"></span>',
                                array(
                                    'controller' => 'tasks',
                                    'action' => 'ajax_uncheck_task',
                                    $task['Task']['id']
                                ),
                                array(
                                    'escape' => false,
                                    'class' => 'uncheck-task-js',
                                    'data-id' => $task['Task']['id'],
                                    'data-confirmmsg' => __t('Task.Confirm_unchecked'),
                                    'data-yes' => __t('General.Yes'),
                                    'data-no' => __t('General.No'),
                                    'title' => __t('Task.Mark_as_pending'),
                                    'data-title-complete' => __t('Task.Mark_as_complete'),
                                    'data-title-pending' => __t('Task.Mark_as_pending')
                                )
                            );
                        }
                    }

                    echo $this->Html->link(
                        '<span class="aag-icon-papelera c-fallo"></span>',
                        array(
                            'controller' => 'tasks',
                            'action' => 'ajax_delete_subtask'
                        ),
                        array(
                            'escape' => false,
                            'class' => 'delete-subtask-js',
                            'data-id' => $task['Task']['id'],
                            'data-task_garage_id' => $task['TaskGarage']['id'],
                            'data-task_distributor_id' => $task['TaskDistributor']['id'],
                            'data-confirmmsg' => __t('Task.Confirm_delete'),
                            'data-yes' => __t('General.Yes'),
                            'data-no' => __t('General.No')
                        )
                    ); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<br/>
<?php echo $this->element('Comun/paginacion'); ?>