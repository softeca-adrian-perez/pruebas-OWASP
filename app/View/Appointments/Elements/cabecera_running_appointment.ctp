<?php if (!empty($my_appointment_assigned)) { ?>
    <div id="running_visit_id" class="running_visit">
        <?php if (!empty($my_appointment_assigned)) {
            $segunda_linea = '';
            $clase_dos_lineas = '';
            if ($my_appointment_assigned[0]['Garage']['name'] != null) {
                $segunda_linea = '<div>' . __t('Appointment.Garage_name') . ': ' . $my_appointment_assigned[0]['Garage']['name'] . '</div>';
                $clase_dos_lineas = ' dos-lineas';
            } elseif ($my_appointment_assigned[0]['Distributor']['name'] != null) {
                $segunda_linea = '<div>' . __t('Appointment.Distributor_name') . ': ' . $my_appointment_assigned[0]['Distributor']['name'] . '</div>';
                $clase_dos_lineas = ' dos-lineas';
            }
            echo $this->Html->link(
                __t('Appointment.Running_visit') . ' ' . $segunda_linea,
                array(
                    'controller' => 'appointments',
                    'action' => 'edit',
                    $my_appointment_assigned[0]['Appointment']['id']
                ),
                array(
                    'class' => 'word-cloud-title link_running_visit' . $clase_dos_lineas,
                    'escape' => false,
                    'style' => 'color: #de7b39'
                )
            );
            ?>

        <?php }
        ?>
    </div>
<?php } ?>