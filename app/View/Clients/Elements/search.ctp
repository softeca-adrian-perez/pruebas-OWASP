<?php
$config = CakeSession::read('Auth.User.Config');
$user = $this->Acceso->user();
echo $this->Html->script('clients.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'clients',
            'action' => 'home'
        )
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('General.Search'); ?>
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
                    'controller' => 'clients',
                    'action' => 'ajax_search_home',
                    ConstantsTypeSearch::CLIENT
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
            'id' => 'trading-group-id'
        )
    );
    echo $this->Form->input(
        'city_id',
        array(
            'label' => __t('Garage.City'),
            'type' => 'select',
            'class' => 'dynamicSelect2_cities_clients clear_field',
            'multiple' => true,
            'empty' => true,
            'id' => 'city-id-clients',
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
            'multiple' => true,
            'id' => 'status-id'
        )
    );

    //If it's Benelux there is not garage g_number, but if the role is Super Admin, garages from both regions can be present at the same time
    if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) {
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
    echo $this->Form->input(
        'bdm_id',
        array(
            'label' => __t('Distributor.BDM'),
            'type' => 'select',
            'class' => 'clear_field dynamicSelect2_contacts_garages_bdm update_contacts_garages_bdm',
            'options' => isset($bdm) ? $bdm : array(),
            'empty' => true,
            'multiple' => false,
            'id' => 'bdm-id'
        )
    );
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
            'id' => 'service-id'
        )
    );

    //Distributors dynamic filter
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
    <label class="center-check p-top-1">
        <?php echo __t('Visit.My_customers'); ?>
        <div class="aag-switch round small">
            <?php echo $this->Form->input('search_my_customers', array('label' => false, 'div' => false, 'type' => 'checkbox', 'required' => true, 'id' => 'my_customers')); ?>
            <label for="my_customers"></label>
        </div>
    </label>
</div>
<div class="ampliar-js">
    <div class="cnt-form-inputs ampliacion-js">
        <?php
        echo $this->Form->input(
            'town',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'id' => 'town-id',
                'required' => true,
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
                'id' => 'vehicle-type-id'
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