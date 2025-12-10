<?php

class PositionConfigBdm extends AppModel{
    public $useTable = 'positions_config_bdms';

    public function new_position_config_bdm( $position_config_id, $user_id ){
        $fields = array(
            'PositionConfigBdm' => array(
                'position_config_id',
                'user_id'
            )
        );

        $position_config_bdm = array(
            'PositionConfigBdm' => array(
                'position_config_id' => $position_config_id,
                'user_id' => $user_id
            )
        );
        
        $this->create();
        $position_config_bdm_bd = $this->guardar($position_config_bdm, $fields);
        if ( !$position_config_bdm_bd ){
            return false;
        }

        return true;
    }

    public function getPositionConfigBdmId( $position_config_id ){
        return $this->find('all',array(
            'conditions' => array(
                'PositionConfigBdm.position_config_id' => $position_config_id
            ),
            'fields' => array(
                'PositionConfigBdm.user_id',
            ),
        ));
    }


}