<?php
$config = CakeSession::read('Auth.User.Config');
echo $this->Html->script('customer_activities.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('vehicle_brands_specialist.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('value_add_suppliers_table.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
echo $this->Form->create(
    'Garage',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);

echo $this->Form->hidden(
    'Garage.id',
    array(
        'id' => 'garage_id',
        'value' => $garage_id
    )
);
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
            __t('Garage.Services'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
            <button type="button" id="edit-btn-disable" value="1" class="aag-button medium" data-url="<?php echo Router::url(array('controller' => 'garages', 'action' => 'ajax_update_edit')); ?>" data-edit_enabled="<?php echo CakeSession::read('Auth.User.edit_enabled'); ?>"><?php echo __t('General.Edit'); ?></button>
        <?php } ?>
        <div class="f-right btn-hide" hidden>
            <?php
            if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) {
                echo $this->element(
                    'Comun/form_actions_garage',
                    $cancel_action
                );
            } elseif ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::REQUEST_CHANGE_GARAGE)) {
                echo $this->Form->button(
                    __t('RequestedChanges.Request_changes'),
                    array(
                        'type' => 'submit',
                        'name' => 'request_changes',
                        'style' => 'margin-top:0 !important;',
                        'class' => 'aag-button medium',
                    )
                );
            }
            ?>
        </div>
    </div>
