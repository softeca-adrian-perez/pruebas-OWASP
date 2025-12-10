<?php echo $this->Form->create(
    'Search',
    array(
        'class' => 'cnt-form-search',
        'type' => 'get',
        'url' => array(
            'controller' => 'permissions',
            'action' => 'home'
        )
    )
);
    ?>
    <div class="cnt-form-search-title">
        <?php echo __t('Maintenance.Permissions');?>
    </div>
    <div class="cnt-form-inputs">
        <?php
        echo $this->Form->input(
            'position_config_type_id',
            array(
                'label' => __t('Maintenance.Position_config_type'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $positions_config_types,
                'empty' => true,
                'required' => true
            )
        );
        echo $this->Form->input(
            'group_permission_id',
            array(
                'label' => __t('Config.Group_permissions'),
                'type' => 'select',
                'class' => 'select2-multiple clear_field',
                'options' => $group_permissions,
                'empty' => true,
                'required' => true
            )
        );
        echo $this->Form->input(
            'name',
            array(
                'class' => 'clear_field',
                'type' => 'text',
                'empty' => true,
                'required' => true,
                'label' => __t('User.Permission'),
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