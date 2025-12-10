<?php
if (isset($trading_groups) && count($trading_groups) > 1) { ?>
    <?php
    echo $this->Form->input(
        'GarageNetwork.trading_group_id',
        array(
            'label' => __t('Garage.Trading_group'),
            'class' => 'select2-multiple input-disabled',
            'type' => 'select',
            'options' => $trading_groups,
            'multiple' => false,
            'empty' => true,
            'id' => 'trading_group_id',
            'data-type' => 'select',
            'disabled' => (isset($garage) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) ? true : false,
        )
    );
} else { ?>
    <?php
    if (isset($trading_groups) && count($trading_groups) == 1) {
        $required_val = true;
    }else {
        $required_val = false;
    }
    echo $this->Form->input(
        'GarageNetwork.trading_group_id',
        array(
            'label' => __t('Garage.Trading_group'),
            'class' => 'select2-multiple input-disabled',
            'type' => 'select',
            'multiple' => false,
            'empty' => isset($trading_groups) ? false : true,
            'options' => isset($trading_groups) ? $trading_groups : array(),
            'id' => 'trading_group_id',
            'required' => $required_val,
            'disabled' => (isset($garage) && $garage['Garage']['status'] == ConstantsGarageStatus::INACTIVE) ? true : false,
        )
    ); ?>
    <?php
}
?>
