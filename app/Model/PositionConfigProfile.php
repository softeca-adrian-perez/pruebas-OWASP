<?php

class PositionConfigProfile extends AppModel{
    public $useTable = 'positions_config_profiles';

    public function new_position_config_profile( $position_config_id, $profile_id ){
        $fields = array(
            'PositionConfigProfile' => array(
                'position_config_id',
                'profile_id'
            )
        );

        $position_config_profile = array(
            'PositionConfigProfile' => array(
                'position_config_id' => $position_config_id,
                'profile_id' => $profile_id
            )
        );
        
        $this->create();
        $position_config_profile_bd = $this->guardar($position_config_profile, $fields);
        if ( !$position_config_profile_bd ){
            return false;
        }

        return true;
    }
}