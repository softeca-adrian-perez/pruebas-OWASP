<?php

class Role extends AppModel{
    public $useTable = 'roles';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array(
        'Permission' => array(
            'joinTable' => 'permissions_roles',
            'foreignKey' => 'role_id',
            'associationForeignKey' => 'permission_id',
        ),
        'GroupPermission' => array(
            'joinTable' => 'groups_permissions_roles',
            'foreignKey' => 'role_id',
            'associationForeignKey' => 'group_permission_id',
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
        'role' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function search_list(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name'. __s()
            ),
            'order' => array(
                'name'. __s()
            )
        ));
    }

    public function getPositionRole(){
        return $this->find('list',array(
            'joins' => array(
                array(
                    'table' => 'positions',
                    'alias' => 'Position',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Position.role_id = Role.id'
                    )
                ),
            ),
            'fields' => array(
                'Position.id',
                'Role.name'.__s(),
            )
        ));
    }

    public function search_list_not_superadmin(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name'. __s()
            ),
            'order' => array(
                'name'. __s()
            ),
            'conditions' => array(
                'id !=' => ConstantsRoles::SUPER_ADMIN
            )
        ));
    }

}