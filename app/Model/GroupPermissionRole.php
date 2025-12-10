<?php

class GroupPermissionRole extends AppModel{

    public $useTable = 'groups_permissions_roles';  // Intermediate table between "groups_permissions" y "roles"

    public $belongsTo = array(
        'GroupPermission',
        'Role',
    );

    private function _conditionId( $id ){
        return array('GroupPermissionRole.id' => $id);
    }

    private function _conditionGrupoPermisoId( $group_permission_id ){
        return array('GroupPermissionRole.group_permission_id' => $group_permission_id);
    }

    private function _conditionRoleId( $role_id ){
        return array('GroupPermissionRole.role_id' => $role_id);
    }

    public function conditions($fields){
        $conditions = array();

        if(isset($fields['id']) && $fields['id']!==''){
            $conditions[] = $this->_conditionId($fields['id']);
        }

        if(!empty($fields['group_permission_id'])){
            $conditions[] = $this->_conditionGrupoPermisoId($fields['group_permission_id']);
        }

        if(!empty($fields['role_id'])){
            $conditions[] = $this->_conditionRoleId($fields['role_id']);
        }

        return $conditions;
    }

    /**
     * Gets all records based on received filters
     *
     * @param int $group_permission_id
     * @param int $role_id
     * @return array
     */
    public function obtenerGrupoPermisoRol( $group_permission_id = null, $role_id = null ){

        $fields = array();
        $conditions = array();

        if(!is_null($group_permission_id)){
            $fields['group_permission_id'] = $group_permission_id;
        }

        if(!is_null($role_id)){
            $fields['role_id'] = $role_id;
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
     * Assigns the default permissions to the user based on their role.
     *
     * @param array $user
     * @return array
     */
    public function assignDefaultPermissionsUsers( $user ){

        $groups_permissions_users = array();

        // You get the default permissions that correspond to the user for the associated role
        $groups_permissions_roles = $this->obtenerGrupoPermisoRol(null, $user['User']['role_id']);

        $groups_permissions_ids = Hash::extract($groups_permissions_roles, '{n}.GroupPermissionRole.group_permission_id');

        foreach ($groups_permissions_ids as $key => $permission_id){
            $group_permission_user = array();
            $group_permission_user['group_permission_id'] = $permission_id;
            $group_permission_user['user_id'] = $user['User']['id'];

            $groups_permissions_users[] = $group_permission_user;
        }

        // Permission groups are assigned to the user
        $this->GroupPermissionUser = ClassRegistry::init('GroupPermissionUser');
        return $this->GroupPermissionUser->add($groups_permissions_users, ConstantsBooleans::YES);
    }

}