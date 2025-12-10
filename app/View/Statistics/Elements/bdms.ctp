<?php
echo $this->Html->script('/js/garages_users_permissions.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
$config = CakeSession::read('Auth.User.Config');
?>
<div class="o-auto">
    <table class="table-tracking">
        <thead>
            <tr>
                <th>
                    <?php echo __t('Contact.BDM'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Number_of_garage_visits'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Number_of_distributor_visits'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Number_of_prospect_garage_visits'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Total_duration_garage_visits'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Formatted_total_duration_garage_visits'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Total_duration_distributor_visits'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Formatted_total_duration_distributor_visits'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Average_duration_garage_visits'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Formatted_average_duration_garage_visits'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Average_duration_distributor_visits'); ?>
                </th>
                <th class="ta-center">
                    <?php echo __t('Statistics.Formatted_average_duration_distributor_visits'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach( $bdms as $bdm ) {
                if( ($bdm['GarageVisits'] + $bdm['DistributorVisits'] + $bdm['EventVisits']) >0) {
                    ?>
                    <tr>
                        <td>
                            <?php
                            echo $this->Html->link(
                                $bdm['name'],
                                array(
                                    'controller' => 'statistics',
                                    'action' => 'bdm',
                                    '?' => array(
                                        'user_assigned_id' => $bdm['user_id'],
                                        'from' => $date_from,
                                        'to' => $date_to,
                                        'appointment_type_id' => array(ConstantsTypesAppointments::VISIT, ConstantsTypesAppointments::PROSPECT_GARAGE_VISIT),
                                        'appointment_status_id' => array(ConstantsStatusAppointments::ACCOMPLISHED),
                                        'network_id' => $network_id,
                                        'trading_group_id' => $trading_group_id
                                    )

                                ),
                                array('class' => 'c-primary')
                            );
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            if($bdm['GarageVisits']) {
                                echo $this->Html->link(
                                    $bdm['GarageVisits'],
                                    array(
                                        'controller' => 'statistics',
                                        'action' => 'bdm',
                                        '?' => array(
                                            'user_assigned_id' => $bdm['user_id'],
                                            'from' => $date_from,
                                            'to' => $date_to,
                                            'appointment_type_id' => ConstantsTypesAppointments::VISIT,
                                            'appointment_status_id' => array(ConstantsStatusAppointments::ACCOMPLISHED),
                                            'network_id' => $network_id,
                                            'trading_group_id' => $trading_group_id,
                                            'garage' => true
                                        )

                                    ),
                                    array('class' => 'c-primary')
                                );
                            }
                            else { echo '0'; }
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php
                            if($bdm['DistributorVisits']){
                                echo $this->Html->link(
                                    $bdm['DistributorVisits'],
                                    array(
                                        'controller' => 'statistics',
                                        'action' => 'bdm',
                                        '?' => array(
                                            'user_assigned_id' => $bdm['user_id'],
                                            'from' => $date_from,
                                            'to' => $date_to,
                                            'appointment_type_id' => ConstantsTypesAppointments::VISIT,
                                            'appointment_status_id' => array(ConstantsStatusAppointments::ACCOMPLISHED),
                                            'network_id' => $network_id,
                                            'trading_group_id' => $trading_group_id,
                                            'distributor' => true
                                        )

                                    ),
                                    array('class' => 'c-primary')
                                );
                            }
                            else { echo '0'; }
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php 
                            if($bdm['EventVisits']){
                                echo $this->Html->link(
                                    $bdm['EventVisits'],
                                    array(
                                        'controller' => 'statistics',
                                        'action' => 'bdm',
                                        '?' => array(
                                            'user_assigned_id' => $bdm['user_id'],
                                            'from' => $date_from,
                                            'to' => $date_to,
                                            'appointment_type_id' => ConstantsTypesAppointments::PROSPECT_GARAGE_VISIT,
                                            'appointment_status_id' => array(ConstantsStatusAppointments::EVENT),
                                            'network_id' => $network_id,
                                            'trading_group_id' => $trading_group_id,
                                        )
                                        
                                    ),
                                    array(
                                        'class' => 'c-primary'
                                    )
                                );
                            }
                            else{
                                echo '0';
                            }
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php echo $bdm['DurationGarageVisits']; ?>
                        </td>
                        <td class="ta-center" data-duration-garages=<?php echo $bdm['DurationGarageVisits']; ?>>
                            <?php
                            if($bdm['DurationGarageVisits']) {
                                $horas= intval($bdm['DurationGarageVisits']/3600);
                                $minutos=intval(($bdm['DurationGarageVisits']%3600)/60);
                                $segundos=(($bdm['DurationGarageVisits']%3600))%60;
                                echo $horas.'h '.$minutos.'m '.$segundos.'s';
                            }
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php echo $bdm['DurationDistributorVisits']; ?>
                        </td>
                        <td class="ta-center" data-duration-distributors=<?php echo $bdm['DurationDistributorVisits']; ?>>
                            <?php
                            if($bdm['DurationDistributorVisits']) {
                                $horas=intval($bdm['DurationDistributorVisits']/3600);
                                $minutos=intval(($bdm['DurationDistributorVisits']%3600)/60);
                                $segundos=(($bdm['DurationDistributorVisits']%3600))%60;
                                echo $horas.'h '.$minutos.'m '.$segundos.'s';
                            }
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php echo number_format($bdm['AverageDurationGarageVisits'], 2); ?>
                        </td>
                        <td class="ta-center" data-avg-duration-garagess=<?php echo $bdm['AverageDurationGarageVisits']; ?>>
                            <?php
                            if($bdm['AverageDurationGarageVisits']){
                                $horas=intval($bdm['AverageDurationGarageVisits']/3600);
                                $minutos=intval(($bdm['AverageDurationGarageVisits']%3600)/60);
                                $segundos=(($bdm['AverageDurationGarageVisits']%3600))%60;
                                echo $horas.'h '.$minutos.'m '.$segundos.'s';
                            }
                            ?>
                        </td>
                        <td class="ta-center">
                            <?php echo number_format($bdm['AverageDurationDistributorVisits'], 2); ?>
                        </td>
                        <td class="ta-center" data-avg-duration-distributors=<?php echo $bdm['AverageDurationDistributorVisits']; ?>>
                            <?php
                            if($bdm['AverageDurationDistributorVisits']){
                                $horas=intval($bdm['AverageDurationDistributorVisits']/3600);
                                $minutos=intval(($bdm['AverageDurationDistributorVisits']%3600)/60);
                                $segundos=(($bdm['AverageDurationDistributorVisits']%3600))%60;
                                echo $horas.'h '.$minutos.'m '.$segundos.'s';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>