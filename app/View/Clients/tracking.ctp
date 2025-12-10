<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('CRM.Crm'),
                array(
                    'controller' => 'dashboard',
                    'action' => 'home'
                )
            ),
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'clients',
                    'action' => 'home'
                )
            ),
            __t('CRM.Visit_history'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>


<div class="cnt-data aag-padding">
    <div class="d-inline-block p-right-1 cnt-title-report ">
        <?php echo $this->element('../Clients/Elements/menu') ?>
        <div class="clear cnt-form-animate">
            <?php echo $this->element('../Clients/Elements/search_tracking'); ?>
        </div>
        <div class="o-auto">
            <table class="table-tracking">
                <thead>
                <tr>
                    <th class="ta-center"><?php echo __t('Appointment.Follow'); ?></th>
                    <th class="ta-center"><?php echo __t('CRM.Date'); ?></th>
                    <th class="ta-center"><?php echo __t('CRM.Feeling'); ?></th>
                    <th class="ta-center"><?php echo __t('CRM.Status'); ?></th>
                    <th class="ta-center"><?php echo __t('CRM.Appointment_type'); ?></th>
                    <th><?php echo __t('Appointment.Feedback'); ?></th>
                    <th><?php echo __t('Appointment.Comments'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Edit'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach( $appointments as $key => $appointment )
                {
                    ?>
                    <tr>
                        <td class="td-icono">
                            <div style="margin: 0; text-align: center;">
                                <?php
                                if ($appointment['Appointment']['requires_follow_up']) {
                                    ?> <span class="icon-follow"
                                                style="color: #eba216"></span> <?php
                                } else {
                                    ?> <span class="icon-follow"></span> <?php
                                }
                                ?>
                            </div>
                        </td>
                        <td class="td-fecha ta-center">
                            <div>
                                <?php echo Fecha::toFormatoVistaFecha(h($appointment['Appointment']['date'])); ?>
                            </div>
                        </td>
                        <td class="td-feeling">
                            <?php if(isset($appointment['Appointment']['appointment_feeling_id']) || $appointment['Appointment']['appointment_feeling_id'] != ConstantsBooleans::NO) { ?>
                                <div style="margin: 0;text-align: center;color:<?php echo h($feelings_colors[$appointment['Appointment']['appointment_feeling_id']]); ?>">
                                    <img style="max-height: 20px;"
                                            src="/img/iconos/<?php echo h($feelings[$appointment['Appointment']['appointment_feeling_id']])?>"
                                            alt="<?php echo h($feelings_list[$appointment['Appointment']['appointment_feeling_id']]); ?>"
                                            title="<?php echo h($feelings_list[$appointment['Appointment']['appointment_feeling_id']]); ?>"/>
                                </div>
                            <?php } ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            $status_color = null;
                            if($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::ACCOMPLISHED)
                            {
                                $status_color = '#7bd36f';
                            }
                            else if($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PLANNED)
                            {
                                $status_color = '#303030';
                            }
                            else if($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::CANCELED)
                            {
                                $status_color = '#FE472F';
                            }
                            else if($appointment['Appointment']['appointment_status_id'] == ConstantsStatusAppointments::PENDING)
                            {
                                $status_color = '#DDC614';
                            }
                            else
                            {
                                $status_color = '#FE9F2F';
                            }
                            ?>
                            <div style="color:<?php echo h($status_color);?>" class="td-status ta-center">
                                <?php echo $status[$appointment['Appointment']['appointment_status_id']]; ?>
                            </div>
                        </td>
                        <td class="ta-center">
                            <?php echo $appointments_types[$appointment['Appointment']['appointment_type_id']]; ?>
                        </td>
                        <td class="td-feedback">
                            <div class="d-inline-block w-100p f-left">
                                <span class="fw-bold f-left">
                                    <?php
                                    if(isset($appointment['Appointment']['feedback_user_modification']))
                                    {
                                        echo h($users[$appointment['Appointment']['feedback_user_modification']]);
                                    }
                                    ?>
                                </span>
                                <span class="f-right">
                                    <?php echo Fecha::toFormatoVistaFecha(h($appointment['Appointment']['feedback_date_creation'])); ?>
                                </span>
                            </div>
                            <?php if(isset($appointment['Appointment']['feedback']) && !empty($appointment['Appointment']['feedback']) ){
                                echo "<p>". h(strip_tags($appointment['Appointment']['feedback'])) . "</p>";
                            }?>
                        </td>
                        <?php
                        $classComments = '';
                        if(count($appointments_comments) == ConstantsBooleans::NO)
                        {
                            $classComments = ' sin-fondo';
                        }
                        ?>
                        <td class="td-comments<?php echo h($classComments); ?>">
                            <?php
                            foreach($appointments_comments as $comment)
                            {
                                if($comment['AppointmentComment']['appointment_id'] == $appointment['Appointment']['id'])
                                {
                                    ?>
                                    <div class="d-inline-block w-100p f-left">
                                        <span class="fw-bold f-left">
                                            <?php echo h($users[$comment['AppointmentComment']['user_id']])?>
                                        </span>
                                        <span class="f-right">
                                            <?php echo Fecha::toFormatoVistaFecha(h($comment['AppointmentComment']['creation_date'])); ?>
                                        </span>
                                    </div>
                                    <?php if (isset($comment['AppointmentComment']['body']) && !empty($comment['AppointmentComment']['body'])) {
                                    echo "<p>" . h(strip_tags($comment['AppointmentComment']['body'])) . "</p>";
                                    }
                                }
                            }
                            ?>
                        </td>
                        <td class="ta-center">
                            <div style="margin: 0; text-align: center;">
                                <?php echo $this->Html->link(
                                    '<span class="icon-Visit_create"></span>',
                                    array(
                                        'controller' => 'appointments',
                                        'action' => 'edit',
                                        $appointment['Appointment']['id']
                                    ),
                                    array(
                                        'escape' => false,
                                        'title' => __t('Appointment.Edit_appointment')
                                    )
                                ); ?>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <br />
        <?php echo $this->element('Comun/paginacion'); ?>
    </div>
</div>
