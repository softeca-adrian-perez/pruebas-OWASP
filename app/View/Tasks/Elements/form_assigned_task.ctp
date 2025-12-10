<?php
    $current_action = $this->request->action;
?>
<?php

if (isset($action) || $current_action == 'add_debrief_task' || $current_action == 'edit_debrief_task') {
    echo $this->Form->input(
        'DebriefTask.user_assigned_id',
        array(
            'label' => __t('Appointment.Assign_to'),
            'class' => 'clear_field select2Dinamico_user_bdm update_users_bdm sel2Dyn-js',
            'id' => 'assigned-to',
            'type' => 'select',
            'multiple' => false,
            'empty' => true,
            'options' => isset($user_assigned_id) ? $user_assigned_id : array(),
        )
    );
} else {
    echo $this->Form->input(
        'Task.user_assigned_id',
        array(
            'label' => __t('Appointment.Assign_to'),
            'class' => 'clear_field select2Dinamico_user_bdm update_users_bdm sel2Dyn-js',
            'id' => 'assigned-to',
            'type' => 'select',
            'multiple' => false,
            'empty' => true,
            'options' => isset($user_assigned_id) ? $user_assigned_id : array(),
        )
    );
}

?>