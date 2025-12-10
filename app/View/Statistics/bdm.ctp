<?php
echo $this->Html->script('gd_export_excel.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('jquery.fileDownload.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

$user = CakeSession::read('Auth.User.id');

?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Statistics.Statistics'),
                array(
                    'controller' => 'statistics',
                    'action' => 'home'
                )
            ),
            __t('Statistics.Bdm'),
        ));
        ?>
    </div>
    <div>
        <?php
        echo $this->Html->link(
            __t('General.Back'),
            array(
                'controller' => 'statistics',
                'action' => 'home',
                '?' => array(
                    'from' => $from,
                    'to' => $to,
                    'network_id' => $search['network_id'],
                    'trading_group_id' => $search['trading_group_id'],
                )
            ),
            array(
                'class' => 'aag-button medium four',
                'style' => 'margin-top:0 !important;'
            )
        );
        ?>
    </div>
</div>

<div class="cnt-data aag-padding">
    <div class="aag-title p-bottom-1">
        <?php echo __t('Statistics.Bdm') ?>
    </div>
    <?php echo $this->element('../Statistics/Elements/search'); ?>

    <div class="flex ai-center gap-1 p-top-1">
        <div class="aag-subtitle m-right-auto">
            <?php echo h($users[$user_id]); ?>
            <?php echo __t('General.From') . ": " . h($from) . " " . __t('General.To') . ": " . h($to); ?>
        </div>
        <?php echo $this->Form->button(
            __t('General.Export') . ' ' . __t('Contact.BDM'),
            array(
                'class' => 'gd-export-excel-buscador-js btn-export aag-button medium',
                'value' => 'submit',
                'escape' => false,
                'name' => 'export',
                'data-url' => Router::url(
                    array(
                        'controller' => 'statistics',
                        'action' => 'ajax_bdm_excel',
                        $user_id,
                        $type
                    )
                ),
            )
        ); ?>
    </div>
    <div class="o-auto p-top-1">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Appointment.date', __t('Appointment.Date')); ?></th>
                    <th><?php echo $this->Paginator->sort('Appointment.end_date', __t('Appointment.End_date')); ?></th>
                    <th class="ta-center"><?php echo __t('Appointment.Start_time'); ?></th>
                    <th class="ta-center"><?php echo __t('Appointment.End_time'); ?></th>
                    <th><?php echo __t('Appointment.Customer'); ?></th>
                    <th><?php echo __t('Alert.Assigned_to'); ?></th>
                    <th class="ta-center"><?php echo __t('Appointment.Feedback_fill_up'); ?></th>
                    <th class="ta-center"><?php echo __t('Appointment.Requires_follow_up'); ?></th>
                    <th class="ta-center"><?php echo __t('Appointment.Feeling'); ?></th>
                    <th class="ta-center"><?php echo __t('Appointment.Status'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Type'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment) { ?>
                    <tr>
                        <td class="ta-defecto">
                            <?php echo Fecha::toFormatoVistaFecha($appointment['Appointment']['date']); ?>
                        </td>
                        <td class="ta-defecto">
                            <?php

                            if (Fecha::isDate($appointment['Appointment']['end_date'])) {
                                $date_to = Fecha::toFormatoVistaFecha($appointment['Appointment']['end_date']);
                            } else {
                                $date_to = Fecha::toFormatoVistaFecha($appointment['Appointment']['date']);
                            }
                            echo h($date_to);

                            ?>
                        </td>

                        <td class="ta-center">
                            <?php echo $appointment['Appointment']['start_time']; ?>
                        </td>
                        <td class="ta-center">
                            <?php echo $appointment['Appointment']['end_time']; ?>
                        </td>
                        <td class="c-defecto">
                            <?php
                            if ($appointment['Appointment']['garage_id']) {
                                echo $this->Html->link(
                                    $appointment['Garage']['name'],
                                    array(
                                        'controller' => 'clients',
                                        'action' => 'report',
                                        $appointment['Appointment']['garage_id']
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                            }
                            if ($appointment['Appointment']['distributor_id']) {
                                echo $this->Html->link(
                                    $appointment['Distributor']['name'],
                                    array(
                                        'controller' => 'clients',
                                        'action' => 'report_distributor',
                                        $appointment['Appointment']['distributor_id']
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                            }
                            ?>
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
                            <?php
                            echo h($appointment_types[$appointment['Appointment']['appointment_type_id']]);
                            if ($appointment['Appointment']['garage_id']) { ?>
                                <span class="icon-garages" title="<?php echo __t('Garage.Garage'); ?>"></span>
                            <?php }
                            if ($appointment['Appointment']['distributor_id']) { ?>
                                <span class="icon-distributors" title="<?php echo __t('Distributor.Distributor'); ?>"></span>
                            <?php }
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