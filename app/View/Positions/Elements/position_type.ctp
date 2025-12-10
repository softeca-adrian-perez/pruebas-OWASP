<?php echo $this->Form->input(
    'Position.position_config_type_id',
    array(
        'label' => __t('Maintenance.Position_config_type'),
        'class' => 'select2-multiple',
        'type' => 'select',
        'multiple' => false,
        'empty' => true,
        'options' => $position_config_types,
        'id' => 'position_config_type',
        'data-url' => Router::url(array(
            'controller' => 'positions',
            'action' => 'ajax_position_group_permissions'
        ))
    )
); ?>