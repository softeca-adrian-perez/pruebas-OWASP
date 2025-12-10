<div class="o-auto clear">
    <table class="table-tracking">
        <thead>
            <tr>
                <th width="200" class="ta-center"><?php echo __t('CRM.Deadline'); ?></th>
                <th width="200"><?php echo __t('CRM.Task'); ?></th>
                <th width="200"><?php echo __t('CRM.Assigned'); ?></th>
                <th><?php echo __t('CRM.Group_name'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($assigned_group_tasks as $task) {
                if ($task['Task']['task_status_id'] == ConstantsStatusTasks::PENDING) {
                    //When we click over de task go to this url
                    $url_task = Router::url(array(
                        'controller' => 'tasks',
                        'action' => 'edit',
                        $task['Task']['id'],
                    ));
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
                            <?php if (strlen($task['Task']['title']) < 30) {
                                echo $this->Html->link(
                                    $task['Task']['title'],
                                    $url_task,
                                    array(
                                        'class' => 'c-informacion'
                                    )
                                );
                            } else {
                                echo $this->Html->link(
                                    substr($task['Task']['title'], 0, 30) . '...',
                                    $url_task,
                                    array(
                                        'class' => 'c-informacion'
                                    )
                                );
                            } ?>
                        </td>
                        <td class="c-defecto">
                            <?php echo h($task['User']['name']) . ' ' . h($task['User']['surname']); ?>
                        </td>
                        <td>
                            <?php echo h($task['ContactList']['name']); ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            if (isset($assigned_group_tasks_deadline_null)) {
                $cont = count($assigned_group_tasks);
                foreach ($assigned_group_tasks_deadline_null as $task) {
                    if ($cont < ConstantsLimitDashboard::TASK) {
                        if ($task['Task']['task_status_id'] == ConstantsStatusTasks::PENDING) {
                            //When we click over de task go to this url
                            $url_task = Router::url(array(
                                'controller' => 'tasks',
                                'action' => 'edit',
                                $task['Task']['id'],
                            ));
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
                                    <?php if (strlen($task['Task']['title']) < 30) {
                                        echo $this->Html->link(
                                            $task['Task']['title'],
                                            $url_task,
                                            array(
                                                'class' => 'c-informacion'
                                            )
                                        );
                                    } else {
                                        echo $this->Html->link(
                                            substr($task['Task']['title'], 0, 30) . '...',
                                            $url_task,
                                            array(
                                                'class' => 'c-informacion'
                                            )
                                        );
                                    } ?>
                                </td>
                                <td class="c-defecto">
                                    <?php echo h($task['User']['name']) . ' ' . h($task['User']['surname']); ?>
                                </td>
                                <td>
                                    <?php echo h($task['ContactList']['name']); ?>
                                </td>
                            </tr>
            <?php
                        }
                    }
                    $cont++;
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
                'view' => ConstantsTasksExistingViews::INDEX_ASSIGNED_TO_MY_GROUP
            ),
        ),
        array(
            'id' => 'see-all-group',
            'class' => 'aag-button small three',
            'escape' => false
        )
    );
    ?>
</div>