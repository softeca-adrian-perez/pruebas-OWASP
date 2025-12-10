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
            __t('Garage.Value_add'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
            <button id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit',)); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php } ?>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'values_adds',)); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Garage.Value_add'); ?>
    </div>
    <div class="btn-hide" hidden>
        <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
            <div class="cnt-buttons-v2 d-inline f-right">
                <?php echo $this->Html->link(
                    __t('Garage.New_value_add'),
                    array(
                        'controller' => 'garages_values_adds',
                        'action' => 'add',
                        $garage_id
                    ),
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green',
                    )
                ); ?>
            </div>
        <?php } ?>
    </div>
    <div class="o-auto">
        <table class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('Garage.Value_add'); ?></th>
                    <th><?php echo __t('General.Billing_schedule'); ?></th>
                    <th><?php echo $this->Paginator->sort('GarageValueAdd.version', __t('Garage.Version')); ?></th>
                    <th>
                        <?php
                        $msg = isset($country['Country']['symbol']) ? __t('General.Amount') . ' ' . $country['Country']['symbol'] : __t('General.Amount');
                        echo $msg;
                        ?>
                    </th>
                    <th><?php echo $this->Paginator->sort('GarageValueAdd.member_pay', __t('General.Member_pay')); ?></th>
                    <th><?php echo $this->Paginator->sort('GarageValueAdd.garage_pay', __t('General.Garage_pay')); ?></th>
                    <th><?php echo $this->Paginator->sort('GarageValueAdd.number_subscription', __t('General.Number_subscription')); ?></th>
                    <th><?php echo $this->Paginator->sort('GarageValueAdd.start_date', __t('Garage.Start_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('GarageValueAdd.end_date', __t('Garage.End_date')); ?></th>
                    <th><?php echo $this->Paginator->sort('GarageValueAdd.online_ordering', __t('General.Online_ordering')); ?></th>
                    <th><?php echo $this->Paginator->sort('GarageValueAdd.billed_by_aag', __t('General.Billed_by_aag')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garages_values_adds as $garage_value_add) { ?>
                    <tr>
                        <td class="link-text">
                            <?php
                            if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                                echo $this->Html->link(
                                    $value_add[$garage_value_add['GarageValueAdd']['value_add_id']],
                                    array(
                                        'controller' => 'garages_values_adds',
                                        'action' => 'edit',
                                        $garage_value_add['GarageValueAdd']['id']
                                    ),
                                    array(
                                        'class' => 'c-primary',
                                    )
                                );
                            } else {
                                echo h($value_add[$garage_value_add['GarageValueAdd']['value_add_id']]);
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo $garage_value_add['GarageValueAdd']['billing_schedule_id'] ? h($billings_schedules[$garage_value_add['GarageValueAdd']['billing_schedule_id']]) : ''; ?>
                        </td>
                        <td>
                            <?php echo h($garage_value_add['GarageValueAdd']['version']); ?>
                        </td>
                        <td>
                            <?php echo $garage_value_add['GarageValueAdd']['amount']; ?>
                        </td>
                        <td>
                            <?php echo $garage_value_add['GarageValueAdd']['member_pay']; ?>
                        </td>
                        <td>
                            <?php echo $garage_value_add['GarageValueAdd']['garage_pay']; ?>
                        </td>
                        <td>
                            <?php echo $garage_value_add['GarageValueAdd']['number_subscription']; ?>
                        </td>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($garage_value_add['GarageValueAdd']['start_date'])); ?>
                        </td>
                        <td>
                            <?php echo h(Fecha::toFormatoVista($garage_value_add['GarageValueAdd']['end_date'])); ?>
                        </td>
                        <td>
                            <?php echo $garage_value_add['GarageValueAdd']['online_ordering'] == 0 ? 'No' : 'Yes'; ?>
                        </td>
                        <td>
                            <?php echo $garage_value_add['GarageValueAdd']['billed_by_aag'] == 0 ? 'No' : 'Yes'; ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('Comun/paginacion'); ?>
</div>