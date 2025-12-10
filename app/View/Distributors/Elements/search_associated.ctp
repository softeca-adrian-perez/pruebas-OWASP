<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search m-0-i buscador-js',
        'type' => 'get',
        'url' => array(
            'controller' => 'distributors',
            'action' => 'home_associated',
            $distributor_id
        ),
    )
);

$config = CakeSession::read('Auth.User.Config');
?>

<div class="cnt-form-search-title">
    <?php echo __t('General.Search'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'name',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Garage.Name'),
        )
    ); 
    if($config[ConstantsConfig::COUNTY_COUNTRY_GARAGE]){ 
        echo $this->Form->input(
            'province_id',
            array(
                'label' => __t('Garage.County'),
                'type' => 'select',
                'empty' => true,
                'class' => 'select2-multiple clear_field',
                        'options' => $province_list,
                        'required' => true,

                    )
                ); 
    } 
    echo $this->Form->input(
        'town',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Garage.Town'),
        )
    );
    echo $this->Form->input(
        'phone',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Garage.Phone'),
        )
    );

    //If it's Benelux there is not garage g_number, but if the role is Super Admin, garages from both regions can be present at the same time
    if ((isset($user_aag_region_id) && ($user_aag_region_id != ConstantsAAGRegionId::BENELUX)) || ($user_role == ConstantsRoles::SUPER_ADMIN)) {
        echo $this->Form->input(
            'g_number_id',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'required' => true,
                'label' => __t('Garage.G_number'),
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
            'class' => 'aag-button medium',
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
