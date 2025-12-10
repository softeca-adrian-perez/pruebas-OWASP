<?php

class GroupingPermission extends AppModel{

    public $useTable = 'groupings_permissions';

    public $hasMany = array(
        'Permission',
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

    private function _conditionId( $id ){
        return array('GroupingPermission.id' => $id);
    }

    private function _conditionName( $name ){
        return array('GroupingPermission.name LIKE' => '%' . $name . '%');
    }

    private function _conditionActive( $active ){
        return array('GroupingPermission.active' => $active);
    }

    public function conditions( $fields ){
        $conditions = array();

        if(isset($fields['id']) && $fields['id']!==''){
            $conditions[] = $this->_conditionId($fields['id']);
        }

        if(!empty($fields['name'])){
            $conditions[] = $this->_conditionName($fields['name']);
        }

        if(isset($fields['active']) && $fields['active']!==''){
            $conditions[] = $this->_conditionActive($fields['active']);
        }

        return $conditions;
    }

    /**
     * Gets all permission pools based on received filters
     *
     * @param int $active
     * @return array
     */
    public function obtenerAgrupacionesPermisos($active = ConstantsBooleans::ACTIVE){

        $conditions = array();

        $fields['active'] = $active;

        // The conditions are added
        $conditions[] = $this->conditions($fields);

        return $this->find(
            'list',
            array(
                'conditions' => $conditions,
                'order'=> 'name',
            )
        );
    }

}