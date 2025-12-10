<div style="padding: 1em 1em 0 1em;">
    <b class="c-primary fs-large">
        <?php echo __t('Task.Task_visit'); ?>
    </b>
    <div class="m-bottom-1 ta-center f-right d-none">
        <?php
        echo $this->Html->link(
            "<span class='aag-icon-ojo'></span>" . __t('Task.View_completed_distributor_tasks'),
            array(),
            array(
                'escape' => false,
                'id' => 'modal-completed-distributor-task',
                'data-open' => "completedDistributorTaskModal",
                'title' => __t('Appointment.View_completed_distributor_tasks'),
                'class' => 'button-general tres conImg',
                'data-url' => Router::url(array(
                    'controller' => 'appointments',
                    'action' => 'ajax_get_completed_distributor_task',
                ))
            )
        );
        ?>
    </div>
</div>
<table class="table-tracking table-tracking-inverse p-1">
    <thead>
        <tr>
            <th width="300"><?php echo __t('General.Title'); ?></th>
            <th><?php echo __t('Appointment.Description'); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($tasks_distributors as $task) { ?>
        <tr class="task_garage">
            <td>
                <strong>
                    <?php echo (substr($task['Task']['title'], 0, 40) != $task['Task']['title']) ?  substr(h($task['Task']['title']), 0, 40) . '...' : h($task['Task']['title']);?>
                </strong>
            </td>
            <td>
                <?php echo h($task['Task']['body']); ?>
            </td>
            <td>
                <i class="ion-ios-checkmark-outline icono-grande cursor-pointer complete_task_distributor" style="margin:0 !important"
                    data-id="<?php echo $task['Task']['id']; ?>"
                    data-action="<?php echo 'complete' ?>"
                    data-url="<?php echo Router::url(array(
                        'controller' => 'tasks',
                        'action' => 'ajax_change_task_distributor'
                    )); ?>"
                ></i>
                <!-- <input
                    type="radio"
                class="radio_task_distributor d-none"
                name="tasks_distributor_<?php echo $task['Task']['id']; ?>"
                    id="tasks_complete_<?php echo $task['Task']['id']; ?>"
                    data-task_id="<?php echo $task['Task']['id']; ?>"
                    data-action="<?php echo 'complete' ?>"
                    data-url="<?php echo Router::url(array(
                        'controller' => 'tasks',
                    'action' => 'ajax_change_task_distributor'
                    )); ?>"
                    <?php echo $complete_tmp; ?>>

                <div class="columns complete_task">
                    <label for="tasks_complete_<?php echo $task['Task']['id']; ?>">
                        <span class="ion-checkmark"></span>
                    </label>
                </div>

                <input
                    type="radio"
                class="radio_task_distributor d-none"
                name="tasks_distributor_<?php echo $task['Task']['id']; ?>"
                    id="tasks_decline_<?php echo $task['Task']['id']; ?>"
                    data-task_id="<?php echo $task['Task']['id']; ?>"
                    data-action="<?php echo 'decline' ?>"
                    data-url="<?php echo Router::url(array(
                        'controller' => 'tasks',
                    'action' => 'ajax_change_task_distributor'
                    )); ?>"
                <?php echo $decline_tmp; ?>>

            <div class="columns decline_task">
                    <label for="tasks_decline_<?php echo $task['Task']['id']; ?>">
                        <span class="ion-close"></span>
                    </label>
                </div> -->
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>