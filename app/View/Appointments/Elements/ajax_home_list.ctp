    <div class="o-auto p-top-1">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Appointment.title', __t('General.Title')); ?></th>
                    <th><?php echo $this->Paginator->sort('Appointment.date', __t('Appointment.Date')); ?></th>
                    <th><?php echo $this->Paginator->sort('Appointment.end_date', __t('Appointment.End_date')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('Appointment.start_time', __t('Appointment.Start_time')); ?></th>
                    <th class="ta-center"><?php echo $this->Paginator->sort('Appointment.end_time', __t('Appointment.End_time')); ?></th>
                    <th><?php echo __t('Appointment.Customer'); ?></th>
                    <th><?php echo __t('Alert.Assigned_to'); ?></th>
                    <th class="ta-center"><?php echo __t('Appointment.Feedback_fill_up'); ?></th>
                    <th class="ta-center"><?php echo __t('Appointment.Requires_follow_up'); ?></th>
                    <th class="ta-center"><?php echo __t('Appointment.Feeling'); ?></th>
                    <th class="ta-center"><?php echo __t('Appointment.Status'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Type'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment) { ?>
                    <tr>
                        <td class="ta-defecto">
                            <?php echo $appointment['Appointment']['title'] ?>
                        </td>
                        <td class="ta-defecto">
                            <?php
                            echo Fecha::toFormatoVistaFecha($appointment['Appointment']['date']);
                            ?>
                        </td>
                        <td>
                            <?php
                            if (Fecha::isDate($appointment['Appointment']['end_date'])) {
                                echo Fecha::toFormatoVistaFecha($appointment['Appointment']['end_date']);
                            } else {
                                echo Fecha::toFormatoVistaFecha($appointment['Appointment']['date']);
                            }
                            ?>
                        </td>

                        <td class="ta-center">
                            <?php echo $appointment['Appointment']['start_time']; ?>
                        </td>
                        <td class="ta-center">
                            <?php echo $appointment['Appointment']['end_time']; ?>
                        </td>
                        <td class="c-defecto">
                            <?php if ($appointment['Appointment']['garage_id']) {
                                echo h($appointment['Garage']['name']);
                            }
                            if ($appointment['Appointment']['distributor_id']) {
                                echo h($appointment['Distributor']['name']);
                            } ?>
                        </td>
                        <td>
                            <?php echo $users[h($appointment['Appointment']['user_assigned_id'])]; ?>
                        </td>
                        <td class="ta-center">
                            <?php if ($appointment['Appointment']['feedback'] != '') {
                                echo __t('General.Yes');
                            } else {
                                echo __t('General.No');
                            } ?>
                        </td>
                        <td class="td-icono">
                            <div style="margin: 0; text-align: center;">
                                <?php
                                if ($appointment['Appointment']['requires_follow_up']) {
                                ?> <span class="icon-follow" style="color: #eba216"></span> <?php
                                                                                        } else {
                                                                                            ?> <span class="icon-follow"></span> <?php
                                                                                                                                    }
                                                                                                                                        ?>
                            </div>
                        </td>
                        <td class="td-feeling">
                            <?php if (isset($appointment['Appointment']['appointment_feeling_id']) || $appointment['Appointment']['appointment_feeling_id'] != ConstantsBooleans::NO) { ?>
                                <div style="margin: 0;text-align: center;color:<?php echo $feelings_colors[$appointment['Appointment']['appointment_feeling_id']] ?>">
                                    <img style="max-height: 20px;" src="/img/iconos/<?php echo $feelings[$appointment['Appointment']['appointment_feeling_id']] ?>" alt="<?php echo $feelings_list[$appointment['Appointment']['appointment_feeling_id']]; ?>" title="<?php echo $feelings_list[$appointment['Appointment']['appointment_feeling_id']]; ?>" />
                                </div>
                            <?php } ?>
                        </td>
                        <?php
                        $status_color = null;
                        if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::ACCOMPLISHED) {
                            $status_color = '#7bd36f';
                        } else if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PLANNED) {
                            $status_color = '#303030';
                        } else if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::CANCELED) {
                            $status_color = '#FE472F';
                        } else if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::RESCHEDULED) {
                            $status_color = '#FE9F2F';
                        } else if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PENDING) {
                            $status_color = '#DDC614';
                        } else {
                            $status_color = '#8E8E8E';
                        }
                        ?>
                        <td style="color:<?php echo $status_color; ?>" class="ta-center">
                            <?php echo h($status[$appointment['Appointment']['appointment_status_id']]); ?>
                        </td>
                        <td class="c-defecto ta-center">
                            <?php echo h($appointment_types[$appointment['Appointment']['appointment_type_id']]); ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            if ($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::EVENT) {
                                echo $this->Html->link(
                                    '<span class="aag-icon-editar c-primary"></span>',
                                    array(
                                        'controller' => 'appointments',
                                        'action' => 'edit_event',
                                        $appointment['Appointment']['id']
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('Appointment.Edit'),
                                    )
                                );
                            } else {
                                echo $this->Html->link(
                                    '<span class="aag-icon-editar c-primary"></span>',
                                    array(
                                        'controller' => 'appointments',
                                        'action' => 'edit',
                                        $appointment['Appointment']['id']
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('Appointment.Edit'),
                                    )
                                );
                            }
                            if ($appointment['Appointment']['garage_id']) {
                                $action = 'report';

                                echo $this->Html->link(
                                    '<span class="icon-garages"></span>',
                                    array(
                                        'controller' => 'clients',
                                        'action' => $action,
                                        $appointment['Appointment']['garage_id']
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('CRM.Garage_profile'),
                                    )
                                );
                            }
                            if ($appointment['Appointment']['distributor_id']) {
                                $action = 'report_distributor';

                                echo $this->Html->link(
                                    '<span class="icon-distributors"></span>',
                                    array(
                                        'controller' => 'clients',
                                        'action' => $action,
                                        $appointment['Appointment']['distributor_id']
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('CRM.Distributor_profile'),
                                    )
                                );
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <br>
    <?php echo $this->element('Comun/paginacion'); ?>