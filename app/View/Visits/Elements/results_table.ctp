<?php
$userAagRegionId=CakeSession::read('Auth.User.aag_region_id');
$userRole=CakeSession::read('Auth.User.role_id');
?>
<div class="medium-12 columns m-top-1 p-0">
    <div class="o-auto">
        <table id="visit-table" class="table-tracking">
            <thead>
            <tr>
                <th><input type="checkbox" id="mark_unmark_all"></th>
                <?php if ((isset($userAagRegionId) && ($userAagRegionId != ConstantsAAGRegionId::BENELUX)) || ($userRole == ConstantsRoles::SUPER_ADMIN)) { ?>
                    <th><?php echo __t('Garage.G_number');?></th>
                <?php } ?>
                <th><?php echo __t('Appointment.Customer'); ?></th>
                <th><?php echo __t('Garage.Postcode');?></th>
                <th><?php echo __t('Visit.City');?></th>
                <th class="ta-center"><?php echo __t('Visit.Last_visit');?></th>
                <th class="d-none"></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($garages as $key => $garage) { ?>
                    <?php echo $this->element('../Visits/Elements/row_garage',
                        array(
                            'garage' => $garage,
                            'key' => $key,
                        )
                    ); ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>