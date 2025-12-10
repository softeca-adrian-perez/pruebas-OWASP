<?php

class GroupPermissionPermission extends AppModel{

    public $useTable = 'groups_permissions_permissions';  // Intermediate table between "groups_permissions" y "permissions"

    public $belongsTo = array(
        'GroupPermission',
        'User',
    );

    private function _conditionId($id){
        return array('GroupPermissionPermission.id' => $id);
    }

    private function _conditionGroupPermissionId( $group_permission_id ){
        return array('GroupPermissionPermission.group_permission_id' => $group_permission_id);
    }

    private function _conditionPermissionId($permission_id){
        return array('GroupPermissionPermission.permission_id' => $permission_id);
    }

    public function conditions($fields){
        $conditions = array();

        if(isset($fields['id']) && $fields['id']!==''){
            $conditions[] = $this->_conditionId($fields['id']);
        }

        if(!empty($fields['group_permission_id'])){
            $conditions[] = $this->_conditionGroupPermissionId($fields['group_permission_id']);
        }

        if(!empty($fields['permission_id'])){
            $conditions[] = $this->_conditionPermissionId($fields['permission_id']);
        }

        return $conditions;
    }

    /**
     * Gets all records based on received filters
     *
     * @param array $group_permission_id
     * @param array $permission_id
     * @return array
     */
    public function obtenerGrupoPermisoPermiso( $group_permission_id = null, $permission_id = null){

        $fields = array();
        $conditions = array();

        if(!is_null($group_permission_id)){
            $fields['group_permission_id'] = $group_permission_id;
        }

        if(!is_null($permission_id)){
            $fields['permission_id'] = $permission_id;
        }

        // The conditions are added
        $conditions[] = $this->conditions($fields);

        return $this->find(
            'all',
            array(
                'conditions' => $conditions,
                'order'=> 'id',
            )
        );
    }

    /**
     * Saves all the permissions associated with the specified permissions group to the database.
     *
     * @param array $group_permissions_permissions
     * @return bool
     */
    public function addVarios( $group_permissions_permissions ){

        $fields = array(
            'GroupPermissionPermission' => array(
                'group_permission_id',
                'permission_id',
            )
        );

        $matriz_final = array();

        // All the permissions associated with the permissions group are traversed to create an array in which they are collected.
        if( isset($group_permissions_permissions['GroupPermissionPermission']) ){
            foreach($group_permissions_permissions['GroupPermissionPermission'] as $key => $value){

                $permission = array();
    
                $permission['permission_id'] = $key;
                $permission['group_permission_id'] = $group_permissions_permissions['GroupPermission']['id'];
    
                if(!empty($permission)){
                    $matriz_final[] = $permission;
                }
    
            }
        }

        // All permissions that the already assigned permission group can have are deleted and new ones that have been provided are saved
        $this->deleteAll(
            array(
                'GroupPermissionPermission.group_permission_id' => $group_permissions_permissions['GroupPermission']['id']
            ),
            false
        );

        if(empty($matriz_final)){
            return true;
        }

        return $this->saveAll($matriz_final, $fields);
    }

    public function getAllByGroupPermissionId( $group_permission_id ){
        return $this->find('list',array(
            'conditions' => array(
                'group_permission_id' => $group_permission_id
            ),
            'fields' => array(
                'id',
                'permission_id'
            )
        ));
    }

    public function getAllGroupPermissionsListByPermissionId( $permission_id){
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'GroupPermissionPermission.permission_id' => $permission_id,
                ),
                'fields' => array(
                    'GroupPermissionPermission.group_permission_id',
                ),
            )
        );
    }
    
}