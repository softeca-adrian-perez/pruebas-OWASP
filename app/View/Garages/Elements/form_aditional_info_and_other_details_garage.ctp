<?php
echo $this->Html->script('enable_edit_garage.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$config = CakeSession::read('Auth.User.Config');
$action = in_array($this->Acceso->rol(), array(ConstantsRoles::GARAGE, ConstantsRoles::DISTRIBUTOR)) ? 'my_data' : 'view';
echo $this->Form->create(
    'Garage',
    array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('Garage.id');
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
            __t('Garage.Aditional_info'),
        ));
        ?>
    </div>
    <div>
        <a class="aag-button medium two go-back-js"><?php echo __t('General.Back') ?></a>
        <?php
        if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN && $garage['Garage']['status'] != ConstantsGarageStatus::INACTIVE) { ?>
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
<?php echo $this->element('../Garages/tabs', array('selected' => 'aditional_ingo_garage')); ?>
<div class="cnt-data aag-padding">
    <div class="aag-title-background">
        <?php echo h($garage['Garage']['name']); ?>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Garage.Aditional_info'); ?>
    </div>
    <div class="aag-subtitle m-top-1">
        <?php echo __t('Garage.B2B'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'fleet_mot',
            array(
                'type' => 'number',
                'label' => isset($country['Country']['symbol']) ? __t('Garage.Fleet_mot') . ' ' . $country['Country']['symbol'] : __t('Garage.Fleet_mot'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'fleet_labour_rate',
            array(
                'type' => 'number',
                'label' => isset($country['Country']['symbol']) ? __t('Garage.Fleet_labour_rate') . ' ' . $country['Country']['symbol'] : __t('Garage.Fleet_labour_rate'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'long_life_oil_price_b2b',
            array(
                'type' => 'number',
                'label' => isset($country['Country']['symbol']) ? __t('Garage.Long_life_oil_price_b2b') . ' ' . $country['Country']['symbol'] : __t('Garage.Long_life_oil_price_b2b'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'standard_oil_price_b2b',
            array(
                'type' => 'number',
                'label' => isset($country['Country']['symbol']) ? __t('Garage.Standard_oil_price_b2b') . ' ' . $country['Country']['symbol'] : __t('Garage.Standard_oil_price_b2b'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'postcode_b2b',
            array(
                'label' => __t('Garage.Postcode'),
                'type' => 'select',
                'options' => $postcodes,
                'class' => 'select2-multiple input-disabled',
                'required' => true,
                'multiple' => true,
                'disabled' => true,
            )
        );
        ?>
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <?php
            echo $this->Form->input(
                'fleet_work_direction',
                array(
                    'label' => __t('Garage.Fleet_work_direction'),
                    'type' => 'checkbox',
                    'class' => 'input-disabled',
                    'disabled' => true,
                )
            );
            ?>
        </div>
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <?php
            echo $this->Form->input(
                'collection_delivery_b2b',
                array(
                    'label' => __t('Garage.Collection_delivery_b2b'),
                    'type' => 'checkbox',
                    'class' => 'input-disabled',
                    'disabled' => true,
                )
            );
            ?>
        </div>
    </div>
    <div class="aag-subtitle p-top-1">
        <?php echo __t('Garage.B2C'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'retail_mot',
            array(
                'type' => 'number',
                'label' => isset($country['Country']['symbol']) ? __t('Garage.Retail_mot') . ' ' . $country['Country']['symbol'] : __t('Garage.Retail_mot'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'retail_labour_rate',
            array(
                'type' => 'number',
                'label' => isset($country['Country']['symbol']) ? __t('Garage.Retail_labour_rate') . ' ' . $country['Country']['symbol'] : __t('Garage.Retail_labour_rate'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'long_life_oil_price_b2c',
            array(
                'type' => 'number',
                'label' => isset($country['Country']['symbol']) ? __t('Garage.Long_life_oil_price_b2c') . ' ' . $country['Country']['symbol'] : __t('Garage.Long_life_oil_price_b2c'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'standard_oil_price_b2c',
            array(
                'type' => 'number',
                'label' => isset($country['Country']['symbol']) ? __t('Garage.Standard_oil_price_b2c') . ' ' . $country['Country']['symbol'] : __t('Garage.Standard_oil_price_b2c'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'postcode_b2c',
            array(
                'label' => __t('Garage.Postcode'),
                'type' => 'select',
                'class' => 'select2-multiple input-disabled',
                'multiple' => true,
                'options' => $postcodes,
                'required' => true,
                'disabled' => true,
            )
        );
        ?>
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <?php
            echo $this->Form->input(
                'collection_delivery_b2c',
                array(
                    'label' => __t('Garage.Collection_delivery_b2c'),
                    'type' => 'checkbox',
                    'class' => 'input-disabled',
                    'disabled' => true,
                )
            );
            ?>
        </div>
    </div>
    <div class="aag-subtitle p-top-1">
        <?php echo __t('Garage.EV'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'ev_charge_points',
            array(
                'type' => 'number',
                'label' => __t('Garage.Ev_charge_points'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'kwh_charging_retail_price',
            array(
                'type' => 'number',
                'label' => __t('Garage.Kwh_charging_retail_price'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'kwh_charging_fleet_price',
            array(
                'type' => 'number',
                'label' => __t('Garage.Kwh_charging_fleet_price'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'ev_ppe_audited_date',
            array(
                'class' => 'fecha-js from-js input-disabled',
                'type' => 'text',
                'div' => array(
                    'class' => 'datepicker datepicker-label-block',
                ),
                'label' => __t('Garage.Ev_ppe_audited_date'),
                'disabled' => true,
            )
        );
        ?>
    </div>
    <div class="aag-subtitle p-top-1">
        <?php echo __t('Garage.Miscellaneous'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'ramps',
            array(
                'type' => 'number',
                'required' => false,
                'label' => __t('Garage.Number_of_ramps'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        echo $this->Form->input(
            'technician',
            array(
                'type' => 'number',
                'required' => false,
                'label' => __t('Garage.Number_of_technicians'),
                'disabled' => true,
                'class' => 'input-disabled',
                'min' => 0
            )
        );
        if ($config[ConstantsConfig::MOT]) {
            echo $this->Form->input(
                'MOT_bays',
                array(
                    'type' => 'number',
                    'required' => false,
                    'label' => __t('Garage.Number_of_MOT_bays'),
                    'disabled' => true,
                    'class' => 'input-disabled',
                    'min' => 0
                )
            );
        }
        ?>
        <div class="cnt-check-visit cnt-form-inputs-max-width">
            <?php
            echo $this->Form->input(
                'courtesy_car',
                array(
                    'label' => __t('Garage.Courtesy_car'),
                    'type' => 'checkbox',
                    'class' => 'input-disabled',
                    'disabled' => true,
                )
            ); ?>
        </div>
        <?php if ($config[ConstantsConfig::SPEND]) { ?>
            <div class="aag-subtitle">
                <?php echo __t('Garage.Spend'); ?>
            </div>
            <div class="cnt-form-inputs">
                <?php
                echo $this->Form->input(
                    'spend_this_month',
                    array(
                        'label' => __t('Garage.This_month'),
                        'type' => 'text',
                        'readonly' => true,
                        'disabled' => true,
                        'class' => 'input-disabled',
                    )
                );
                echo $this->Form->input(
                    'spend_last_month',
                    array(
                        'label' => __t('Garage.Last_month'),
                        'type' => 'text',
                        'readonly' => true,
                        'disabled' => true,
                        'class' => 'input-disabled',
                    )
                );
                echo $this->Form->input(
                    'spend_12_month',
                    array(
                        'label' => __t('Garage.Last_12_month'),
                        'type' => 'text',
                        'readonly' => true,
                        'disabled' => true,
                        'class' => 'input-disabled',
                    )
                );
                echo $this->Form->input(
                    'spend_projected',
                    array(
                        'label' => __t('Garage.Projected'),
                        'type' => 'text',
                        'readonly' => true,
                        'disabled' => true,
                        'class' => 'input-disabled',
                    )
                );
                ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>