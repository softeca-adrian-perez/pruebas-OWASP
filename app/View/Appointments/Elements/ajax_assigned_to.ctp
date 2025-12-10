<?php
if (isset($this->request->data['Appointment']['user_assigned_id'])) {
    $default = $this->request->data['Appointment']['user_assigned_id'];
} else if (isset($this->request->query['user'])) {
    $default = $this->request->query['user'];
} else {
    $default = CakeSession::read('Auth.User.id');
}

// echo $this->Form->input(
//     'Appointment.user_assigned_id',
//     array(
//         'label' => __t('Appointment.Assign_to'),
//         'class' => 'clear_field select2Dinamico_user_bdm update_users_bdm',
//         'id' => 'assigned-to',
//         'type' => 'select',
//         'multiple' => false,
//         'empty' => true,
//         'options' => isset($user_assigned_id) ? $user_assigned_id : array(),
//         // 'default' => $user_assigned_id
//     )
// );
echo $this->Form->input(
    'Appointment.user_assigned_id',
    array(
        'label' => __t('Appointment.Assign_to'),
        'class' => 'select2-multiple disabled_fields',
        'default' => $default,
        'empty' => false,
        'options' => isset($users) ? $users : array(),
        'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
    )
);