</div>
<?php echo $this->element('../Garages/tabs', array('selected' => 'activities_and_services_garage')); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Garage.Customer_activities'); ?>
    </div>
    <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
        <div class="row btn-hide" hidden>
            <div class="cnt-buttons-v2 d-inline f-right">
                <?php echo $this->Html->link(
                    __t('Garage.New_customer_activity'),
                    array(),
                    array(
                        'escape' => false,
                        'class' => 'aag-button small green m-left-1',
                        'title' => __t('Garage.New_customer_activity'),
                        'id' => 'btn_add_activity',
                        'data-url' => Router::url(array(
                            'controller' => 'garages_customers_activities',
                            'action' => 'ajax_add_activities_garage',
                        )),
                    )
                ); ?>
            </div>
            <div class="d-inline-block f-right" style="width:225px;">
                <?php
                echo $this->Form->input(
                    'CusomterActivityName',
                    array(
                        'label' => false,
                        'type' => 'select',
                        'class' => 'select2-multiple',
                        'required' => true,
                        'id' => 'customer_activity_name',
                        'options' => $customers_activities
                    )
                );
                ?>
            </div>
        </div>
    <?php } ?>
    <div class="o-auto">
        <table id="visit-table" class="table-tracking">
            <thead>
                <tr>
                    <th><?php echo __t('General.Order'); ?></th>
                    <th><?php echo __t('General.Name'); ?></th>
                    <th class="ta-center"><?php echo __t('General.Actions'); ?></th>
                </tr>
            </thead>
            <tbody id="sort-customer-activities" data-url-sort="<?php echo Router::url(array('controller' => 'garages_customers_activities', 'action' => 'ajax_set_order')) ?>" data-url-delete="<?php echo Router::url(array('controller' => 'garages_customers_activities', 'action' => 'ajax_delete_garage_customer_activity')) ?>" data-page="<?php echo ConstantsPagination::FIRST_PAGE; ?>">
                <?php echo $this->element('../GaragesCustomersActivities/Elements/results_table'); ?>
            </tbody>
        </table>
    </div>
    <hr>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'facilities',
            array(
                'label' => __t('Garage.Facilities'),
                'type' => 'select',
                'options' => $facilities,
                'class' => 'select2-multiple input-disabled',
                'required' => true,
                'multiple' => true,
                'disabled' => true,
            )
        );
        ?>
    </div>
    <hr>
    <div class="row">
        <div class="aag-subtitle">
            <?php echo __t('Garage.Suppliers'); ?>
        </div>
        <div class="row btn-hide" hidden>
            <?php if ($this->Acceso->haveNetworkRegionPermission(ConstantsPermissionsGrouping::EDIT_GARAGE)) { ?>
                <div class="cnt-buttons-v2 d-inline f-right">
                    <?php
                    echo $this->Html->link(
                        __t('Garage.New_services'),
                        array(
                            'controller' => 'garages_services',
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
    </div>
    <div class="o-auto">
        <table class="table-tracking tabla-responsive" id="tabla-value-add-supplier">
            <thead>
                <tr>
                    <th><?php echo __t('Garage.Value_add_suppliers') ?></th>
                    <th><?php echo __t('Garage.Value_and_supplier_type') ?></th>
                    <th><?php echo __t('Garage.From_date') ?></th>
                    <th><?php echo __t('Garage.To_date') ?></th>
                    <th class="ta-center btn-hide" hidden><?php echo __t('General.Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($garage_value_supplier as $key => $value) { ?>
                    <tr>
                        <td class="link-text">
                            <?php echo $this->Html->link(
                                '<span class="c-primary">' . h($value['ValueAddSupplier']['name_en']) . '</span>',
                                array(
                                    'controller' => 'garages_services',
                                    'action' => 'edit',
                                    $value['GarageValueAddSupplier']['id'],
                                ),
                                array(
                                    'escape' => false,
                                )
                            );
                            ?>
                        </td>
                        <td><?php echo $value['ValueAddSupplierType']['name_en'] ?></td>
                        <td><?php echo Fecha::toFormatoVistaFecha($value['GarageValueAddSupplier']['from_date']) ?></td>
                        <td><?php echo Fecha::toFormatoVistaFecha($value['GarageValueAddSupplier']['to_date']) ?></td>
                        <td class="ta-center btn-hide" hidden>
                            <span class="aag-icon-papelera c-fallo delete-value" style="cursor: pointer;" data-delete-url="<?php echo Router::url(array('controller' => 'garages_values_add_suppliers', 'action' => 'ajax_delete', $value['GarageValueAddSupplier']['id'])); ?>"></span>
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
            <div class="columns medium-7">
                <div class="aag-subtitle">
                    <?php echo __t('RequestedChanges.Describe_change'); ?>
                </div>
            </div>
            <div class="medium-5 columns ta-right cnt-buttons-v2">
                <?php echo $this->Form->button(
                    __t('RequestedChanges.Request_changes'),
                    array(
                        'type' => 'submit',
                        'name' => 'request_changes',
                        'style' => 'margin-top:0 !important;',
                        'class' => 'aag-button medium',
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
    <hr>
    <div class="row">
        <div class="aag-subtitle">
            <?php echo __t('Garage.Services'); ?>
        </div>
        <div class="cnt-form-inputs cont-services w-100p">
            <?php foreach ($services as $key => $service) { ?>
                <label class="pointer-disabled m-0-i" style="pointer-events: none; cursor: not-allowed">
                    <?php
                    $valor = false;
                    if (isset($garage_services) && in_array($service['Service']['id'], $garage_services)) {
                        $valor = true;
                    }
                    echo $this->Form->input(
                        'Garage.Service.' . $service['Service']['id'],
                        array(
                            'type' => 'checkbox',
                            'label' => false,
                            'div' => false,
                            'value' => $service['Service']['id'],
                            'checked' => $valor
                        )
                    );
                    ?>
                    <span class="unselectable"><?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . $service['Service']['url'])) . " " . $service['Service']['name_' . __l()]; ?></span>
                </label>
            <?php } ?>
        </div>
        <div class="aag-subtitle">
            <?php echo __t('Garage.Vehicle_brands_specialist') ?>
        </div>
        <div class="columns btn-hide p-bottom-1" hidden>
            <?php
            echo $this->Form->button(
                __t('Garage.Mark_all_vehicles'),
                array(
                    'class' => 'aag-button medium',
                    'id' => 'mark_all_vehicles'
                )
            );
            echo $this->Form->button(
                __t('Garage.Unmark_all_vehicles'),
                array(
                    'class' => 'aag-button medium',
                    'id' => 'unmark_all_vehicles'
                )
            );
            ?>
        </div>
        <div class="cont-vehicles cnt-vehicles-brands">
            <?php foreach ($vehicles as $key => $vehicle) { ?>
                <div>
                    <label class="vehicle_specialist_service pointer-disabled" style="pointer-events: none; cursor: not-allowed">
                        <?php
                        $valor_vehicle = false;
                        $valor_specialist = false;
                        foreach ($vehicles_and_specialists as $vehicle_and_specialist) {
                            if ($vehicle_and_specialist['GarageVehicle']['vehicle_id'] == $vehicle['Vehicle']['id']) {
                                $valor_vehicle = true;
                            }
                            if ($vehicle_and_specialist['GarageSpecialistMake']['vehicle_id'] == $vehicle['Vehicle']['id']) {
                                $valor_specialist = true;
                            }
                        }
                        echo $this->Form->input(
                            'VehicleSpecialist.',
                            array(
                                'type' => 'checkbox',
                                'label' => false,
                                'div' => false,
                                'value' => $vehicle['Vehicle']['id'],
                                'checked' => $valor_specialist,
                                'class' => 'garage_vehicle ',
                                'id' => 'vehicle_specialist_' . $key
                            )
                        );
                        ?>
                        <span class="ion-ios-star unselectable"></span>
                    </label>
                    <label class="vehicle_service pointer-disabled" style="pointer-events: none; cursor: not-allowed">
                        <?php
                        echo $this->Form->input(
                            'Vehicle.',
                            array(
                                'type' => 'checkbox',
                                'label' => false,
                                'div' => false,
                                'value' => $vehicle['Vehicle']['id'],
                                'checked' => $valor_vehicle,
                                'class' => 'garage_vehicle',
                                'id' => 'vehicle_' . $key
                            )
                        ); ?>
                        <span class="unselectable"><?php echo $vehicle['Vehicle']['name_' . __l()]; ?></span>
                    </label>
                </div>
            <?php } ?>
        </div>
        <div class="aag-subtitle">
            <?php echo __t('Garage.Vehicle_types'); ?>
        </div>
        <div class="cnt-form-inputs cont-services w-100p">
            <?php foreach ($vehicletypes as $vehicletype) { ?>
                <label class="pointer-disabled m-0-i" style="pointer-events: none; cursor: not-allowed">
                    <?php
                    $valor = false;
                    if (isset($garage_vehicle_types) && in_array($vehicletype['VehicleType']['id'], $garage_vehicle_types)) {
                        $valor = true;
                    }
                    echo $this->Form->input(
                        'Garage.VehicleType.' . $vehicletype['VehicleType']['id'],
                        array(
                            'type' => 'checkbox',
                            'label' => false,
                            'div' => false,
                            'value' => $vehicletype['VehicleType']['id'],
                            'checked' => $valor
                        )
                    );
                    ?>
                    <span class="unselectable"><?php echo $this->Html->image(FileManager::get_url(FilePaths::ICONS_IMAGES_RELATIVE . $vehicletype['VehicleType']['url'])) . " " . $vehicletype['VehicleType']['name_' . __l()]; ?></span>
                </label>
            <?php } ?>
        </div>
        <?php if ($config[ConstantsConfig::PARTS_BRANDS] && isset($parts_brands)) { ?>
            <div class="columns p-0 p-top-1">
                <div class="titulo2">
                    <?php echo __t('Garage.Parts_brands_allowed'); ?>
                </div>
            </div>
            <div class="d-inline-block cont-vehicles w-100p">
                <?php foreach ($parts_brands as $key => $part_brand) { ?>
                    <div class="small-6 medium-2 columns end">
                        <?php
                        $valor = false;
                        foreach ($garages_parts_brands as $garage_part_brand) {
                            if ($garage_part_brand['GarageBrand']['brand_id'] == $part_brand['Brand']['id']) {
                                $valor = true;
                            }
                        }
                        ?>
                        <label class="garage_part_brand pointer-disabled" style="pointer-events: none; cursor: not-allowed">
                            <?php
                            echo $this->Form->input(
                                'Garage.Brand.' . $part_brand['Brand']['id'],
                                array(
                                    'type' => 'checkbox',
                                    'label' => false,
                                    'div' => false,
                                    'value' => $part_brand['Brand']['id'],
                                    'checked' => $valor,
                                    'class' => 'garage_part_brand',
                                    'id' => 'part_brand_' . $key
                                )
                            ); ?>
                            <span class="unselectable"><?php echo $part_brand['Brand']['name']; ?></span>
                        </label>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<style>
    div.cnt-label-star.new-cnt-label-star {
        display: grid;
        gap: 5px;
        grid-template-columns: min-content 1fr;
    }

    div.cnt-label-star.new-cnt-label-star>label {
        position: static;
    }

    .cnt-vehicles-brands {
        clear: both;
        margin: 0 10px;
        column-count: 6;
        column-gap: 5px;
    }

    @media(max-width: 1500px) {
        .cnt-vehicles-brands {
            column-count: 5;
        }
    }

    @media(max-width: 1100px) {
        .cnt-vehicles-brands {
            column-count: 4;
        }
    }

    @media(max-width: 850px) {
        .cnt-vehicles-brands {
            column-count: 3;
        }
    }

    @media(max-width: 600px) {
        .cnt-vehicles-brands {
            column-count: 2;
        }
    }

    @media(max-width: 400px) {
        .cnt-vehicles-brands {
            column-count: 1;
        }
    }

    .cnt-vehicles-brands>div {
        gap: 5px;
        width: 100%;
        padding: 3px 5px;
        border-radius: 3px;
        margin-bottom: 5px;
        white-space: nowrap;
        display: inline-flex;
        border: 1px solid #eaeaea;
    }

    .cnt-vehicles-brands>div>label {
        margin: 0;
    }

    .cnt-vehicles-brands>div>label:first-child {
        width: max-content;
    }
</style>