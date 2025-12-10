<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'brands',
            'action' => 'maintenance_brands',
        ),
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('Brands.Brands'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'name',
        array(
            'type' => 'text',
            'class' => 'clear_field',
            'required' => true,
            'label' => __t('Brands.Name'),
        )
    );
    echo $this->Form->input(
        'supplier',
        array(
            'label' => __t('Suppliers.Supplier'),
            'class' => 'select2-multiple clear_field',
            'type' => 'select',
            'options' => $suppliers,
            'empty' => true,
        )
    );
    echo $this->Form->input(
        'active',
        array(
            'label' => __t('Brands.Active'),
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
                'class' => 'select2-multiple',
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