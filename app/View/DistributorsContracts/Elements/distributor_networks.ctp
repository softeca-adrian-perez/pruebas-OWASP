
<?php echo $this->Form->input(
    'DistributorContract.network_id',
    array(
        'label' => __t('Network.Network'),
        'type' => 'select',
        'class' => 'select2-multiple',
        'options' => $distributor_networks,
        'required' => true,
        'empty' => true
    )
); ?>