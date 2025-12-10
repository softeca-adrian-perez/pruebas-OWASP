<?php
class Position extends AppModel
{
    public $useTable = 'positions';

    public $validate = array(
        'role_id' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_role',
            ),
        ),
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
    );

    private $_queries = array(
        'search' =>
        array(
            'fields' => array(
                'Position.*',
            )
        ),
        'home' =>
        array(
            'fields' => array(
                'Position.*',
            ),
            'order' => 'Position.id asc'
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function conditions($fields)
    {
        $conditions = array();
        if (!empty($fields['name'])) {
            $conditions[] = $this->_conditionName($fields['name']);
        }

        if (!empty($fields['role_id'])) {
            $conditions[] = $this->_conditionRole($fields['role_id']);
        }

        if (!empty($fields['group_permission_id'])) {
            $conditions[] = $this->_conditionGroupPermission($fields['group_permission_id']);
        }

        return $conditions;
    }

    private function _conditionName($name)
    {
        return array('Position.name' . __s() . ' LIKE' =>  '%' . $name . '%');
    }

    private function _conditionRole($role_id)
    {
        return array('Position.role_id' => $role_id);
    }

    private function _conditionGroupPermission($group_permission_id)
    {
        $positions = $this->getPositionsWithGroupPermissionId($group_permission_id);
        return array('Position.id' => Hash::extract($positions, '{n}.Position.id'));
    }


    public function new_position($data)
    {
        $fields = array(
            'Position' => array(
                'name_en',
                'name_fr',
                'name_de',
                'role_id'
            )
        );

        $data = $this->checkNames($data);

        $position = array(
            'Position' => array(
                'name_en' => $data['Position']['name_en'],
                'name_fr' => $data['Position']['name_fr'],
                'name_de' => $data['Position']['name_de'],
                'role_id' => $data['Position']['role_id'],
            )
        );

        $this->create();
        $service_bd = $this->guardar($position, $fields);
        if (!$service_bd) {
            return false;
        }

        return true;
    }

    public function edit_position($data)
    {
        $fields = array(
            'Position' => array(
                'id',
                'name_en',
                'name_fr',
                'name_de',
                'role_id'
            )
        );

        $data = $this->checkNames($data);

        $position = array(
            'Position' => array(
                'id' => $data['Position']['id'],
                'name_en' => $data['Position']['name_en'],
                'name_fr' => $data['Position']['name_fr'],
                'name_de' => $data['Position']['name_de'],
                'role_id' => $data['Position']['role_id'],
            )
        );

        $service_bd = $this->guardar($position, $fields);
        if (!$service_bd) {
            return false;
        }

        return true;
    }

    public function getPositionsWithGroupPermissionId($group_permission_id)
    {
        return $this->find(
            'all',
            array(
                'joins' => array(
                    array(
                        'alias' => 'PositionConfig',
                        'table' => 'positions_config',
                        'type' => 'INNER',
                        'conditions' => array(
                            'PositionConfig.position_id = Position.id',
                        ),
                    ),
                ),
                'conditions' => array(
                    'PositionConfig.group_permission_id' => $group_permission_id,
                ),
                'fields' => array(
                    'Position.id'
                ),
            )
        );
    }

    public function search_list_bdm()
    {
        return $this->find('list', array(
            'conditions' => array(
                'OR' => array(
                    'role_id' => array(
                        ConstantsRoles::BDM_AAG,
                        ConstantsRoles::BDM_TG,
                    ),
                    'id' => array(
                        ConstantsPositions::REGIONAL_SALES_MANAGER_CV_ID,
                        ConstantsPositions::REGIONAL_SALES_MANAGER_LV_ID
                    )
                )
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => 'name_' . __l() . ' ASC'
        ));
    }

    public function search_list_bdm_roles($roles)
    {

        return $this->find('list', array(
            'conditions' => array(
                'role_id' => $roles
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => 'name_' . __l() . ' ASC'
        ));
    }


    public function search_list_staff()
    {
        return $this->find('list', array(
            'conditions' => array(
                'role_id' => ConstantsRoles::GENERIC_STAFF
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            )
        ));
    }

    public function search_list_staff_garage_manager()
    {
        return $this->find('list', array(
            'conditions' => array(
                'OR' => array(
                    array('role_id' => ConstantsRoles::GENERIC_STAFF),
                )
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            )
        ));
    }

    public function search_list_staff_distributor_manager()
    {
        return $this->find('list', array(
            'conditions' => array(
                'OR' => array(
                    array('role_id' => ConstantsRoles::GENERIC_STAFF),
                )
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            )
        ));
    }

    public function search_list_general_branch_manager_garage()
    {
        return $this->find('list', array(
            'conditions' => array(
                'OR' => array(
                    'id' => array(
                        ConstantsPositions::GENERAL_BRANCH_MANAGER_ID,
                    ),
                    'role_id' => ConstantsRoles::GARAGE
                ),
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            )
        ));
    }

    public function search_list_general_branch_manager_distributor()
    {
        return $this->find('list', array(
            'conditions' => array(
                'OR' => array(
                    'id' => array(
                        ConstantsPositions::GENERAL_BRANCH_MANAGER_ID,
                    ),
                    'role_id' => ConstantsRoles::DISTRIBUTOR
                ),
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            )
        ));
    }

    public function search_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name' . __s()
            ),
            'order' => array(
                'name' . __s()
            )
        ));
    }

    public function search_list_profiles()
    {
        return $this->find('list', array(

            'conditions' => array(
                'Or' => array(
                    array(
                        'role_id' => ConstantsRoles::DISTRIBUTOR
                    ),
                    array(
                        'role_id' => ConstantsRoles::GARAGE
                    ),
                ),
            ),
            'fields' => array(
                'id',
                'name' . __s()
            ),
            'order' => array(
                'name' . __s()
            )
        ));
    }

    public function getlist()
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Role',
                    'table' => 'roles',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Role.id = Position.role_id'
                    )
                ),
            ),
            'fields' => array(
                'Position.id',
                'Position.name' . __s(),
                'Position.role_id',
                'Role.name' . __s(),
            ),
            'order' => array(
                'Role.name' . __s(),
                'Position.name' . __s(),
            )
        ));
    }

    public function getListDirectory($roles)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.position_id = Position.id'
                    )
                ),
            ),
            'conditions' => array(
                'Position.role_id' => $roles
            ),
            'fields' => array(
                'Position.id',
                'Position.id',
            ),
            'order' => array(
                'Position.name' . __s(),
            )
        ));
    }

    public function getListSearchDirectory($roles)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Role',
                    'table' => 'roles',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Role.id = Position.role_id'
                    )
                ),
                array(
                    'alias' => 'Contact',
                    'table' => 'contacts',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Contact.position_id = Position.id'
                    )
                ),
            ),
            'conditions' => array(
                'Position.role_id' => $roles
            ),
            'fields' => array(
                'Position.id',
                'Position.name' . __s(),
            ),
            'order' => array(
                'Position.name' . __s(),
            )
        ));
    }

    public function getPositionByRole($roles)
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Role',
                    'table' => 'roles',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Role.id = Position.role_id'
                    )
                ),
            ),
            'conditions' => array(
                'Role.id' => $roles,
            ),
            'fields' => array(
                'Position.id',
                'Position.name' . __s(),
                'Position.role_id',
                'Role.name' . __s(),
            ),
        ));
    }

    public function getListPositionByRole($roles)
    {
        return $this->find('list', array(
            'joins' => array(
                array(
                    'alias' => 'Role',
                    'table' => 'roles',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Role.id = Position.role_id'
                    )
                ),
            ),
            'conditions' => array(
                'Role.id IN' => $roles,
            ),
            'fields' => array(
                'Position.id',
                'Position.id',
            ),
        ));
    }

    public function get_list_staff_garage_manager()
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Role',
                    'table' => 'roles',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Role.id = Position.role_id'
                    )
                ),
            ),
            'conditions' => array(
                'OR' => array(
                    array('role_id' => ConstantsRoles::GARAGE),
                    array('role_id' => ConstantsRoles::GENERIC_STAFF),
                )
            ),
            'fields' => array(
                'Position.id',
                'Position.name' . __s(),
                'Position.role_id',
                'Role.name' . __s(),
            ),
            'order' => array(
                'Role.name' . __s(),
                'Position.name' . __s(),
            )
        ));
    }

    public function get_list_staff_distributor_manager()
    {
        return $this->find('all', array(
            'joins' => array(
                array(
                    'alias' => 'Role',
                    'table' => 'roles',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Role.id = Position.role_id'
                    )
                ),
            ),
            'conditions' => array(
                'OR' => array(
                    //array('role_id' => ConstantsRoles::DISTRIBUTOR),
                    array('role_id' => ConstantsRoles::GENERIC_STAFF),
                )
            ),
            'fields' => array(
                'Position.id',
                'Position.name' . __s(),
                'Position.role_id',
                'Role.name' . __s(),
            ),
            'order' => array(
                'Role.name' . __s(),
                'Position.name' . __s(),
            )
        ));
    }

    private function checkNames($data)
    {
        if (empty($data['Position']['name_fr'])) {
            $data['Position']['name_fr'] = $data['Position']['name_en'];
        }
        if (empty($data['Position']['name_de'])) {
            $data['Position']['name_de'] = $data['Position']['name_en'];
        }

        return $data;
    }
}
