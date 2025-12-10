<?php
echo $this->Html->link(
    ' ' . __t('CRM.Visit_history'),
    array(
        'controller' => 'clients',
        'action' => 'tracking',
        $garage['Garage']['id']
    ),
    array(
        'class' => 'aag-button medium two',
    )
);
