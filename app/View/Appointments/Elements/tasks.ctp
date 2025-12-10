<?php
if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
    $hide_save = true;
}
if (isset($tasks)) { ?>
    <div class='d-none'>
        <?php foreach ($tasks as $task) { ?>
            <div class="" id="tooltip_frame<?php echo $task['Task']['id'] ?>">
                <ul class="ls-none">
                    <?php if (isset($task['Task']['Files'])) {
                        foreach ($task['Task']['Files'] as $key => $task_file) { ?>
                            <li class='task-tooltipster download-task' data-id="<?php echo $key ?>"
                                data-url="<?php echo Router::url(
                                    array(
                                        'controller' => 'tasks_files',
                                        'action' => 'download_file',
                                        $key
                                    )
                                ); ?>">
                                <?php echo h($task_file);?>
                            </li>
                            <?php
                        }
                    } ?>
                </ul>
            </div>
            <?php
        } ?>
    </div>
<?php } ?>

<?php
echo $this->Form->hidden(
    'url_check_hidden',
    array(
        'id' => 'url_check_task',
        'value' => Router::url(array(
            'controller' => 'tasks',
            'action' => 'ajax_check_task'
        ))
    )
);
echo $this->Form->hidden(
    'url_check_hidden',
    array(
        'id' => 'url_uncheck_task',
        'value' => Router::url(array(
            'controller' => 'tasks',
            'action' => 'ajax_uncheck_task'
        ))
    )
);
?>
<table class="table-tracking table-tracking-inverse">
    <thead>
        <tr>
            <th style="min-width:120px;"><?php echo __t('General.Title'); ?></th>
            <th><?php echo __t('Appointment.Description'); ?></th>
            <th class="ta-center" width="120"><?php echo __t('Appointment.Date'); ?></th>
            <th class="ta-center" width="120"><?php echo __t('Appointment.Created_by'); ?></th>
            <th class="ta-center" width="120"><?php echo __t('Task.Assigned_to'); ?></th>
            <th class="ta-center" width="30"><?php echo __t('General.Files'); ?></th>
            <?php if(!isset($hide_save)) { ?>
                <th class="ta-center" width="30"></th>
                <th class="ta-center" width="30"></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($tasks)) {
            foreach ($tasks as $task) {
                ?>
                <tr>
                    <td>
                        <strong>
                            <?php echo (substr($task['Task']['title'], 0, 40) != $task['Task']['title']) ?  h(substr($task['Task']['title'], 0, 40)) . '...' : h($task['Task']['title']);?>
                        </strong>
                    </td>
                    <td>
                        <?php echo $task['Task']['body']; ?>
                    </td>
                    <td class="ta-center">
                        <?php echo Fecha::toFormatoVistaFecha($task['Task']['limit_date']); ?>
                    </td>
                    <td class="ta-center">
                        <?php echo h($users[$task['Task']['user_creation_id']]);?>
                    </td>
                    <td class="ta-center">
                        <?php if (!empty($task['Task']['user_assigned_id'])) {
                            // echo $task['Task']['user_assigned_id'];
                        } else if (!empty($task['Task']['ContactsLists'])) {
                            foreach ($task['Task']['ContactsLists'] as $contact_list) {
                                echo h($contact_list);
                            }
                        } ?>
                        <?php echo $task['Task']['user_assigned_id'] ? h($users[$task['Task']['user_assigned_id']]) : ''; ?>
                    </td>
                    <td class="ta-center">
                        <?php
                        if(!empty($task['Task']['Files'])) {
                            ?>
                            <span style="font-size: 30px"
                                class="ion-paperclip tooltip_view cursor-pointer"
                                data-tooltip-content="#tooltip_frame<?php echo $task['Task']['id'] ?>"
                                data-id="tooltip_frame<?php echo $task['Task']['id'] ?>"
                                id="paperclip<?php echo $task['Task']['id'] ?>">
                            </span>
                            <?php
                        }
                        ?>
                    </td>
                    <?php if(!isset($hide_save)) { ?>
                        <td class="ta-center">
                            <?php echo $this->Html->link(
                                "<span class='ion-ios-compose'></span>",
                                'javascript:void(0)',
                                array(
                                    'escape' => false,
                                    'title' => __t('General.Edit'),
                                    'data-open' => "taskModal",
                                    'class' => 'icon-edit-task',
                                    'data-id' => $task['Task']['id'],
                                    'data-url' => Router::url(array(
                                        'controller' => 'tasks',
                                        'action' => 'ajax_fill_form',
                                    ))
                                )
                            ); ?>
                        </td>
                        <td>
                            <?php if(ConstantsStatusTasks::COMPLETED == $task['Task']['task_status_id']){ ?>
                            <i class="ion-ios-checkmark icono-grande cursor-pointer revert_task c-exito" style="margin:0 !important"
                                data-id="<?php echo $task['Task']['id'];?>"
                                data-url="<?php echo Router::url(array(
                                    'controller' => 'tasks',
                                    'action' => 'ajax_uncheck_task',
                                    $task['Task']['id']
                                )); ?>"
                            ></i>
                            <?php } else { ?>
                            <i class="ion-ios-checkmark-outline icono-grande cursor-pointer complete_task" style="margin:0 !important"
                                data-id="<?php echo $task['Task']['id'];?>"
                                data-url="<?php echo Router::url(array(
                                    'controller' => 'tasks',
                                    'action' => 'ajax_check_task',
                                    $task['Task']['id']
                                )); ?>"
                            ></i>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>

