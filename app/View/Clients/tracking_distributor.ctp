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
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back')?></a>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="d-inline-block p-right-1 cnt-title-report">
        <?php echo $this->element('../Clients/Elements/menu_distributor') ?>
    </div>
    <div class="clear cnt-form-animate">
        <?php echo $this->element('../Clients/Elements/search_tracking'); ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
            <tr>
                <th class="ta-center" width="165"><?php echo __t('Appointment.Follow'); ?></th>
                <th class="ta-center"><?php echo __t('CRM.Date'); ?></th>
                <th class="ta-center"><?php echo __t('CRM.Feeling'); ?></th>
                <th class="ta-center"><?php echo __t('CRM.Status'); ?></th>
                <th class="ta-center"><?php echo __t('CRM.Appointment_type'); ?></th>
                <th><?php echo __t('Appointment.Feedback'); ?></th>
                <th class="ta-center"><?php echo __t('Appointment.Objectives'); ?></th>
                <th><?php echo __t('Appointment.Comments'); ?></th>
                <th class="ta-center"><?php echo __t('General.Edit'); ?></th>
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
                    <td class="ta-center">
                        <?php
                        echo $this->Html->link(
                            "<span class='ion-information-circled'></span>",
                            'javascript:;',
                            array(
                                'escape' => false,
                                'data-open' => "objectives".$appointment['Appointment']['id'],
                                'title' => __t('General.View'),
                                'class' => '',
                                'style' => 'background-color: transparent !important;'
                            )
                        );

                        ?>
                        <div style="display: none;" id="objectives<?php echo $appointment['Appointment']['id']; ?>" class="reveal-modal background-color-primary" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" >
                            <div class="columns medium-12">
                                <h2>
                                    <?php echo __t('Appointment.Management_objectives'); ?>
                                </h2>
                                <div class="o-auto">
                                    <table class="table-tracking">
                                        <thead>
                                            <tr>
                                                <th><?php echo __t('Appointment.Objective');?></th>
                                                <th><?php echo __t('Appointment.Objective_status');?></th>
                                                <th><?php echo __t('Appointment.Post_visit_comment');?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($management_objectives as $key => $objective) { ?>
                                                <?php if( isset($appointment['Objectives'][$key] ) ){ ?>
                                                    <tr>
                                                        <td class="c-defecto">
                                                            <?php
                                                                echo h($objective);
                                                            ?>
                                                        </td>
                                                        <td class="c-defecto">
                                                            <?php
                                                                echo isset($appointment['Objectives'][$key]) ? $objectives_status[$appointment['Objectives'][$key]['status']] : $objectives_status[0];
                                                            ?>
                                                        </td>
                                                        <td class="c-defecto">
                                                            <?php
                                                                echo  isset($appointment['Objectives'][$key]) ? $appointment['Objectives'][$key]['comment'] : '';
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <h2 class="p-top-1">
                                    <?php echo __t('Appointment.Personal_objectives'); ?>
                                </h2>
                                <div class="o-auto">
                                    <table class="table-tracking">
                                        <thead>
                                            <tr>
                                                <th><?php echo __t('Appointment.Objective');?></th>
                                                <th><?php echo __t('Appointment.Objective_status');?></th>
                                                <th><?php echo __t('Appointment.Post_visit_comment');?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if( $appointment['Objectives'] ){?>
                                            <?php foreach ( $appointment['Objectives'] as $key => $objective) { ?>
                                                <?php if( !$key ){?>
                                                    <tr>
                                                        <td class="c-defecto">
                                                            <?php
                                                                echo h($objective['objective']);
                                                            ?>
                                                        </td>
                                                        <td class="c-defecto">
                                                            <?php
                                                                echo $objectives_status[$appointment['Objectives'][$key]['status']];
                                                            ?>
                                                        </td>
                                                        <td class="c-defecto">
                                                            <?php
                                                                echo  $appointment['Objectives'][$key]['comment'];
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php foreach ($personal_objectives as $key => $objective) { ?>
                                            <?php if( isset($appointment['Objectives'][$key] ) ){ ?>
                                                <tr>
                                                    <td class="c-defecto">
                                                        <?php
                                                            echo h($objective);
                                                        ?>
                                                    </td>
                                                    <td class="c-defecto">
                                                        <?php
                                                            echo isset($appointment['Objectives'][$key]) ? $objectives_status[$appointment['Objectives'][$key]['status']] : $objectives_status[0];
                                                        ?>
                                                    </td>
                                                    <td class="c-defecto">
                                                        <?php
                                                            echo  isset($appointment['Objectives'][$key]) ? $appointment['Objectives'][$key]['comment'] : '';
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <a class="close-modal" data-close aria-label="Close">&#215;</a>
                        </div>
                    </td>
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
                    <td class="ta-center">
                        <?php
                        echo $this->Html->link(
                            '<span class="icon-Visit_create"></span>',
                            array(
                                'controller' => 'appointments',
                                'action' => 'edit',
                                $appointment['Appointment']['id']

                            ),
                            array(
                                'escape' => false,
                            )
                        );
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
