<?php
echo $this->Html->script('clients_distributors.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
echo $this->Html->script('dynamic_lists.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$user = $this->Acceso->user();
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'clients',
            'action' => 'home_distributors'
        )
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('Distributor.Distributors'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'name',
        array(
            'label' => false,
            'type' => 'text',
            'id' => 'name-distributors',
            'class' => 'search_ajax clear_field',
            'data-url' => Router::url(
                array(
                    'controller' => 'clients',
                    'action' => 'ajax_search_distributors',
                    ConstantsTypeSearch::DISTRIBUTOR_CLIENT
                )
            ),
            'required' => true,
            'label' => __t('Appointment.Customer'),
        )
    );
    echo $this->Form->input(
        'account_number',
        array(
            'type' => 'text',
            'id' => 'account-number',
            'class' => 'clear_field',
            'required' => true,
            'label' => __t('Distributor.Account_number'),
        )
    );
    echo $this->Form->input(
        'VAT_number',
        array(
            'type' => 'text',
            'class' => 'clear_field',
            'id' => 'vat-number',
            'required' => true,
            'label' => __t('Distributor.VAT_number'),
        )
    );
    echo $this->Form->input(
        'trading_group_id',
        array(
            'label' => __t('Distributor.Trading_groups'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $trading_groups,
            'empty' => true,
            'id' => 'trading-groups',
            'multiple' => true,
        )
    );
    echo $this->Form->input(
        'association_type_id',
        array(
            'label' => __t('Distributor.Association'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $associations_types,
            'empty' => true,
            'id' => 'associations'
        )
    );
    echo $this->Form->input(
        'town',
        array(
            'label' => false,
            'type' => 'text',
            'class' => 'clear_field',
            'id' => 'town',
            'required' => true,
            'label' => __t('Distributor.Town'),
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
    //if ($user['role_id'] != ConstantsRoles::BDM_AAG && $user['role_id'] != ConstantsRoles::BDM_TG) {
    echo $this->Form->input(
        'bdm_id',
        array(
            'label' => __t('Distributor.BDM'),
            'type' => 'select',
            'class' => 'clear_field dynamicSelect2_contacts_distributors_bdm',
            'empty' => true,
            'multiple' => false,
            'id' => 'bdm-id',
            'options' => isset($bdm) ? $bdm : array(),
        )
    );
    //}
    echo $this->Form->input(
        'head_office',
        array(
            'label' => __t('Distributor.Profile'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $profile,
            'empty' => true,
            'id' => 'profile'
        )
    );
    echo $this->Form->input(
        'status',
        array(
            'label' => __t('General.Status'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $distributors_statuses,
            'empty' => true,
            'id' => 'status'
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
<div class="cnt-form-search-buttons">
    <?php
    echo $this->Form->button(
        __t('General.Search'),
        array(
            'type' => 'submit',
            'class' => 'aag-button medium'
        )
    );
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
<?php echo $this->Form->end(); ?>