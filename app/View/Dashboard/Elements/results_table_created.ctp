<div class="o-auto clear">
    <table class="table-tracking">
        <thead>
        <tr>
            <th width="200" class="ta-center"><?php echo __t('CRM.Deadline'); ?></th>
            <th width="200" class="ta-left"><?php echo __t('CRM.Task'); ?></th>
            <th width="200" class="ta-left"><?php echo __t('CRM.Assigned'); ?></th>
            <th class="ta-left"><?php echo __t('CRM.Name'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($created_tasks as $task) {
            if ($task['Task']['task_status_id'] == ConstantsStatusTasks::PENDING) {
                if ($task['Task']['appointment_id'] != null) {
                    if ($task['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
                        $url_task = Router::url(array(
                            'controller' => 'appointments',
                            'action' => 'edit_event',
                            $task['Task']['appointment_id'],
                        ));
                    } else {
                        $url_task = Router::url(array(
                            'controller' => 'appointments',
                            'action' => 'edit',
                            $task['Task']['appointment_id'],
                        ));
                    }
                } else {
                    $url_task = Router::url(array(
                        'controller' => 'tasks',
                        'action' => 'edit',
                        $task['Task']['id'],
                    ));
                }
                ?>
                <tr>
                    <?php if ($task['Task']['limit_date'] < date('Y-m-d')) { ?>
                        <td class="c-fallo ta-center">
                            <?php if ($task['Task']['limit_date'] != '0000-00-00') {
                                echo Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                            } ?>
                        </td>
                    <?php } else { ?>
                        <td class="c-defecto ta-center">
                            <?php if ($task['Task']['limit_date'] != '0000-00-00') {
                                echo Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                            } ?>
                        </td>
                    <?php } ?>
                    <td class="c-informacion">
                        <?php if(strlen($task['Task']['title']) < 30){
                            echo $this->Html->link(
                                $task['Task']['title'],
                                $url_task,
                                array(
                                    'class' => 'c-informacion'
                                )
                            );
                        } else {
                            echo $this->Html->link(
                                substr($task['Task']['title'],0,30) . '...',
                                $url_task,
                                array(
                                    'class' => 'c-informacion'
                                )
                            );
                        } ?>
                    </td>
                    <td style="color:black" class="ta-left">
                        <?php
                        if (isset($users[$task['Task']['user_assigned_id']]) && $users[$task['Task']['user_assigned_id']] != null)
                        {
                            echo $users[$task['Task']['user_assigned_id']];
                        }
                        ?>
                    </td>
                    <td class="ta-left">
                        <?php
                        if (isset($task[0]['garage_name']))
                        {
                            ?> <span class="d-inline-block icon-garages"></span> <?php
                            if (strlen($task[0]['garage_name']) > 30)
                            {
                                echo substr(h($task[0]['garage_name']), 0, 30) . '...';
                            }
                            else
                            {
                                echo h($task[0]['garage_name']);
                            }
                        }
                        if (isset($task[0]['distributor_name']))
                        {
                            ?> <span class='icon-distributors'></span> <?php
                            if(strlen($task[0]['distributor_name']) > 30)
                            {
                                echo substr(h($task[0]['distributor_name']), 0, 30) . '...';
                            }
                            else
                            {
                                echo h($task[0]['distributor_name']);
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        if(isset($created_tasks_deadline_null)) {
            $cont = count($created_tasks);
            foreach ($created_tasks_deadline_null as $task) {
                if($cont < ConstantsLimitDashboard::TASK) {
                    if ($task['Task']['task_status_id'] == ConstantsStatusTasks::PENDING) {
                        if ($task['Task']['appointment_id'] != null) {
                            if ($task['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
                                $url_task = Router::url(array(
                                    'controller' => 'appointments',
                                    'action' => 'edit_event',
                                    $task['Task']['appointment_id'],
                                ));
                            } else {
                                $url_task = Router::url(array(
                                    'controller' => 'appointments',
                                    'action' => 'edit',
                                    $task['Task']['appointment_id'],
                                ));
                            }
                        } else {
                            $url_task = Router::url(array(
                                'controller' => 'tasks',
                                'action' => 'edit',
                                $task['Task']['id'],
                            ));
                        }
                        ?>
                        <tr>
                            <?php if ($task['Task']['limit_date'] < date('Y-m-d')) { ?>
                                <td class="c-fallo ta-center">
                                    <?php if ($task['Task']['limit_date'] != '0000-00-00') {
                                        echo Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                                    } ?>
                                </td>
                            <?php } else { ?>
                                <td class="c-defecto ta-center">
                                    <?php if ($task['Task']['limit_date'] != '0000-00-00') {
                                        echo Fecha::toFormatoVistaFecha($task['Task']['limit_date']);
                                    } ?>
                                </td>
                            <?php } ?>
                            <td class="c-informacion">
                                <?php if(strlen($task['Task']['title']) < 30){
                                    echo $this->Html->link(
                                        $task['Task']['title'],
                                        $url_task,
                                        array(
                                            'class' => 'c-informacion'
                                        )
                                    );
                                } else {
                                    echo $this->Html->link(
                                        substr($task['Task']['title'],0,30) . '...',
                                        $url_task,
                                        array(
                                            'class' => 'c-informacion'
                                        )
                                    );
                                } ?>
                            </td>
                            <td style="color:black">
                                <?php echo !is_null($task['Task']['user_assigned_id']) ? $users[$task['Task']['user_assigned_id']] : ''; ?>
                            </td>
                            <td>
                                <?php
                                if(isset($task[0])){
                                    if ($task[0]['garage_name']) {
                                        ?> <span class="d-inline-block icon-garages"></span> <?php
                                        if (strlen($task[0]['garage_name']) > 30) {
                                            echo substr(h($task[0]['garage_name']), 0, 30) . '...';
                                        } else {
                                            echo h($task[0]['garage_name']);
                                        }
                                    }
                                    if ($task[0]['distributor_name']) {
                                        ?> <span class='icon-distributors'></span> <?php
                                        if (strlen($task[0]['distributor_name']) > 30) {
                                            echo substr(h($task[0]['distributor_name']), 0, 30) . '...';
                                        } else {
                                            echo h($task[0]['distributor_name']);
                                        }
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                $cont ++;
            }
        }
        ?>
        </tbody>
    </table>
</div>
<div class="cnt-button-see-all">
    <?php echo $this->Html->link(
            __t('CRM.See_all'),
            array(
                'controller' => 'tasks',
                'action' => 'home',
                '?' => array(
                    'task_status_id' => ConstantsStatusTasks::PENDING,
                    'view' => ConstantsTasksExistingViews::INDEX_CREATED_BY_ME
                ),
            ),
            array(
                'id' => 'see-all-created',
                'class' => 'aag-button small three',
                'escape' => false
            )
        );
     ?>
</div>