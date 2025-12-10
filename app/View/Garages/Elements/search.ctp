<?php
$config = CakeSession::read('Auth.User.Config');
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Html->script('garages.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'garages',
            'action' => 'home'
        ),
        'id' => 'search-garage'
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('Garage.Garages'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'name',
        array(
            'type' => 'text',
            'id' => 'name-garages',
            'class' => 'search_ajax clear_field',
            'data-url' => Router::url(
                array(
                    'controller' => 'garages',
                    'action' => 'ajax_search_home',
                    ConstantsTypeSearch::GARAGE
                )
            ),
            'required' => true,
            'label' => __t('Appointment.Customer'),
        )
    );
    echo $this->Form->input(
        'trading_group_id',
        array(
            'label' => __t('Garage.Trading_group'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $trading_groups,
            'empty' => true,
            'id' => 'trading-group-id',
            'multiple' => true
        )
    );
    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
        echo $this->Form->input(
            'aag_region_id',
            array(
                'label' => __t('General.Region'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $regions,
                'empty' => true,
                'default' => CakeSession::read('Auth.User.aag_region_id'),
                'id' => 'region-id',
                'disabled' => true,
            )
        );
    }
    echo $this->Form->input(
        'country_id',
        array(
            'label' => __t('Garage.Country'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $countries,
            'empty' => true,
            'id' => 'country-id',
            'multiple' => true
        )
    );
    echo $this->Form->input(
        'city_id',
        array(
            'label' => __t('Garage.City'),
            'type' => 'select',
            'class' => 'dynamicSelect2_cities clear_field',
            'multiple' => true,
            'empty' => true,
            'id' => 'city-id',
            'data-selected-cities' => isset($array_city_name) ? $array_city_name : array(),
        )
    );
    echo $this->Form->input(
        'network_id',
        array(
            'label' => __t('Garage.Network'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $networks,
            'empty' => true,
            'id' => 'network-id',
            'multiple' => true
        )
    );
    echo $this->Form->input(
        'status_id',
        array(
            'label' => __t('Garage.Network_status'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $networks_statuses,
            'empty' => true,
            'id' => 'status-id',
            'multiple' => true
        )
    );

    echo $this->Form->input(
        'annex_detail_id',
        array(
            'label' => __t('Network.Annex_detail'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $annex_detail,
            'empty' => true,
            'id' => 'annex_detail-id',
            'multiple' => true
        )
    );

    if ((isset($user['aag_region_id']) && ($user['aag_region_id'] != ConstantsAAGRegionId::BENELUX)) || ($user['role_id'] == ConstantsRoles::SUPER_ADMIN)) {
        echo $this->Form->input(
            'g_number_id',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'id' => 'g-number-id',
                'required' => true,
                'label' => __t('Garage.G_number'),
            )
        );
    }
    if (
        $user['role_id'] != ConstantsRoles::BDM_AAG && $user['role_id'] != ConstantsRoles::BDM_TG
    ) {
        echo $this->Form->input(
            'bdm_id',
            array(
                'label' => __t('Distributor.BDM'),
                'type' => 'select',
                'class' => 'dynamicSelect2_contacts_garages_bdm clear_field',
                'options' => isset($bdm) ? $bdm : array(),
                'empty' => true,
                'id' => 'bdm-id'
            )
        );
    }
    echo $this->Form->input(
        'postcode',
        array(
            'type' => 'text',
            'class' => 'clear_field',
            'id' => 'postcode-id',
            'required' => true,
            'label' => __t('Garage.Postcode'),
        )
    );
    echo $this->Form->input(
        'service_id',
        array(
            'label' => __t('Garage.Services'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $services,
            'empty' => true,
            'id' => 'service-id',
            'multiple' => true
        )
    );
    echo $this->Form->input(
        'distributor_id',
        array(
            'label' => __t('AppointmentObjective.Distributor'),
            'type' => 'select',
            'class' => 'dynamicSelect2_distributors clear_field',
            'multiple' => true,
            'empty' => true,
            'id' => 'distributor-id-js',
            'data-selected_distributors' => isset($distributors) ? $distributors : array(),
        )
    );
    echo $this->Form->input(
        'erp_id',
        array(
            'label' => __t('Garage.ERP'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $erp_providers,
            'empty' => true,
            'id' => 'erp_id-id',
            'multiple' => true
        )
    );
	echo $this->Form->input(
		'sales_area_id',
		array(
			'label' => __t('Garage.Sales_area'),
			'type' => 'select',
			'class' => 'select2-multiple clear_field',
			'options' => $sales_area,
			'empty' => true,
			'id' => 'sales-area-id',
			'multiple' => true
		)
	);
    ?>
</div>
<div class="ampliar-js">
    <div class="cnt-form-inputs ampliacion-js">
        <?php
        echo $this->Form->input(
            'town',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'id' => 'town',
                'label' => __t('Garage.Town'),
            )
        );
        echo $this->Form->input(
            'ramps',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'id' => 'ramps-id',
                'required' => true,
                'label' => __t('Garage.Ramps'),
            )
        );
        echo $this->Form->input(
            'MOT_bays',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'id' => 'MOT-bays-id',
                'required' => true,
                'label' => __t('Garage.MOT_bays'),
            )
        );
        echo $this->Form->input(
            'phone',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'id' => 'phone-id',
                'required' => true,
                'label' => __t('Garage.Phone'),
            )
        );
        echo $this->Form->input(
            'vehicle_type_id',
            array(
                'label' => __t('Garage.Vehicle_types'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $vehicles_types,
                'empty' => true,
                'id' => 'vehicle-type-id',
                'multiple' => true
            )
        );
        echo $this->Form->input(
            'foundation_year',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'id' => 'foundation-year-id',
                'required' => true,
                'label' => __t('Garage.Foundation'),
            )
        );
        echo $this->Form->input(
            'lead_source',
            array(
                'label' => __t('Garage.Lead_source'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'multiple' => false,
                'empty' => true,
                'id' => 'lead_source_id',
                'options' => $lead_sources,
                'multiple' => true
            )
        );
        echo $this->Form->input(
            'ref_code',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'id' => 'ref-code-id',
                'required' => true,
                'label' => __t('Garage.Ref_code'),
            )
        );
        echo $this->Form->input(
            'external_agreements',
            array(
                'label' => __t('Garage.External_agreements'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $external_agreements,
                'empty' => true,
                'id' => 'external-agreements-id',
                'multiple' => true
            )
        );
        echo $this->Form->input(
            'internal_agreements',
            array(
                'label' => __t('Garage.Internal_agreements'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $internal_agreements,
                'empty' => true,
                'id' => 'internal-agreements-id',
                'multiple' => true
            )
        );
        ?>
    </div>
    <div class="cnt-form-search-buttons">
        <?php
        echo $this->Form->button(
            __t('General.Search'),
            array(
                'type' => 'submit',
                'class' => 'aag-button medium'
            )
        );
        ?>
        <div class="mostrar-ampliado aag-button medium one border-four outlined">
            <?php echo __t('General.Advanced_search'); ?>
        </div>
        <?php
        echo $this->Form->button(
            "<span class='aag-icon-escoba'></span>",
            array(
                'id' => 'clear_field',
                'class' => 'aag-button medium four outlined',
                'escape' => false,
                'title' => __t('General.Clean_search')
            )
        );
        ?>
    </div>
</div>
<?php echo $this->Form->end(); ?>