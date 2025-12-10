<div class="o-auto clear">
    <table class="table-tracking">
        <thead>
        <tr>
            <th width="200" class="ta-center"><?php echo __t('CRM.Deadline'); ?></th>
            <th width="200"><?php echo __t('CRM.Task'); ?></th>
            <th width="200"><?php echo __t('Task.Created_by'); ?></th>
            <th><?php echo __t('CRM.Name'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($assigned_tasks as $task) {
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
                    <?php if($task['Task']['limit_date'] < date('Y-m-d')){ ?>
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
                        <?php echo h($users[$task['Task']['user_creation_id']]); ?>
                    </td>
                    <td class="ta-left" >
                        <?php
                        if ($task['Garage']['name']) {
                            ?> <span class="d-inline-block icon-garages"></span> <?php
                            if (strlen($task['Garage']['name']) > 30) {
                                echo substr(h($task['Garage']['name']), 0, 30) . '...';
                            } else {
                                echo h($task['Garage']['name']);
                            }
                        }
                        if ($task['Distributor']['name']) {
                            ?> <span class="d-inline-block icon-garages"></span> <?php
                            if (strlen($task['Distributor']['name']) > 30) {
                                echo substr(h($task['Distributor']['name']), 0, 30) . '...';
                            } else {
                                echo h($task['Distributor']['name']);
                            }
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        if(isset($assigned_tasks_deadline_null)) {
            $cont = count($assigned_tasks);
            foreach ($assigned_tasks_deadline_null as $task) {
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
                                <?php echo (!is_null($task['Task']['user_creation_id']) && !empty($users)) ? $users[$task['Task']['user_creation_id']] : ''; ?>
                            </td>
                            <td>
                                <?php
                                if ($task['Garage']['name']) {
                                    ?> <span class="d-inline-block icon-garages"></span> <?php
                                    if (strlen($task['Garage']['name']) > 30) {
                                        echo substr(h($task['Garage']['name']), 0, 30) . '...';
                                    } else {
                                        echo h($task['Garage']['name']);
                                    }
                                }
                                if ($task['Distributor']['name']) {
                                    ?> <span class='icon-distributors'></span> <?php
                                    if (strlen($task['Distributor']['name']) > 30) {
                                        echo substr(h($task['Distributor']['name']), 0, 30) . '...';
                                    } else {
                                        echo h($task['Distributor']['name']);
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
                'view' => ConstantsTasksExistingViews::INDEX_ASSIGNED_TO_ME
            ),
        ),
        array(
            'id' => 'see-all-assigned',
            'class' => 'aag-button small three'
        )
    ); ?>
</div>