<?php

class PositionConfigAagMember extends AppModel{
    public $useTable = 'positions_config_aag_members';

    public function new_position_config_aag_member( $position_config_id, $aag_member ){
        $fields = array(
            'PositionConfigAagMember' => array(
                'position_config_id',
                'aag_member'
            )
        );

        $position_config_aag_member = array(
            'PositionConfigAagMember' => array(
                'position_config_id' => $position_config_id,
                'aag_member' => $aag_member
            )
        );
        
        $this->create();
        $position_config_aag_member_bd = $this->guardar($position_config_aag_member, $fields);
        if ( !$position_config_aag_member_bd ){
            return false;
        }

        return true;
    }
}