<?php
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$counter = 1;
foreach ( $garage_customer_activities as $activity ) { ?>
    <tr>
        <td class="order-row">
            <?php echo $counter; $counter++;?>
        </td>
        <td class="item-activity"
            data-id="<?php echo $activity['GarageCustomerActivity']['id']; ?>"
            data-activity_id="<?php echo $activity['GarageCustomerActivity']['customer_activity_id']; ?>">
            <?php echo h($customers_activities[$activity['GarageCustomerActivity']['customer_activity_id']]);?>
        </td>
        <td class="ta-center">
            <?php if( $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)){ ?>
                <span class="icon-delete cursor-pointer c-fallo delete-garage-customer-activity btn-hide" hidden></span>
            <?php } ?>
        </td>
    </tr>
<?php } ?>