<?php echo $this->Form->input(
    'route',
    array(
        'label' => __t('Visit.Route'),
        'type' => 'select',
        'class' => 'select2-multiple',
        'options' => $routes,
        'empty' => true,
        'required' => true,
        'id' => 'search_route',
    )
); ?>

