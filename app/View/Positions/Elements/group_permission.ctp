<?php echo $this->Form->input(
    'FormInput',
    array(
        'label' => __t('Config.Group_permissions'),
        'class' => 'select2-multiple clear_field',
        'type' => 'select',
        'multiple' => false,
        'empty' => true,
        'options' => $group_permissions,
        'id' => 'permissions'
    )
); ?>