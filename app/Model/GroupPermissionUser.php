<?php

class GroupPermissionUser extends AppModel{

    public $useTable = 'groups_permissions_users';  // Intermediate table between "groups_permissions" y "users"

    public $belongsTo = array(
        'GroupPermission',
        'User',
    );

    private function _conditionId($id){
        return array('GroupPermissionUser.id' => $id);
    }

    private function _conditionGroupPermissionId($group_permission_id){
        return array('GroupPermissionUser.group_permission_id' => $group_permission_id);
    }

    private function _conditionUserId($user_id){
        return array('GroupPermissionUser.user_id' => $user_id);
    }

    public function conditions($fields){
        $conditions = array();

        if(isset($fields['id']) && $fields['id']!==''){
            $conditions[] = $this->_conditionId($fields['id']);
        }

        if(!empty($fields['group_permission_id'])){
            $conditions[] = $this->_conditionGroupPermissionId($fields['group_permission_id']);
        }

        if(!empty($fields['user_id'])){
            $conditions[] = $this->_conditionUserId($fields['user_id']);
        }

        return $conditions;
    }

    /**
     * Gets all records based on received filters
     *
     * @param int $group_permission_id
     * @param int $user_id
     * @return array
     */
    public function obtenerGrupoPermisoUsuario( $group_permission_id = null, $user_id = null ){

        $fields = array();
        $conditions = array();

        if(!is_null($group_permission_id)){
            $fields['group_permission_id'] = $group_permission_id;
        }

        if(!is_null($user_id)){
            $fields['user_id'] = $user_id;
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

    public function add( $group_permission_user, $save_many = ConstantsBooleans::NO){
        $fields = array(
            'GroupPermissionUser' => array(
                'group_permission_id',
                'user_id',
            )
        );

        $this->create();

        if( $save_many == ConstantsBooleans::NO){
            return $this->guardar($group_permission_user, $fields);
        }
        else{
            return $this->guardarVarios($group_permission_user, $fields);
        }

    }

    /**
     * Gets all permissions for the permission groups to which the specified user belongs.
     *
     * @param int $user_id
     * @return array
     */
    public function obtenerPermisosGrupoPermisosDelUsuario( $user_id = null , $group_permission_id = null){

        $this->GroupPermissionPermission = ClassRegistry::init('GroupPermissionPermission');

        // Obtain the permission groups that the user has access to
        if($group_permission_id == null){
            $groups_permissions = $this->obtenerGrupoPermisoUsuario(null, $user_id);

            // Si se han obtenido grupos de permisos, se continua con la obtención de los permisos. En caso contrario, se devuelve un array vacío.
            // Con esto se evita que al no haber grupos de permisos, al llamar a "obtenerGrupoPermisoPermiso" se obtendrían todos los permisos.
            if(!empty($groups_permissions)){

                $groups_permissions_ids = Hash::extract($groups_permissions, '{n}.GroupPermissionUser.group_permission_id');

                // Se obtienen los permisos correspondientes a los grupos
                $permisos_del_grupo = $this->GroupPermissionPermission->obtenerGrupoPermisoPermiso($groups_permissions_ids, null);
                $permisos_del_grupo_ids = Hash::extract($permisos_del_grupo, '{n}.GroupPermissionPermission.permission_id');

                // Se eliminan los posibles permisos repetidos al poder estar incluidos en distintos grupos de permisos a los que pueda pertenecer el usuario (reindexando los índices)
                $permisos_del_usuario_ids = array_keys(array_count_values(array_unique($permisos_del_grupo_ids)));

                return $permisos_del_usuario_ids;

            }
            else{
                return array();
            }
        }else{
            //Recibimos un grupo de permiso concreto:

            $permisos_del_grupo = $this->GroupPermissionPermission->obtenerGrupoPermisoPermiso($group_permission_id, null);
            $permisos_del_grupo_ids = Hash::extract($permisos_del_grupo, '{n}.GroupPermissionPermission.permission_id');

            // Se eliminan los posibles permisos repetidos al poder estar incluidos en distintos grupos de permisos a los que pueda pertenecer el usuario (reindexando los índices)
            $permisos_del_usuario_ids = array_keys(array_count_values(array_unique($permisos_del_grupo_ids)));

            return $permisos_del_usuario_ids;
        }

        

    }

}