<?php
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        echo $this->Html->breadcrumb(array(
            $this->Html->link(
                __t('Garage.Garages'),
                array(
                    'controller' => 'garages',
                    'action' => 'home'
                )
            ),
            __t('Equipment.Equipment'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
            <button id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit')); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php } ?>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'equipment_and_software_garage')); ?>

<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <div class="row">
        <div class="aag-subtitle">
            <?php echo __t('Equipment.Equipment'); ?>
        </div>
        <div class="btn-hide" hidden>
            <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
                <div class="cnt-buttons-v2 d-inline f-right">
                    <?php
                    echo $this->Html->link(
                        __t('Garage.New_equipment'),
                        array(
                            'controller' => 'garages_equipments',
                            'action' => 'add',
                            $garage_id
                        ),
                        array(
                            'escape' => false,
                            'class' => 'aag-button small green',
                            'style' => 'position:relative; z-index: 1;',
                        )
                    );
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Equipment.Equipment'); ?></th>
                    <th><?php echo __t('Equipment.Type'); ?></th>
                    <th><?php echo __t('Equipment.Supplier'); ?></th>
                    <th><?php echo __t('Equipment.Brand'); ?></th>
                    <th><?php echo __t('General.Billing_schedule'); ?></th>
                    <th>
                        <?php
                        $msg = isset($country['Country']['symbol']) ? __t('General.Amount') . ' ' . $country['Country']['symbol'] : __t('General.Amount');
                        echo $msg;
                        ?>
                    </th>
                    <th><?php echo __t('General.Member_pay'); ?></th>
                    <th><?php echo __t('General.Garage_pay'); ?></th>
                    <th><?php echo __t('Equipment.Start_date'); ?></th>
                    <th><?php echo __t('Equipment.End_date'); ?></th>
                    <th><?php echo __t('General.Billed_by_aag'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garage_equipments as $equipment_tmp) { ?>
                    <tr>
                        <td class="link-text">
                            <?php
                            echo $this->Html->link(
                                $equipments[$equipment_tmp['GarageEquipment']['equipment_id']],
                                array(
                                    'controller' => 'garages_equipments',
                                    'action' => 'edit',
                                    $equipment_tmp['GarageEquipment']['id']
                                ),
                                array(
                                    'class' => 'c-primary',
                                )
                            );
                            ?>
                        </td>
                        <td>
                            <?php echo $equipment_tmp['GarageEquipment']['equipment_type_id'] ? h($equipment_types[$equipment_tmp['GarageEquipment']['equipment_type_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo $equipment_tmp['GarageEquipment']['supplier_id'] ? h($suppliers[$equipment_tmp['GarageEquipment']['supplier_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo $equipment_tmp['GarageEquipment']['brand_id'] ? h($brands[$equipment_tmp['GarageEquipment']['brand_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo $equipment_tmp['GarageEquipment']['billing_schedule_id'] ? h($billings_schedules[$equipment_tmp['GarageEquipment']['billing_schedule_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo $equipment_tmp['GarageEquipment']['amount']; ?>
                        </td>
                        <td>
                            <?php echo $equipment_tmp['GarageEquipment']['member_pay']; ?>
                        </td>
                        <td>
                            <?php echo $equipment_tmp['GarageEquipment']['garage_pay']; ?>
                        </td>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($equipment_tmp['GarageEquipment']['start_date'])); ?>
                        </td>
                        <td>
                            <?php echo h($equipment_tmp['GarageEquipment']['end_date']); ?>
                        </td>
                        <td>
                            <?php echo $equipment_tmp['GarageEquipment']['billed_by_aag'] == 0 ? 'No' : 'Yes'; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    if (
        !$this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
        $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)
    ) {
    ?>
        <div class="row p-top-1">
            <?php echo $this->Form->create(); ?>
            <div class="aag-subtitle">
                <?php echo __t('RequestedChanges.Describe_change'); ?>
            </div>
            <div class="ta-right cnt-buttons-v2">
                <?php
                echo $this->Form->button(
                    __t('RequestedChanges.Request_changes'),
                    array(
                        'type' => 'submit',
                        'name' => 'request_changes',
                        'style' => 'margin-top:0 !important;',
                        'class' => 'btn-edit edit',
                    )
                );
                ?>
            </div>
            <div class="medium-12 columns end">
                <?php
                echo $this->Form->input(
                    'ChangeDescription',
                    array(
                        'type' => 'text',
                        'name' => 'change_description',
                        'rows' => 7,
                        'label' => false
                    )
                );
                ?>
            </div>
        </div>
    <?php } ?>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Garage.Software'); ?>
    </div>
    <div class="btn-hide" hidden>
        <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
            <div class="cnt-buttons-v2 d-inline f-right">
                <?php
                echo $this->Html->link(
                    __t('Garage.New_software'),
                    array(
                        'controller' => 'garages_software',
                        'action' => 'add',
                        $garage_id
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green',
                    )
                );
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Garage.Software'); ?></th>
                    <th><?php echo __t('Software.Type'); ?></th>
                    <th><?php echo __t('Software.Supplier'); ?></th>
                    <th><?php echo __t('Software.Manufacturer'); ?></th>
                    <th><?php echo __t('General.Billing_schedule'); ?></th>
                    <th><?php echo __t('Garage.Version'); ?></th>
                    <th>
                        <?php
                        $msg = isset($country['Country']['symbol']) ? __t('General.Amount') . ' ' . $country['Country']['symbol'] : __t('General.Amount');
                        echo $msg;
                        ?>
                    </th>
                    <th><?php echo __t('General.Member_pay'); ?></th>
                    <th><?php echo __t('General.Garage_pay'); ?></th>
                    <th><?php echo __t('General.Number_subscription'); ?></th>
                    <?php if ($config[ConstantsConfig::SOFTWARE_USER_GARAGE]) { ?>
                        <th><?php echo __t('Garage.Username'); ?></th>
                    <?php
                    }
                    if ($config[ConstantsConfig::SOFTWARE_PASSWORD_GARAGE]) {
                    ?>
                        <th><?php echo __t('Garage.Password'); ?></th>
                    <?php } ?>
                    <th><?php echo __t('Garage.Start_date'); ?></th>
                    <th><?php echo __t('Garage.End_date'); ?></th>
                    <th><?php echo __t('General.Online_ordering'); ?></th>
                    <th><?php echo __t('General.Billed_by_aag'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garage_software as $software_tmp) { ?>
                    <tr>
                        <td class="link-text">
                            <?php
                            if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                echo $this->Html->link(
                                    $software[$software_tmp['GarageSoftware']['software_id']],
                                    array(
                                        'controller' => 'garages_software',
                                        'action' => 'edit',
                                        $software_tmp['GarageSoftware']['id']
                                    ),
                                    array(
                                        'class' => 'c-primary',
                                    )
                                );
                            } else {
                                echo h($software[$software_tmp['GarageSoftware']['software_id']]);
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['GarageSoftware']['software_type_id'] ? h($software_types[$software_tmp['GarageSoftware']['software_type_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['GarageSoftware']['supplier_id'] ? h($suppliers[$software_tmp['GarageSoftware']['supplier_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['GarageSoftware']['software_manufacture_id'] ? h($software_manufactures[$software_tmp['GarageSoftware']['software_manufacture_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['GarageSoftware']['billing_schedule_id'] ? h($billings_schedules[$software_tmp['GarageSoftware']['billing_schedule_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo h($software_tmp['GarageSoftware']['version']); ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['GarageSoftware']['amount']; ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['GarageSoftware']['member_pay']; ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['GarageSoftware']['garage_pay']; ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['GarageSoftware']['number_subscription']; ?>
                        </td>
                        <?php if ($config[ConstantsConfig::SOFTWARE_USER_GARAGE]) { ?>
                            <td>
                                <?php echo h($software_tmp['GarageSoftware']['username']); ?>
                            </td>
                        <?php
                        }
                        if ($config[ConstantsConfig::SOFTWARE_PASSWORD_GARAGE]) {
                        ?>
                            <td>
                                <?php echo h($software_tmp['GarageSoftware']['password']); ?>
                            </td>
                        <?php } ?>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($software_tmp['GarageSoftware']['start_date'])); ?>
                        </td>
                        <td>
                            <?php echo h($software_tmp['GarageSoftware']['end_date']); ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['GarageSoftware']['online_ordering'] == 0 ? 'No' : 'Yes'; ?>
                        </td>
                        <td>
                            <?php echo $software_tmp['GarageSoftware']['billed_by_aag'] == 0 ? 'No' : 'Yes'; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
    if (
        !$this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE) &&
        $this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)
    ) {
    ?>
        <div class="row p-top-1">
            <?php echo $this->Form->create(); ?>
            <div class="aag-subtitle">
                <?php echo __t('RequestedChanges.Describe_change'); ?>
            </div>
            <div class="ta-right cnt-buttons-v2">
                <?php echo $this->Form->button(
                    __t('RequestedChanges.Request_changes'),
                    array(
                        'type' => 'submit',
                        'name' => 'request_changes',
                        'style' => 'margin-top:0 !important;',
                        'class' => 'aag-button medium green',
                    )
                );
                ?>
            </div>
            <div class="cnt-form-inputs">
                <?php
                echo $this->Form->input(
                    'ChangeDescription',
                    array(
                        'type' => 'text',
                        'name' => 'change_description',
                        'rows' => 7,
                        'label' => false
                    )
                );
                ?>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    <?php } ?>
</div>