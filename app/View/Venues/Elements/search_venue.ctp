<?php
echo $this->Html->script('inputsValidations.js?v=' . Configure::read('VERSION_CACHE'), array('block' => 'script'));
$controller = $this->request->controller;
$action = $this->request->action;
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'venues',
            'action' => 'home'
        ),
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('Venue.Venues'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'name',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Venue.Name'),
        )
    );
    echo $this->Form->input(
        'address_1',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Venue.Address_1'),
        )
    );
    echo $this->Form->input(
        'address_2',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Venue.Address_2'),
        )
    );
    echo $this->Form->input(
        'town',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Garage.Town') . '/' . __t('Garage.City'),
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
    echo $this->Form->input(
        'post_code',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Venue.Post_code'),
        )
    );
    echo $this->Form->input(
        'telephone',
        array(
            'class' => 'clear_field phoneValidation',
            'type' => 'text',
            'required' => true,
            'label' => __t('Venue.Telephone'),
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
            'label' => __t('User.Active'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $active,
            'empty' => true,
            'id' => 'read',
            'required' => true,
        )
    );
    if (CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN) {
        echo $this->Form->input(
            'aag_region_id',
            array(
                'label' => __t('General.Region'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $aag_regions_user,
                'default' => CakeSession::read('Auth.User.aag_region_id'),
                'disabled' => true,
                'empty' => true,
            )
        );
    }
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