<?php

class PositionConfigNetwork extends AppModel{
    public $useTable = 'positions_config_networks';

    public function new_position_config_network( $position_config_id, $network_id ){
        $fields = array(
            'PositionConfigNetwork' => array(
                'position_config_id',
                'network_id'
            )
        );

        $position_config_network = array(
            'PositionConfigNetwork' => array(
                'position_config_id' => $position_config_id,
                'network_id' => $network_id
            )
        );
        
        $this->create();
        $position_config_network_bd = $this->guardar($position_config_network, $fields);
        if ( !$position_config_network_bd ){
            return false;
        }

        return true;
    }
}