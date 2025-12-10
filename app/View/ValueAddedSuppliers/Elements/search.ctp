<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'value_added_suppliers',
            'action' => 'management_home',
        ),
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('ValueAddedSuppliers.ValueAddedSuppliers'); ?>
</div>
<div class="cnt-form-inputs">
    <?php
    echo $this->Form->input(
        'title',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('ValueAddedSuppliers.Title'),
        )
    );
    echo $this->Form->input(
        'supplier_url',
        array(
            'class' => 'clear_field',
            'type' => 'text',
            'required' => true,
            'label' => __t('ValueAddedSuppliers.Url'),
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