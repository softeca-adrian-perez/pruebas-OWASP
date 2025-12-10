<?php

class PositionConfigDistributorNetwork extends AppModel{
    public $useTable = 'positions_config_distributor_networks';

    public function new_position_config_distributor_network( $position_config_id, $distributor_network_id ){
        $fields = array(
            'PositionConfigDistributorNetwork' => array(
                'position_config_id',
                'distributor_network_id'
            )
        );

        $position_config_distributor_network = array(
            'PositionConfigDistributorNetwork' => array(
                'position_config_id' => $position_config_id,
                'distributor_network_id' => $distributor_network_id
            )
        );
        
        $this->create();
        $position_config_distributor_network_bd = $this->guardar($position_config_distributor_network, $fields);
        if ( !$position_config_distributor_network_bd ){
            return false;
        }

        return true;
    }
}