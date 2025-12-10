<?php
$previous_url = CakeSession::read('url_referer');
$previous_url_params = Router::parse($this->request->referer('/', true));
$current_url = ConstantsHTTP::HTTPS . Configure::read('URL_BASE') . $this->here;
if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
    $hide_save = true;
}
if ($current_url == $previous_url) {
    if ($this->params['controller'] == 'appointments' && $this->action == ConstantsActionsNames::EDIT) {
        if (CakeSession::read('Auth.User.appointment_url')) {
            $previous_url = CakeSession::read('Auth.User.appointment_url');
        } else {
            $previous_url = Router::url(array('controller' => 'appointments', 'action' => 'home'));
        }
    }
} elseif ($previous_url_params['controller'] == 'appointments' && $previous_url_params['action'] == 'add') {
    if (CakeSession::read('Auth.User.appointment_url')) {
        $previous_url = CakeSession::read('Auth.User.appointment_url');
    } else {
        $previous_url = Router::url(array('controller' => 'appointments', 'action' => 'home'));
    }
}
echo $this->Html->link(__t('General.Back'), $previous_url, array('class' => 'aag-button medium two'));
if (isset($appointment) && $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PENDING) {
    $none = 'd-inline';
} else {
    $none = 'd-none';
}
if (!isset($hide_save)) {
?>
    <div id="cnt_save_and_send" class="<?php echo $none; ?>">
        <?php
        echo $this->Form->submit(
            __t('General.Save_and_send'),
            array(
                'div' => false,
                'name' => 'btn_exit',
                'class' => 'aag-button medium outlined green',
                'id' => 'btn-guardar',
                'data-warning' => __t('Appointment.No_objetives')
            )
        );
        ?>
    </div>
<?php
    if (!isset($appointment) || ($appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::ACCOMPLISHED && $appointment['Appointment']['appointment_status_id'] != ConstantsStatusAppointments::CANCELED)) {
        echo $this->Form->submit(
            __t('General.Save'),
            array(
                'div' => false,
                'name' => 'btn_no_exit',
                'class' => 'aag-button medium green',
                'id' => 'btn-guardar-no-exit'
            )
        );
    }
}
?>