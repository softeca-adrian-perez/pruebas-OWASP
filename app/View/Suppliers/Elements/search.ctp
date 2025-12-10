<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'suppliers',
            'action' => 'maintenance_suppliers',
        ),
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('Suppliers.Suppliers'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'name',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Suppliers.Name'),
        )
    );
    echo $this->Form->input(
        'web',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('Suppliers.Web'),
        )
    );
    echo $this->Form->input(
        'brand',
        array(
            'label' => __t('Brands.Brand'),
            'class' => 'select2-multiple clear_field',
            'type' => 'select',
            'options' => $brands,
            'empty' => true,
        )
    );
    echo $this->Form->input(
        'active',
        array(
            'label' => __t('Suppliers.Active'),
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