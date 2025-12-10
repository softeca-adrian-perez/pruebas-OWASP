<?php 
echo $this->Html->script('/js/garages_users_permissions.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
$config = CakeSession::read('Auth.User.Config'); ?>
<div class="medium-12 columns">
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th class="ta-center" ><?php echo __t('Distributor.Account_number'); ?></th>
                    <th><?php echo __t('Appointment.Customer'); ?></th>
                    <th><?php echo __t('Contact.BDM'); ?></th>
                    <th><?php echo __t('Distributor.Postcode'); ?></th>
                    <th><?php echo __t('Distributor.Town'); ?></th>
                    <th><?php echo __t('Distributor.Phone'); ?></th>
                    <th class="ta-center"><?php echo __t('Visit.Visits'); ?></th>
                    <th></th>
                    <th class="ta-center"><?php echo __t('Visit.Last_visit'); ?></th>
                    <th></th>
                    <th class="ta-center"><?php echo __t('Contract.Start_date'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($distributors as $key => $distributor){ ?>
                    <tr>
                        <td class="ta-center">
                            <?php  echo $distributor['account_number']; ?>
                        </td>
                        <td>
                            <?php 
                            echo $this->Html->link(
                                $distributor['name'],
                                array(
                                    'controller' => 'clients',
                                    'action' => 'report_distributor',
                                    $key
                                ),
                                array(
                                    'class' => 'c-primary'
                                )
                            ); 
                            ?>
                        </td>
                        <td>
                            <?php
                            if($distributor['bdms']){
                                foreach($distributor['bdms'] as $bdm){
                                    if( isset($contacts[$bdm]) ){
                                        echo $contacts[$bdm];
                                        if ($bdm !== end($distributor['bdms'])) {
                                            echo "<br/>";
                                        }
                                    }                                  
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php  echo $distributor['postcode']; ?>
                        </td>
                        <td>
                            <?php  echo $distributor['town']; ?>
                        </td>
                        <td>
                            <?php  echo $distributor['phone']; ?>
                        </td>
                        <td class="ta-center">
                            <?php  echo $distributor['count']; ?>
                        </td>
                        <td>
                            <?php echo strtotime($distributor['last_visit']); ?>
                        </td>
                        <td class="ta-center">
                            <?php echo Fecha::toFormatoVista($distributor['last_visit']); ?>
                        </td>
                        <td>
                            <?php echo strtotime($distributor['contract_start']); ?>
                        </td>
                        <td class="ta-center">
                            <?php echo Fecha::toFormatoVista($distributor['contract_start']); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
