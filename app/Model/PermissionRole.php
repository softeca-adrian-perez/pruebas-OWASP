<?php

class PermissionRole extends AppModel{

    public $useTable = 'permissions_roles';

    public $belongsTo = array(
        'Permission',
        'Role',
    );

    private function _conditionId($id){
        return array('PermissionRole.id' => $id);
    }

    private function _conditionPermissionId( $permission_id ){
        return array('PermissionRole.permission_id' => $permission_id);
    }

    private function _conditionRoleId( $role_id ){
        return array('PermissionRole.role_id' => $role_id);
    }

    public function conditions($fields){
        $conditions = array();

        if(isset($fields['id']) && $fields['id']!==''){
            $conditions[] = $this->_conditionId($fields['id']);
        }

        if(!empty($fields['permission_id'])){
            $conditions[] = $this->_conditionPermissionId($fields['permission_id']);
        }

        if(!empty($fields['role_id'])){
            $conditions[] = $this->_conditionRoleId($fields['role_id']);
        }

        return $conditions;
    }

    /**
     * Obtiene todos los registros en base a los filtros recibidos
     *
     * @param int $permission_id
     * @param int $role_id
     * @return array
     */
    public function obtenerPermisosRoles($permission_id = null, $role_id = null){

        $fields = array();
        $conditions = array();

        if(!is_null($permission_id)){
            $fields['permission_id'] = $permission_id;
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
     * Obtiene todos los registros en base a los filtros recibidos
     *
     * @param array $permisos_id
     * @param int $role_id
     * @return array
     */
    public function verificarPermisosVisiblesPorRol($permisos_id = null, $role_id = null){

        // Se obtienen las restricciones de roles para los permisos recibidos
        $permisos_para_roles = $this->obtenerPermisosRoles($permisos_id);

        // Intercambiamos las claves por el contenido, de forma que optimicemos el trabajo con el array de permisos (para quitar por ejemplo los permisos que no haya que aplicar)
        $permisos_id = array_flip($permisos_id);

        // Obtenemos los permisos implicados
        $permisos_para_roles_ids = Hash::extract($permisos_para_roles, '{n}.PermissionRole.permission_id');

        // Se eliminan los permisos repetidos (reindexando los índices)
        $permisos_para_roles_ids = array_keys(array_count_values(array_unique($permisos_para_roles_ids)));

        // Para cada permiso identificado, se comprueba si está disponible para el rol indicado
        foreach($permisos_para_roles_ids as $key => $permission_id){

            $coincidencia_encontrada = false;

            foreach($permisos_para_roles as $permiso_rol){

                if($permiso_rol['PermissionRole']['permission_id'] == $permission_id){

                    if($permiso_rol['PermissionRole']['role_id'] == $role_id){
                        $coincidencia_encontrada = true;
                        break;
                    }

                }

            }

            // Si no se ha encontrado el permiso para el rol, se elimina de los permisos recibidos
            if(!$coincidencia_encontrada){
                unset($permisos_id[$permission_id]);
            }

        }

        // Una vez realizados los ajustes correspondientes, se procede a intercambiar de nuevo las claves y valores
        $permisos_id = array_flip($permisos_id);

        return $permisos_id;
    }

}