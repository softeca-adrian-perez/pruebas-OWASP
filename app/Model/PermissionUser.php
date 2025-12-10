<?php

class PermissionUser extends AppModel{

    public $useTable = 'permissions_users';

    public $belongsTo = array(
        'Permission',
        'User',
    );

    private function _conditionId($id){
        return array('PermissionUser.id' => $id);
    }

    private function _conditionPermissionId( $permission_id ){
        return array('PermissionUser.permission_id' => $permission_id);
    }

    private function _conditionUserId($user_id){
        return array('PermissionUser.user_id' => $user_id);
    }

    public function conditions($fields){
        $conditions = array();

        if(isset($fields['id']) && $fields['id']!==''){
            $conditions[] = $this->_conditionId($fields['id']);
        }

        if(!empty($fields['permission_id'])){
            $conditions[] = $this->_conditionPermissionId($fields['permission_id']);
        }

        if(!empty($fields['user_id'])){
            $conditions[] = $this->_conditionUserId($fields['user_id']);
        }

        return $conditions;
    }

    /**
     * Obtiene todos los registros en base a los filtros recibidos
     *
     * @param int $permission_id
     * @param int $user_id
     * @return array
     */
    public function getPermissionsUsers($permission_id = null, $user_id = null){
        $fields = array();
        $conditions = array();

        if(!is_null($permission_id)){
            $fields['permission_id'] = $permission_id;
        }

        if(!is_null($user_id)){
            $fields['user_id'] = $user_id;
        }

        // The conditions are added
        $conditions[] = $this->conditions($fields);

        return $this->find(
            'all',
            array(
                'fields' => array(
                    'PermissionUser.*',
                    'Permission.name'.__s()
                ),
                'joins' => array(
                    array(
                        'alias' => 'Permission',
                        'table' => 'permissions',
                        'type' => 'LEFT',
                        'conditions' => 'PermissionUser.permission_id = Permission.id'
                    ),
                ),
                'conditions' => $conditions,
                'order'=> 'id',
            )
        );
    }

    /**
     * Obtiene todos los registros en base a los filtros recibidos
     *
     * @param array $permisos_id
     * @param int $user_id
     * @return array
     */
    public function cruzarConPermisosUsuarios($permisos_id = null, $user_id = null){

        // Intercambiamos las claves por el contenido, de forma que optimicemos el trabajo con el array de permisos
        $permisos_id = array_flip($permisos_id);

        // Se obtienen los permisos exclusivos asociados al usuario
        $permisos_exclusivos_usuario = $this->getPermissionsUsers(null, $user_id);

        foreach ($permisos_exclusivos_usuario as $permiso) {
            switch ($permiso['PermissionUser']['exclude_permission']){

                case 0:     // Añadirle el permiso

                    if(!array_key_exists($permiso['PermissionUser']['permission_id'], $permisos_id)){
                        $permisos_id[$permiso['PermissionUser']['permission_id']] = max($permisos_id)+1;
                    }
                    break;

                default:        // Quitarle el permiso

                    if(array_key_exists($permiso['PermissionUser']['permission_id'], $permisos_id)){
                        unset($permisos_id[$permiso['PermissionUser']['permission_id']]);
                    }
                    break;
            }
        }

        // Una vez realizados los ajustes correspondientes, se procede a intercambiar de nuevo las claves y valores
        $permisos_id = array_flip($permisos_id);

        return $permisos_id;
    }

    /**
     * Obtiene todos los registros en base a los filtros recibidos
     *
     * @param array $permisos_id
     * @return array
     */

    public function add($permiso_usuario){
        $fields = array(
            'PermissionUser' => array(
                'permission_id',
                'user_id',
                'exclude_permission',
            )
        );

        $this->create();

        return $this->guardar($permiso_usuario, $fields);
    }

    /**
     * Elimina de la base de datos el permiso del usuario con ID indicado
     *
     * @param int $id ID del permiso del usuario
     * @return boolean
     */
    public function eliminar($id){
        $permiso_usuario = $this->findById($id);

        if(!$permiso_usuario){
            return false;
        }

        if($this->canBeDeleted( $id )){

            if (!parent::eliminar( $id )) {
                return false;
            }

        }else{
            return false;
        }

        return true;

    }

    // By default it is always allowed to delete it
    public function canBeDeleted($id){
        return true;
    }

}