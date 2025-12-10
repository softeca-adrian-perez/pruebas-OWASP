<div style="padding: 1em 1em 0 1em;">
    <b class="c-primary fs-large">
        <?php echo __t('Task.Task_visit'); ?>
    </b>
    <div class="m-bottom-1 ta-center f-right d-none">
        <?php
        echo $this->Html->link(
            "<span class='aag-icon-ojo'></span>" . __t('Task.View_completed_garage_tasks'),
            array(),
            array(
                'escape' => false,
                'id' => 'modal-completed-garage-task',
                'data-open' => "completedGarageTaskModal",
                'title' => __t('Appointment.View_completed_garage_tasks'),
                'class' => 'button-general tres conImg',
                'data-url' => Router::url(array(
                    'controller' => 'appointments',
                    'action' => 'ajax_get_completed_garage_task',
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
            <th width="50"></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($tasks_garages as $task) { ?>
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
                <i class="ion-ios-checkmark-outline icono-grande cursor-pointer complete_task_garage" style="margin:0 !important"
                    data-id="<?php echo $task['Task']['id']; ?>"
                    data-action="<?php echo 'complete' ?>"
                    data-url="<?php echo Router::url(array(
                        'controller' => 'tasks',
                        'action' => 'ajax_change_task_garage'
                    )); ?>"
                ></i>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>