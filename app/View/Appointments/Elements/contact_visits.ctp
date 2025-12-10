<?php
if(isset($appointment) && isset($appointment['Appointment'])) {
    if($appointment['Appointment']['visit_contact_name']) {
        $contacts_visits[$appointment['Appointment']['visit_contact_name']] = $appointment['Appointment']['visit_contact_name'];
        $default = $appointment['Appointment']['visit_contact_name'];
    }
    else {
        $default = $appointment['Appointment']['visit_contact_id'];
    }
    if($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::ACCOMPLISHED || $appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::CANCELED) {
        $disabled = 'disabled';
    }
    else { $disabled = false; }
}
else {
    $default = array();
    $disabled = false;
}
?>
<div class="medium-12 columns">
    <?php
    echo $this->Form->input(
        'Appointment.visit_contact_id',
        array(
            'label' => __t('Appointment.Visit_contact'),
            'type' => 'select',
            'id' => 'visit_contact',
            'class' => 'select2-multiple disabled_fields',
            'options' => $contacts_visits,
            'default' => $default,
            'disabled' => $disabled,
            'multiple' => false,
            'empty' => true,
            'disabled' => CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN ? false : true,
        )
    );
    ?>
</div>
<?php
if($disabled) {
    echo $this->Form->hidden(
        'Appointment.visit_contact_name_disabled',
        array(
            'id' => 'visit_contact_name',
            'value' => $appointment['Appointment']['visit_contact_name']
        )
    );
    echo $this->Form->hidden(
        'Appointment.visit_contact_id_disabled',
        array(
            'id' => 'visit_contact_id',
            'value' => $appointment['Appointment']['visit_contact_id']
        )
    );
}
?>
<script>
    $(document).ready(function() { $("#visit_contact").select2({tags: true}); });
</script>