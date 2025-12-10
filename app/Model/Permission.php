<?php

class Permission extends AppModel
{

    public $useTable = 'permissions';

    public $belongsTo = array(
        'GroupingPermission',
    );

    public $hasAndBelongsToMany = array(
        'GroupPermission' => array(
            'joinTable' => 'groups_permissions_permissions',
            'foreignKey' => 'permission_id',
            'associationForeignKey' => 'group_permission_id',
        ),
        'Role' => array(
            'joinTable' => 'permissions_roles',
            'foreignKey' => 'permission_id',
            'associationForeignKey' => 'role_id',
        ),
        'User' => array(
            'joinTable' => 'permissions_users',
            'foreignKey' => 'permission_id',
            'associationForeignKey' => 'user_id',
        ),
    );

    public $validate = array(
        'name_en' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_fr' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_de' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    public function conditions($fields)
    {
        $conditions = array();

        if (isset($fields['id']) && $fields['id'] !== '') {
            $conditions[] = $this->_conditionId($fields['id']);
        }

        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }

        if (isset($fields['active']) && $fields['active'] !== '') {
            $conditions[] = $this->_conditionActive($fields['active']);
        }

        if (!empty($fields['user_id'])) {
            $conditions[] = $this->_conditionUser($fields['user_id']);
        }

        if (!empty($fields['role_id'])) {
            $conditions[] = $this->_conditionRole($fields['role_id']);
        }

        if (!empty($fields['position_id'])) {
            $conditions[] = $this->_conditionPosition($fields['position_id']);
        }

        if (!empty($fields['group_permission_id'])) {
            $conditions[] = $this->_conditionGroupPermission($fields['group_permission_id']);
        }

        if (!empty($fields['position_config_type_id'])) {
            $conditions[] = $this->_conditionPositionConfigType($fields['position_config_type_id']);
        }

        return $conditions;
    }

    private function _conditionId($id)
    {
        return array('Permission.id' => $id);
    }

    private function _conditionName($name)
    {
        return array('OR' => array(
            'Permission.name_en LIKE' => '%' . $name . '%',
            'Permission.name_de LIKE' => '%' . $name . '%',
            'Permission.name_fr LIKE' => '%' . $name . '%'
        ));
    }

    private function _conditionActive($active)
    {
        return array('Permission.active' => $active);
    }

    private function _conditionUser($user_id)
    {
        return array('User.id' => $user_id);
    }

    private function _conditionRole($role_id)
    {
        return array('User.role_id' => $role_id);
    }

    private function _conditionPosition($position_id)
    {
        return array('PositionConfig.position_id' => $position_id);
    }

    private function _conditionGroupPermission($group_permission_id)
    {
        return array('Permission.grouping_permission_id' => $group_permission_id);
    }

    private function _conditionPositionConfigType($position_config_type_id)
    {
        return array('Permission.position_config_type_id' => $position_config_type_id);
    }

