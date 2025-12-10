<?php
echo $this->Html->script(CakeSession::read('GOOGLE_MAPS_API'), array('block' => 'script'));
echo $this->Html->script('gmaps.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('inputsValidations.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$action = $this->request->action;
echo $this->Html->script('agreements.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));

echo $this->Form->create(
    'Venue',
    array(
        'id' => 'form',
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
    )
);
echo $this->Form->hidden('Venue.id'); ?>
<div class="cnt-breadcrumb">
    <div>
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Venue.Venues'),
                    array(
                        'controller' => 'venues',
                        'action' => 'home'
                    )
                ),
                __t('General.Add'),
            ));
        } else {
            echo $this->Html->breadcrumb(array(
                $this->Html->link(
                    __t('Venue.Venues'),
                    array(
                        'controller' => 'venues',
                        'action' => 'home'
                    )
                ),
                __t('General.Edit')
            ));
        }
        ?>
    </div>
    <div>
        <?php echo $this->element('Comun/form_actions', $cancel_action); ?>
    </div>
</div>
<div class="cnt-data aag-padding">
    <div class="aag-title">
        <?php
        if ($action == ConstantsActionsNames::ADD) {
            echo __t('Venue.Add_venue');
        } else {
            echo __t('Venue.Edit_venue');
        }
        ?>
    </div>
    <br />
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Venue.Name'),
            )
        );
        echo $this->Form->input(
            'address_1',
            array(
                'label' => __t('Garage.Address_1'),
                'type' => 'text',
                'required' => true,
                'id' => 'autocomplete-address',
                'placeholder' => '',
                'data-map' => 'map-event-location',
                'class' => 'input-disabled-address1',
                'data-key' => Texto::encryptDecryptText(GOOGLE_API_KEY, false),
                'data-user_aag_region_id' => $user_aag_region_id,
            )
        );
        echo $this->Form->input(
            'address_2',
            array(
                'label' => __t('Garage.Address_2'),
                'required' => false,
                'type' => 'text',
                'id' => 'autocomplete-address2',
                'required' => true,
            )
        );
        echo $this->Form->input(
            'town',
            array(
                'required' => false,
                'type' => 'text',
                'id' => 'autocomplete-town',
                'label' => __t('Garage.Town') . '/' . __t('Garage.City'),
            )
        );
        echo $this->Form->input(
            'post_code',
            array(
                'required' => false,
                'id' => 'autocomplete-postcode',
                'type' => 'text',
                'label' => __t('Garage.Postcode'),
            )
        );
        echo $this->Form->input(
            'telephone',
            array(
                'type' => 'text',
                'required' => true,
                'label' => __t('Venue.Telephone'),
                'class' => 'phoneValidation',
            )
        );
        echo $this->Form->input(
            'venue_type_id',
            array(
                'label' => __t('Venue.Venue_type'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $venues_types,
                'empty' => true,
            )
        );
        echo $this->Form->input(
            'active',
            array(
                'label' => __t('General.Active'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $active,
                'empty' => true,
                'required' => true,
            )
        );
        echo $this->Form->input(
            'aag_region_id',
            array(
                'label' => __t('General.Region'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'required' => true,
                'options' => $aag_regions,
                'empty' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? true : false,
                'disabled' => $user_role_id == ConstantsRoles::SUPER_ADMIN ? false : true,
                'value' => isset($venue) ? $venue['Venue']['aag_region_id'] : $user_aag_region_id,
                'id' => 'aag-region-select',
            )
        );
        echo $this->Form->input(
            'sales_area_id',
            array(
                'value' => $venue['Venue']['sales_area_id'] ?? null,
                'required' => false,
                'id' => 'sales_area_id-select',
                'label' => __t('Garage.Sales_area'),
                'class' => 'select2-multiple',
                'type' => 'select',
                'multiple' => false,
                'empty' => true,
                'options' => $sales_area,
            )
        );
        echo $this->Form->hidden(
            'latitude',
            array(
                'label' => false,
                'id' => 'latitude-localization',
            )
        );
        echo $this->Form->hidden(
            'longitude',
            array(
                'label' => false,
                'id' => 'longitude-localization',
            )
        );
        ?>
    </div>
    <div id="map-event-location" class="contenedor-mapa m-top-1" style="height: 400px;" data-editable="<?php echo ConstantsBooleans::YES; ?>"></div>
    <div class="alliance_bar"></div>
</div>
<?php echo $this->Form->end(); ?>