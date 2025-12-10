<?php
echo $this->Form->create(
    'Buscador',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'groups_permissions',
            'action' => 'home'
        ),
    )
);
    ?>
    <div class="cnt-form-search-title">
        <?php echo __t('General.Group_of_permissions'); ?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'name',
            array(
                'type' => 'text',
                'class' => 'clear_field',
                'label' => __t('User.Name'),
            )
        );
        echo $this->Form->input(
            'position_config_type_id',
            array(
                'label' => __t('Garage.Type'),
                'class' => 'select2-multiple clear_field',
                'type' => 'select',
                'empty' => true,
                'multiple' => false,
                'options' => $config_types,
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