    private $_queries = array(
        'search' => array(
            'fields' => array(
                'Permission.*',
            ),
            'order' => 'Permission.id asc'
        ),
        'search_permission' => array(
            'joins' => array(
                array(
                    'alias' => 'GroupPermissionPermission',
                    'table' => 'groups_permissions_permissions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GroupPermissionPermission.permission_id = Permission.id',
                    ),
                ),
                array(
                    'alias' => 'GroupPermission',
                    'table' => 'groups_permissions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GroupPermission.id = GroupPermissionPermission.group_permission_id',
                    ),
                ),
                array(
                    'alias' => 'PositionConfig',
                    'table' => 'positions_config',
                    'type' => 'INNER',
                    'conditions' => array(
                        'GroupPermission.id = PositionConfig.group_permission_id',
                    ),
                ),
                array(
                    'alias' => 'Position',
                    'table' => 'positions',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Position.id = PositionConfig.position_id',
                    ),
                ),
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.position_id = Position.id',
                    ),
                ),
                array(
                    'alias' => 'User',
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.id = User.contact_id',
                    ),
                ),
            ),
            'order' => array(
                'User.name'
            ),
            'fields' => array(
                'CONCAT(User.name, " ", User.surname) as full_user_name',
                'User.name',
                'User.role_id',
                'User.id',
                'PositionConfig.*'
            )
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    /**
     * Get all permissions based on received filters
     *
     * @param int $active
     * @return array
     */
    public function obtenerPermisos($active = ConstantsBooleans::ACTIVE)
    {

        $conditions = array();

        $fields['active'] = $active;

        // The conditions are added
        $conditions[] = $this->conditions($fields);

        return $this->find(
            'list',
            array(
                'conditions' => $conditions,
                'order' => 'name' . __s(),
            )
        );
    }

    /**
     * Obtiene los permisos del usuario. Para ello obtiene los permisos que le correspondería por su pertenencia a los grupos de usuario, a los cuales hay
     * que aplicar una serie de comprobaciones adicionales, de forma que éstos queden aumentados o recortados en base a ellas.
     *
     * @param array $user
     * @return array
     */
    public function obtenerPermisosUsuario($user)
    {

        $permisos_del_usuario_ids = array();

        $this->GroupPermissionUser = ClassRegistry::init('GroupPermissionUser');
        $this->PermissionUser = ClassRegistry::init('PermissionUser');
        $this->PermissionRole = ClassRegistry::init('PermissionRole');

        //--------------------------------------------------------------------------------------------------------------
        // [1] Se obtienen todos los permisos a los que tiene acceso por su pertenencia a grupos de permisos
        //--------------------------------------------------------------------------------------------------------------
        $permisos_del_usuario_ids = $this->GroupPermissionUser->obtenerPermisosGrupoPermisosDelUsuario($user['id']);

        //--------------------------------------------------------------------------------------------------------------
        // [2] Se comprueba si, a nivel individual, el usuario tiene añadidos o quitados permisos
        //--------------------------------------------------------------------------------------------------------------
        $permisos_del_usuario_ids = $this->PermissionUser->cruzarConPermisosUsuarios($permisos_del_usuario_ids, $user['id']);

        //--------------------------------------------------------------------------------------------------------------
        // [3] Por último, se verifica que el rol del usuario realmente tiene acceso a los permisos indicados (para evitar por ejemplo que por error se haya asignado un permiso
        //     a los que no debería tener acceso por su rol).
        //--------------------------------------------------------------------------------------------------------------
        $permisos_del_usuario_ids = $this->PermissionRole->verificarPermisosVisiblesPorRol($permisos_del_usuario_ids, $user['role_id']);

        return $permisos_del_usuario_ids;
    }

    /**
     * Get the permission from the User.
     *
     * @param array $user
     * @return array
     */
    public function obtenerPermisosUsuariov2($user)
    {
        $permissions = array();
        $this->PositionConfig = ClassRegistry::init('PositionConfig');
        $this->PositionConfigNetwork = ClassRegistry::init('PositionConfigNetwork');
        $this->PositionConfigRegion = ClassRegistry::init('PositionConfigRegion');
        $this->PositionConfigTradingGroup = ClassRegistry::init('PositionConfigTradingGroup');
        $this->GroupPermissionPermission = ClassRegistry::init('GroupPermissionPermission');

        $position_configs = $this->PositionConfig->findAllByPositionId($user['Contact']['position_id']);

        foreach ($position_configs as $key => $position_config) {

            $tmp_networks = $this->PositionConfigNetwork->findAllByPositionConfigId($position_config['PositionConfig']['id']);

            $position_configs[$key]['PositionConfig']['networks'] = Hash::extract($tmp_networks, '{n}.PositionConfigNetwork.network_id');

            $tmp_regions = $this->PositionConfigRegion->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            $position_configs[$key]['PositionConfig']['regions'] = Hash::extract($tmp_regions, '{n}.PositionConfigRegion.region_id');

            $tmp_trading_groups = $this->PositionConfigTradingGroup->findAllByPositionConfigId($position_config['PositionConfig']['id']);
            $position_configs[$key]['PositionConfig']['trading_groups'] = Hash::extract($tmp_trading_groups, '{n}.PositionConfigTradingGroup.trading_group_id');
        }


        $positions_default = Hash::extract(
            $position_configs,
            '{n}.PositionConfig[position_config_type_id=' . ConstantsPositionConfigType::DEFAULT_TYPE . ']'
        );

        $positions_garage = Hash::extract(
            $position_configs,
            '{n}.PositionConfig[position_config_type_id=' . ConstantsPositionConfigType::GARAGE . '][all_networks!=1][all_regions!=1]'
        );

        $positions_garage_all_regions = Hash::extract(
            $position_configs,
            '{n}.PositionConfig[position_config_type_id=' . ConstantsPositionConfigType::GARAGE . '][all_regions=1]'
        );

        $positions_garage_all_networks = Hash::extract(
            $position_configs,
            '{n}.PositionConfig[position_config_type_id=' . ConstantsPositionConfigType::GARAGE . '][all_networks=1]'
        );

        $positions_distributor = Hash::extract(
            $position_configs,
            '{n}.PositionConfig[position_config_type_id=' . ConstantsPositionConfigType::DISTRIBUTOR . '][all_trading_groups!=1]'
        );

        $positions_distributor_all_trading_groups = Hash::extract(
            $position_configs,
            '{n}.PositionConfig[position_config_type_id=' . ConstantsPositionConfigType::DISTRIBUTOR . '][all_trading_groups=1]'
        );

        if ($positions_garage_all_networks) {
            foreach ($positions_garage_all_networks as $garage_all_networks) {
                $tmp_permissions = $this->GroupPermissionPermission->findAllByGroupPermissionId($garage_all_networks['group_permission_id']);
                if (!$garage_all_networks['all_regions']) {
                    foreach ($garage_all_networks['regions'] as $region) {
                        $permissions['networks_regions'][0][$region] = Hash::extract($tmp_permissions, '{n}.GroupPermissionPermission.permission_id');
                    }
                } else {
                    $permissions['networks_regions'][0][0] = Hash::extract($tmp_permissions, '{n}.GroupPermissionPermission.permission_id');
                }
            }
        }

        if ($positions_garage_all_regions) {
            foreach ($positions_garage_all_regions as $garage_all_regions) {
                $tmp_permissions = $this->GroupPermissionPermission->findAllByGroupPermissionId($garage_all_regions['group_permission_id']);
                if (!$garage_all_regions['all_networks']) {
                    foreach ($garage_all_regions['networks'] as $network) {
                        $permissions['networks_regions'][$network][0] = Hash::extract($tmp_permissions, '{n}.GroupPermissionPermission.permission_id');
                    }
                } else {
                    $permissions['networks_regions'][0][0] = Hash::extract($tmp_permissions, '{n}.GroupPermissionPermission.permission_id');
                }
            }
        }
        foreach ($positions_garage as $garage) {
            foreach ($garage['networks'] as $network) {
                foreach ($garage['regions'] as $region) {
                    $tmp_permissions = $this->GroupPermissionPermission->findAllByGroupPermissionId($garage['group_permission_id']);
                    $permissions['networks_regions'][$network][$region] = Hash::extract($tmp_permissions, '{n}.GroupPermissionPermission.permission_id');
                }
            }
        }

        foreach ($positions_distributor_all_trading_groups as $distributor_all_trading_groups) {
            $tmp_permissions = $this->GroupPermissionPermission->findAllByGroupPermissionId($distributor_all_trading_groups['group_permission_id']);
            $permissions['trading_groups'][0] = Hash::extract($tmp_permissions, '{n}.GroupPermissionPermission.permission_id');
        }

        foreach ($positions_distributor as $distributor) {
            foreach ($distributor['trading_groups'] as $trading_group) {
                $tmp_permissions = $this->GroupPermissionPermission->findAllByGroupPermissionId($distributor['group_permission_id']);
                $permissions['trading_groups'][$trading_group] = Hash::extract($tmp_permissions, '{n}.GroupPermissionPermission.permission_id');
            }
        }

        if (isset($positions_default[0])) {
            $tmp_permissions = $this->GroupPermissionPermission->findAllByGroupPermissionId($positions_default[0]['group_permission_id']);
            $permissions['default'] = Hash::extract($tmp_permissions, '{n}.GroupPermissionPermission.permission_id');
        }

        return $permissions;
    }

    public function reversePermissions($permissions)
    {

        $permissions_reverse = array();

        if (isset($permissions['networks_regions'])) {
            foreach ($permissions['networks_regions'] as $network => $arr1) {
                foreach ($arr1 as $region => $arr2) {
                    foreach ($arr2 as $arr3) {
                        $permissions_reverse[$arr3]['networks_regions'][][$network] = $region;
                    }
                }
            }
        }

        if (isset($permissions['trading_groups'])) {
            foreach ($permissions['trading_groups'] as $trading_group => $arr1) {
                foreach ($arr1 as $arr2) {
                    $permissions_reverse[$arr2]['trading_groups'][] = $trading_group;
                    $permissions_reverse[$arr2]['trading_groups'] = array_values(array_unique($permissions_reverse[$arr2]['trading_groups']));
                }
            }
        }

        if (isset($permissions['default'])) {
            foreach ($permissions['default'] as $default => $arr1) {
                $permissions_reverse[$arr1]['default'][] = $default;
                $permissions_reverse[$arr1]['default'] = array_values(array_unique($permissions_reverse[$arr1]['default']));
            }
        }

        ksort($permissions_reverse);

        return $permissions_reverse;
    }



    public function getPermissions()
    {

        $permisos = $this->find(
            'all',
            array(
                'fields' => array(
                    'Permission.*',
                    ' GroupingPermission.id',
                    ' GroupingPermission.name' . __s()
                ),
                'joins' => array(
                    array(
                        'alias' => 'GroupingPermission',
                        'table' => 'groupings_permissions',
                        'type' => 'LEFT',
                        'conditions' => 'Permission.grouping_permission_id = GroupingPermission.id'
                    ),
                ),
                'order' => array(
                    'GroupingPermission.id' => 'asc',
                    'Permission.id' => 'asc',
                ),
            )

        );
        return $permisos;
    }

    public function getPermissionsByType($position_config_type_id = ConstantsPositionConfigType::DEFAULT_TYPE)
    {

        $permisos = $this->find(
            'all',
            array(
                'fields' => array(
                    'Permission.*',
                    ' GroupingPermission.id',
                    ' GroupingPermission.name' . __s()
                ),
                'joins' => array(
                    array(
                        'alias' => 'GroupingPermission',
                        'table' => 'groupings_permissions',
                        'type' => 'LEFT',
                        'conditions' => 'Permission.grouping_permission_id = GroupingPermission.id'
                    ),
                ),
                'conditions' => array(
                    'Permission.position_config_type_id' => $position_config_type_id,
                ),
                'order' => array(
                    'GroupingPermission.id' => 'asc',
                    'Permission.id' => 'asc',
                ),
            )

        );
        return $permisos;
    }


    public function getByGroupingPermission($grouping_permission_id)
    {
        $permisos = $this->find(
            'list',
            array(
                'fields' => array(
                    'Permission.id',
                    'Permission.name' . __s(),
                ),
                'joins' => array(
                    array(
                        'alias' => 'GroupingPermission',
                        'table' => 'groupings_permissions',
                        'type' => 'INNER',
                        'conditions' => 'Permission.grouping_permission_id = GroupingPermission.id'
                    ),
                ),
                'conditions' => array(
                    'Permission.grouping_permission_id' => $grouping_permission_id,
                ),
                'order' => array(
                    'Permission.id' => 'asc',
                ),
            )

        );
        return $permisos;
    }
}
