<?php

class PositionConfigTradingGroup extends AppModel{
    public $useTable = 'positions_config_trading_groups';

    public function new_position_config_trading_group( $position_config_id, $trading_group_id ){
        $fields = array(
            'PositionConfigTradingGroup' => array(
                'position_config_id',
                'trading_group_id'
            )
        );

        $position_config_trading_group = array(
            'PositionConfigTradingGroup' => array(
                'position_config_id' => $position_config_id,
                'trading_group_id' => $trading_group_id
            )
        );
        
        $this->create();
        $position_config_trading_group_bd = $this->guardar($position_config_trading_group, $fields);
        if ( !$position_config_trading_group_bd ){
            return false;
        }

        return true;
    }
}