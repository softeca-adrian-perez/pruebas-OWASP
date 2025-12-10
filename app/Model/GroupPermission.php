<?php
class GroupPermission extends AppModel
{
    public $useTable = 'groups_permissions';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array(
        'Permission' => array(
            'joinTable' => 'groups_permissions_permissions',
            'foreignKey' => 'group_permission_id',
            'associationForeignKey' => 'permission_id',
            'order' => 'Permission.id ASC',
        ),
        'User' => array(
            'joinTable' => 'groups_permissions_users',
            'foreignKey' => 'group_permission_id',
            'associationForeignKey' => 'user_id',
            'order' => 'User.id ASC',
        ),
        'Role' => array(
            'joinTable' => 'groups_permissions_roles',
            'foreignKey' => 'group_permission_id',
            'associationForeignKey' => 'role_id',
            'order' => 'Rol.id ASC',
        )
    );

    public $validate = array(
        'name_en' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_fr' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_de' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_nl' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'position_config_type_id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'required' => true,
                'message' => 'Type is required'
            ),
        ),
    );

    private function _queries($index)
    {
        $tmp = array(
            'home' => array(
                'fields' => array(
                    'GroupPermission.*'
                ),
                'order' => 'GroupPermission.id asc'
            ),
        );
        return $tmp[$index];
    }

    public function _query($index)
    {
        $tmp = $this->_queries($index);

        return $tmp;
    }

    private function _conditionId($id)
    {
        return array('GroupPermission.id' => $id);
    }

    private function _conditionName($name)
    {
        return array('GroupPermission.name' . __s() . ' LIKE' => '%' . $name . '%');
    }

    private function _conditionType($type)
    {
        return array('GroupPermission.position_config_type_id' => $type);
    }

    public function conditions($fields)
    {
        $conditions = array();

        if (isset($fields['id']) && $fields['id'] !== '') {
            $conditions[] = $this->_conditionId($fields['id']);
        }

        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }

        if (!empty($fields['position_config_type_id'])) {
            $conditions[] = $this->_conditionType($fields['position_config_type_id']);
        }

        return $conditions;
    }

    /**
     * Gets all permission groups based on received filters
     *
     * @param int $active
     * @return array
     */
    public function obtenerGruposPermisos($active = ConstantsBooleans::ACTIVE)
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

    public function add($group_permission)
    {
        $fields = array(
            'GroupPermission' => array(
                'name_en',
                'name_fr',
                'name_de',
                'position_config_type_id',
            )
        );
        $group_permission = $this->checkNames($group_permission);
        $this->create();

        // The permission group is saved and, if successful, its associated permissions are saved
        if ($this->guardar($group_permission, $fields)) {
            $this->GroupPermissionPermission = ClassRegistry::init('GroupPermissionPermission');

            // The id of the group of permissions that has just been added is added to the array, so that it can be included in the permissions of that group of permissions
            $group_permission['GroupPermission']['id'] = $this->id;

            return $this->GroupPermissionPermission->addVarios($group_permission);
        }
    }

    public function edit($group_permission)
    {
        $fields = array(
            'GroupPermission' => array(
                'id',
                'name_en',
                'name_fr',
                'name_de',
                'position_config_type_id',
            )
        );
        $group_permission = $this->checkNames($group_permission);
        // The permission group is saved and, if successful, its associated permissions are saved
        if ($this->guardar($group_permission, $fields)) {
            $this->GroupPermissionPermission = ClassRegistry::init('GroupPermissionPermission');

            // The id of the group of permissions that has just been added is added to the array, so that it can be included in the permissions of that group of permissions
            $group_permission['GroupPermission']['id'] = $this->id;

            return $this->GroupPermissionPermission->addVarios($group_permission);
        }
    }


    private function checkNames($data)
    {
        if (empty($data['GroupPermission']['name_fr'])) {
            $data['GroupPermission']['name_fr'] = $data['GroupPermission']['name_en'];
        }
        if (empty($data['GroupPermission']['name_de'])) {
            $data['GroupPermission']['name_de'] = $data['GroupPermission']['name_en'];
        }

        return $data;
    }

    /**
     * Delete the data from the database with the permission group ID indicated.
     *
     * @param int $id ID from permission group
     * @return boolean
     */
    public function eliminar($id)
    {
        $groupPermission = $this->findById($id);

        if (!$groupPermission) {
            return false;
        }

        if ($this->canBeDeleted($id)) {
            /* NOTE: There is no need to do a previous delete before deleting the permission group because the users, roles, and permissions are related in $hasAndBelongsToMany*/
            if (!parent::eliminar($id)) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Saves permissions in the database to which the permissions group belongs
     *
     * @param array $group_permission_permission
     * @return bool
     */
    public function editDataPermissionGroupPermission($group_permission_permission)
    {
        // You pass an empty array so that no errors pop up.
        // You have to do this even if the data is not saved in the table groups_permissions but in groups_permissions_permissions.
        // Its has to be done like this even thought the data is save in group_permissions_permission instead of group_permissions
        $fields = array(
            'GroupPermission' => array(
                'id',
            )
        );

        return $this->guardar($group_permission_permission, $fields);
    }

    public function canBeDeleted($id)
    {
        // It is checked whether it is a fixed or custom group
        $group_permission = $this->findById($id);

        if ($group_permission) {
            if (!$group_permission['GroupPermission']['is_fixed']) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Include in the load information of the permissions group of the permissions group those permissions that are already part of the permissions group.
     *
     * @param array $matriz_permissions
     * @return bool
     */
    public function completeInformationMatrixPermissions($matriz_permissions)
    {
        $this->PermissionUser = ClassRegistry::init('PermissionUser');

        $permissions_extra = array();

        if (isset($matriz_permissions['Permission'])){
            foreach ($matriz_permissions['Permission'] as $permission) {
                $permissions_extra[$permission['id']] = true;
            }
        }

        $matriz_permissions['GroupPermissionPermission'] = $permissions_extra;

        return $matriz_permissions;
    }

    public function search_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name' . __s()
            ),
            'order' => array(
                'name' . __s(),
            ),
        ));
    }

    public function search_list_by_position_config_type_id($position_config_type_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'position_config_type_id' => $position_config_type_id
            ),
            'fields' => array(
                'id',
                'name' . __s()
            ),
            'order' => array(
                'name' . __s(),
            ),
        ));
    }
}
