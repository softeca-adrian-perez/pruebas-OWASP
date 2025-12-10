<?php

class PermissionVersion extends AppModel{

    public $useTable = 'permissions_versions';

    public $belongsTo = array(
        'Permission',
        'Version',
    );

    private function _conditionId($id){
        return array('PermissionVersion.id' => $id);
    }

    private function _conditionPermissionId( $permission_id ){
        return array('PermissionVersion.permission_id' => $permission_id);
    }

    private function _conditionVersionId($version_id){
        return array('PermissionVersion.version_id' => $version_id);
    }

    public function conditions($fields){
        $conditions = array();

        if(isset($fields['id']) && $fields['id']!==''){
            $conditions[] = $this->_conditionId($fields['id']);
        }

        if(!empty($fields['permission_id'])){
            $conditions[] = $this->_conditionPermissionId($fields['permission_id']);
        }

        if(!empty($fields['version_id'])){
            $conditions[] = $this->_conditionVersionId($fields['version_id']);
        }

        return $conditions;
    }

    /**
     * Obtiene todos los registros en base a los filtros recibidos
     *
     * @param int $permission_id
     * @param int $version_id
     * @return array
     */
    public function obtenerPermisosVersiones( $permission_id = null, $version_id = null){

        $fields = array();
        $conditions = array();

        if(!is_null($permission_id)){
            $fields['permission_id'] = $permission_id;
        }

        if(!is_null($version_id)){
            $fields['version_id'] = $version_id;
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
     * Obtiene todos los permisos de la versión indicada. Si no se recibe versión, se devuelven todos los permisos
     *
     * @param int $version_id
     * @return array
     */
    public function obtenerPermisosVersion($version_id = null){

        $conditions = array();
        $conditions['Permission.active'] = ConstantsBooleans::ACTIVE;

        // Si se tiene una versión, entonces se
        if(!is_null($version_id)){
            $permisos_version = $this->obtenerPermisosVersiones(null, $version_id);
            $permisos_version_ids = Hash::extract($permisos_version, '{n}.PermissionVersion.permission_id');

            $conditions['Permission.id'] = $permisos_version_ids;
        }

        $permisos = $this->Permission->find(
            'all',
            array(
                'fields' => array(
                    'Permission.*',
                    ' GroupingPermission.id',
                    ' GroupingPermission.name'.__s()
                ),
                'joins' => array(
                    array(
                        'alias' => 'GroupingPermission',
                        'table' => 'groupings_permissions',
                        'type' => 'LEFT',
                        'conditions' => 'Permission.grouping_permission_id = GroupingPermission.id'
                    ),
                ),
                'conditions' => $conditions,
                'order'=> array(
                    'GroupingPermission.id' => 'asc',
                    'Permission.id' => 'asc',
                ),
            )

        );

        return $permisos;
    }

    /**
     * Obtiene todos los registros en base a los filtros recibidos
     *
     * @param array $permisos_id
     * @param int $version_id
     * @return array
     */
    public function cruzarConPermisosVersiones($permisos_id = null, $version_id = null){

        // Intercambiamos las claves por el contenido, de forma que optimicemos el trabajo con el array de permisos
        $permisos_id = array_flip($permisos_id);

        // Se obtienen los permisos exclusivos asociados a la versión
        $permisos_exclusivos_version = $this->obtenerPermisosVersiones(null, $version_id);
        $permisos_exclusivos_version_ids = Hash::extract($permisos_exclusivos_version, '{n}.PermissionVersion.permission_id');
        $permisos_exclusivos_version_ids = array_flip($permisos_exclusivos_version_ids);

        // Se obtienen los elementos en común, entre los permisos del usuario y los permisos de la versión.
        // Los permisos que prevalecen son los de la versión, por lo que si el usuario tuviera más, en este cruce se
        // eliminarían
        $permisos_en_comun_id = array_flip(array_intersect_key($permisos_exclusivos_version_ids, $permisos_id));

        return $permisos_en_comun_id;
    }

}