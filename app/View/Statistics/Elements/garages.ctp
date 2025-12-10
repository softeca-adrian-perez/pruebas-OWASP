<?php 
echo $this->Html->script('/js/garages_users_permissions.js?v='.Configure::read('VERSION_CACHE'), array('block' => 'script'));
$config = CakeSession::read('Auth.User.Config'); ?>
<div class="medium-12 columns">
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Appointment.Customer'); ?></th>
                    <th><?php echo __t('Contact.BDM'); ?></th>
                    <th><?php echo __t('Garage.Town'); ?></th>
                    <th><?php echo __t('Garage.Address'); ?></th>
                    <th><?php echo __t('Garage.Phone'); ?></th>
                    <?php
                    //If it's Benelux there is not garage g_number, but if the role is Super Admin, garages from both regions can be present at the same time
                    if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) { ?>
                        <th><?php echo __t('Garage.G_number'); ?></th>
                    <?php } ?>
                    <th class="ta-center"><?php echo __t('Garage.Ref_code'); ?></th>
                    <th class="ta-center"><?php echo __t('Visit.Visits'); ?></th>
                    <th></th>
                    <th class="ta-center"><?php echo __t('Visit.Last_visit'); ?></th>
                    <th></th>
                    <th class="ta-center"><?php echo __t('Contract.Start_date'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach($garages as $key => $garage){ ?>
                    <tr>
                        <td>
                            <?php 
                            echo $this->Html->link(
                                $garage['name'],
                                array(
                                    'controller' => 'clients',
                                    'action' => 'report',
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
                            if($garage['bdms']){
                                foreach($garage['bdms'] as $bdm){
                                    echo isset($contacts[$bdm]) ? $contacts[$bdm] : null;
                                    if ($bdm !== end($garage['bdms'])) {
                                        echo "<br/>";
                                    }
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php  echo $garage['town']; ?>
                        </td>
                        <td>
                            <?php  echo $garage['address']; ?>
                        </td>
                        <td>
                            <?php  echo $garage['phone']; ?>
                        </td>
                        <?php
                        if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) { ?>
                            <td class="ta-center">
                                <?php  echo $garage['g_number_id']; ?>
                            </td>
                        <?php } ?>
                        <td class="ta-center">
                            <?php  echo $garage['ref_code']; ?>
                        </td>
                        <td class="ta-center">
                            <?php  echo $garage['count']; ?>
                        </td>
                        <td>
                            <?php echo strtotime($garage['last_visit']); ?>
                        </td>
                        <td class="ta-center">
                            <?php echo Fecha::toFormatoVista($garage['last_visit']); ?>
                        </td>
                        <td>
                        <?php echo isset($garage['contract_start']['GarageNetwork']) ? strtotime($garage['contract_start']['GarageNetwork']['contract_start_date']) :0 ; ?>
                        </td>
                        <td class="ta-center">
                            <?php echo isset($garage['contract_start']['GarageNetwork']) ? Fecha::toFormatoVista($garage['contract_start']['GarageNetwork']['contract_start_date']) :'' ; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>