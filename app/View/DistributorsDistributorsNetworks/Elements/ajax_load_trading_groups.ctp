<?php
echo $this->Form->input(
    $trading_group_field_name,
    array(
        'label' => __t('Garage.Trading_group'),
        'class' => 'select2-multiple',
        'type' => 'select',
        'disabled' => false,
        'multiple' => false,
        'empty' => false,
        'options' => $trading_groups,
        'id' => $trading_group_field_name,
    )
);
