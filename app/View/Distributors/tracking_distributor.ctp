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
                __t('Distributor.Distributor'),
                array(
                    'controller' => 'clients',
                    'action' => 'home_distributors'
                )
            ),
            __t('CRM.Visit_history'),
        ));
        ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="d-inline-block p-right-1 cnt-title-report">
        <?php echo $this->element('../Distributors/Elements/menu_distributor') ?>
    </div>
    <?php echo $this->element('../Distributors/Elements/search_tracking'); ?>
    <div class="o-auto p-top-1">
        <table class="table-tracking">
            <thead>
            <tr>
                <th class="ta-center" width="165"><?php echo __t('Appointment.Follow'); ?></th>
                <th class="ta-center"><?php echo __t('CRM.Date'); ?></th>
                <th class="ta-center"><?php echo __t('CRM.Feeling'); ?></th>
                <th class="ta-center"><?php echo __t('CRM.Status'); ?></th>
                <th class="ta-center"><?php echo __t('CRM.Appointment_type'); ?></th>
                <th><?php echo __t('Appointment.Feedback'); ?></th>
                <th><?php echo __t('Appointment.Comments'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach( $appointments as $key => $appointment ){ ?>
                <tr class="va-top">
                    <td class="ta-center">
                        <?php
                        if ($appointment['Appointment']['requires_follow_up']) {
                            ?> <span class="icon-follow"
                                    style="color: #eba216"></span> <?php
                        } else {
                            ?> <span class="icon-follow"></span> <?php
                        }
                        ?>
                        </td>
                    <td class="td-fecha ta-center">
                        <div>
                            <?php echo Fecha::toFormatoVistaFecha(h($appointment['Appointment']['date'])); ?>
                        </div>
                    </td>
                    <td class="td-feeling ta-center">
                        <?php
                        if(isset($appointment['Appointment']['appointment_feeling_id']) || $appointment['Appointment']['appointment_feeling_id'] != ConstantsBooleans::NO)
                        {
                            ?> <img style="max-height: 20px;" src="/img/iconos/<?php echo h($feelings[$appointment['Appointment']['appointment_feeling_id']])?>" alt="<?php echo h($feelings_list[$appointment['Appointment']['appointment_feeling_id']]); ?>" title="<?php echo h($feelings_list[$appointment['Appointment']['appointment_feeling_id']]); ?>"/> <?php
                        }
                        ?>
                    </td>
                    <td class="td-status ta-center">
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
                        <div style="color:<?php echo $status_color;?>">
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
                    <td class="td-comments<?php echo $classComments; ?>">
                        <?php
                        foreach($appointments_comments as $comment)
                        {
                            if($comment['AppointmentComment']['appointment_id'] == $appointment['Appointment']['id'])
                            {
                                ?>
                                <div class="d-inline-block w-100p f-left">
                                    <span class="fw-bold f-left">
                                        <?php echo $users[$comment['AppointmentComment']['user_id']]?>
                                    </span>
                                    <span class="f-right">
                                        <?php echo Fecha::toFormatoVistaFecha(h($comment['AppointmentComment']['creation_date'])); ?>
                                    </span>
                                </div>
                                <?php
                                if (isset($comment['AppointmentComment']['body']) && !empty($comment['AppointmentComment']['body']))
                                {
                                    echo "<p>" . h(strip_tags($comment['AppointmentComment']['body'])) . "</p>";
                                }
                            }
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <br />
    <?php echo $this->element('Comun/paginacion'); ?>
</div>
