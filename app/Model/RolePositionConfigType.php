<?php

class RolePositionConfigType extends AppModel{
    public $useTable = 'roles_positions_config_types';

    public function getListByRoleId( $role_id ){
        return $this->find('list',array(
            'joins'=> array(
                array(
                    'alias' => 'PositionConfigType',
                    'table' => 'positions_config_types',
                    'type' => 'INNER',
                    'conditions' => array(
                        'PositionConfigType.id = RolePositionConfigType.position_config_type_id',
                    ),
                ),
            ),
            'conditions' => array(
                'role_id' => $role_id
            ),
            'fields' => array(
                'PositionConfigType.id',
                'PositionConfigType.name' . __s()
            ),
            'order' => array(
                'PositionConfigType.name' . __s()
            )
        ));
    }
}