<div style="display: none !important">
    <div class="d-inline-block w-100p p-top-1 clear" id="task-container-pending">
        <div class="d-inline-block clear w-100p">
            <span id="pending-tasks">
                <?php echo __t('Appointment.Pending_task'); ?>
            </span>
        </div>
        <?php if (!empty($tasks)) {?>
            <?php foreach ($tasks as $key => $task) {
                if ($task['Task']['task_status_id'] == ConstantsStatusTasks::PENDING) {?>
                    <div class="task-container" data-id="<?php echo $task['Task']['id'];?>">
                        <div class="columns medium-12 fieldset-tasks-incomplete-header">
                            <div class="medium-7 columns">
                                <?php if ($task['Task']['user_creation_id'] == CakeSession::read('Auth.User.id')) {
                                    $data_user = ConstantsBooleans::YES;
                                } else {
                                    $data_user = ConstantsBooleans::NO;
                                }
                                echo $this->Form->input(
                                    "task-$key",
                                    array(
                                        'label' => false,
                                        'type' => 'checkbox',
                                        'id' => "task-$key",
                                        'class' => 'check-task',
                                        'data-user' => $data_user,
                                        'data-task' => $task['Task']['id'],
                                        'data-url' => Router::url(
                                            array(
                                                'controller' => 'tasks',
                                                'action' => 'ajax_check_task',
                                                $task['Task']['id']
                                            )
                                        )
                                    )
                                ); ?>
                                <label for="task-<?php echo $key ?>" class="c-informacion">
                                    <?php echo $task['Task']['body']; ?>
                                </label>

                            </div>
                            <div class="columns medium-3">
                                <div class="row">
                                    <div class="columns medium-12">
                                        <div class="created-by">
                                            <strong>
                                                <?php echo __t('Appointment.Created_by'); ?>
                                            </strong>

                                        </div>
                                    </div>
                                    <div class="columns medium-12">
                                        <div class="created-by">
                                            <strong>
                                                <?php echo __t('Task.Assigned_to'); ?>
                                            </strong>
                                            <?php echo $task['Task']['user_assigned_id'] ? h($users[$task['Task']['user_assigned_id']]) : ''; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="columns medium-2">
                                <div class="deadline c-informacion">

                                </div>
                            </div>
                        </div>
                        <?php if (!empty($task['Task']['Users']) || !empty($task['Task']['ContactsLists'])) { ?>
                            <div class="columns medium-12 fieldset-tasks-incomplete-body">
                                <?php
                                if (!empty($task['Task']['Users'])) {
                                    ?>
                                    <div class="d-inline-block w-100p clear">
                                        <?php
                                        foreach ($task['Task']['Users'] as $user) {
                                            echo "<div class='task_contact'><span class='ion-ios-contact'></span>$user</div>";
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                if (!empty($task['Task']['ContactsLists'])) {
                                    ?>
                                    <div class="d-inline-block w-100p clear">
                                        <?php
                                        foreach ($task['Task']['ContactsLists'] as $contact_list) {
                                            echo "<div class='task_contact'><span class='ion-ios-list-outline'></span>$contact_list</div>";
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php
                }
            }
        }
        ?>
    </div>
    <div class="d-inline-block clear w-100p p-top-1" id="task-container-completed">
        <div class="d-inline-block clear w-100p">
            <span id="completed-tasks">
                <?php echo __t('Appointment.Completed_tasks'); ?>
            </span>
        </div>
        <?php if (!empty($tasks)) {
            foreach ($tasks as $key => $task) {
                if ($task['Task']['task_status_id'] == ConstantsStatusTasks::COMPLETED) { ?>
                    <div class="task-container" data-id="<?php echo $task['Task']['id'];?>">
                        <div class="columns medium-12 fieldset-tasks-complete-header">
                            <div class="columns medium-7">
                                <?php if ($task['Task']['user_creation_id'] == CakeSession::read('Auth.User.id')) {
                                    $data_user = ConstantsBooleans::YES;
                                } else {
                                    $data_user = ConstantsBooleans::NO;
                                }
                                echo $this->Form->input(
                                    "task-$key",
                                    array(
                                        'label' => false,
                                        'type' => 'checkbox',
                                        'id' => "task-$key",
                                        'class' => 'uncheck-task',
                                        'data-user' => $data_user,
                                        'data-task' => $task['Task']['id'],
                                        'data-url' => Router::url(
                                            array(
                                                'controller' => 'tasks',
                                                'action' => 'ajax_uncheck_task',
                                                $task['Task']['id']
                                            )
                                        )
                                    )
                                ); ?>
                                <label for="task-<?php echo $key ?>" class="c-exito">
                                    <?php echo $task['Task']['body']; ?>
                                </label>
                                <?php if (!empty($task['Task']['Files'])) { ?>
                                    <span
                                        class="ion-paperclip tooltip_view cursor-pointer"
                                        data-tooltip-content="#tooltip_frame<?php echo $task['Task']['id'] ?>"
                                        data-id="tooltip_frame<?php echo $task['Task']['id'] ?>"
                                        id="paperclip<?php echo $task['Task']['id'] ?>">
                                                    </span>
                                <?php } ?>
                                <?php echo $this->Html->link(
                                    "<span class='ion-ios-compose'></span>",
                                    'javascript:void(0)',
                                    array(
                                        'escape' => false,
                                        'title' => __t('General.Edit'),
                                        'data-open' => "taskModal",
                                        'class' => 'icon-edit-task',
                                        'data-id' => $task['Task']['id'],
                                        'data-url' => Router::url(array(
                                            'controller' => 'tasks',
                                            'action' => 'ajax_fill_form',
                                        ))
                                    )
                                ); ?>
                            </div>
                            <div class="columns medium-3">
                                <div class="row">
                                    <div class="columns medium-12">
                                        <div class="created-by">
                                            <strong>
                                                <?php echo __t('Appointment.Created_by'); ?>
                                            </strong>
                                            <?php echo $users[$task['Task']['user_creation_id']] . " " . Fecha::toFormatoVistaFecha($task['Task']['creation_date']) ?>
                                        </div>
                                    </div>
                                    <div class="columns medium-12">
                                        <div class="created-by">
                                            <strong>
                                                <?php echo __t('Task.Assigned_to'); ?>
                                            </strong>
                                            <?php echo $task['Task']['user_assigned_id'] ? h($users[$task['Task']['user_assigned_id']]) : ''; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="columns medium-2">
                                <div class="deadline c-informacion">
                                    <?php echo $task['Task']['limit_date'] ? (__t('Appointment.Deadline') . " " . Fecha::toFormatoVistaFecha($task['Task']['limit_date'])) : ''; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        if (!empty($task['Task']['Users']) || !empty($task['Task']['ContactsLists'])) {
                            ?>
                            <div class="columns medium-12 fieldset-tasks-complete-body">
                                <?php
                                if (!empty($task['Task']['Users'])) {
                                    ?>
                                    <div class="d-inline-block w-100p clear">
                                        <?php
                                        foreach ($task['Task']['Users'] as $user) {
                                            echo "<div class='task_contact'><span class='ion-ios-contact'></span>$user</div>";
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                if (!empty($task['Task']['ContactsLists'])) {
                                    ?>
                                    <div class="d-inline-block w-100p clear">
                                        <?php
                                        foreach ($task['Task']['ContactsLists'] as $contact_list) {
                                            echo "<div class='task_contact'><span class='ion-ios-list-outline'></span>$contact_list</div>";
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php
                }
            }
        } ?>
    </div>
</div>
