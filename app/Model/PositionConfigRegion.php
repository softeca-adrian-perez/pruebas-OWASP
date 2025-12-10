<?php

class PositionConfigRegion extends AppModel{
    public $useTable = 'positions_config_regions';

    public function new_position_config_region( $position_config_id, $region_id ){
        $fields = array(
            'PositionConfigRegion' => array(
                'position_config_id',
                'region_id'
            )
        );

        $position_config_region = array(
            'PositionConfigRegion' => array(
                'position_config_id' => $position_config_id,
                'region_id' => $region_id
            )
        );
        
        $this->create();

        $position_config_region_bd = $this->guardar($position_config_region, $fields);
        if ( !$position_config_region_bd ){
            return false;
        }

        return true;
    }
}