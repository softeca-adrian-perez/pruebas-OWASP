<?php
echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search buscador-js',
        'style' => 'margin-top: 0;',
        'type' => 'get',
        'url' => array(
            'controller' => 'appointments',
            'action' => 'maintenance_home_events'
        ),
        'id' => 'FormSearch'
    )
);
?>
<div class="cnt-form-search-title">
    <?php echo __t('Maintenance.Events'); ?>
</div>
<div class="cnt-form-inputs">
    <?php echo $this->Form->input(
        'name_' . __l(),
        array(
            'label' => false,
            'type' => 'text',
            'id' => 'search_id',
            'placeholder' => __t('Maintenance.Search_placeholder'),
            'class' => ' clear_field',
        )
    ); ?>
</div>
<div class="cnt-form-search-buttons">
    <?php
    echo $this->Form->button(
        __t('General.Search'),
        array(
            'type' => 'submit',
            'class' => 'aag-button medium',
            'id' => 'btn-search'
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