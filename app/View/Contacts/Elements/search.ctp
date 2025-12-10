<?php
$controller = $this->request->controller;
$action = $this->request->action;

echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'contacts',
            'action' => 'home',

        ),
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('Contact.Contacts'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'first_name',
        array(
            'label' => false,
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Contact.First_name'),
        )
    );
    echo $this->Form->input(
        'last_name',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Contact.Last_name'),
        )
    );
    echo $this->Form->input(
        'position_id',
        array(
            'label' => __t('Contact.Position'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $opt_positions,
            'empty' => true,
            'id' => 'associations'
        )
    );
    echo $this->Form->input(
        'logistic_center_id',
        array(
            'label' => __t('Contact.Logistic_center'),
            'type' => 'select',
            'class' => 'select2-multiple clear_field',
            'options' => $logistic_centers,
            'empty' => true,
            'id' => 'logistic_center'
        )
    );
    echo $this->Form->input(
        'email',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Contact.Email'),
        )
    );
    echo $this->Form->input(
        'phone',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Contact.Phone'),
        )
    );
    echo $this->Form->input(
        'mobile_phone',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' =>  __t('Contact.Mobile_phone'),
        )
    );

    if (CakeSession::read('Auth.User.role_id') != ConstantsRoles::SUPER_ADMIN) {
        echo $this->Form->input(
            'Contact.aag_region_id',
            array(
                'required' => true,
                'type' => 'hidden',
                'value' => CakeSession::read('Auth.User.aag_region_id'),
            )
        );
    } else {
        echo $this->Form->input(
            'Contact.aag_region_id',
            array(
                'label' => __t('Garage.Region'),
                'required' => true,
                'type' => 'select',
                'options' => $aag_regions,
                'default' => CakeSession::read('Auth.User.aag_region_id'),
                'disabled' => true,
                'empty' => true,
                'class' => 'select2-multiple',
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