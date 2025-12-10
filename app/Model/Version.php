<?php

class Version extends AppModel{

    public $useTable = 'versions';
    public $displayField = 'name';

    public $hasAndBelongsToMany = array(
        'Permission' => array(
            'joinTable' => 'permissions_versions',
            'foreignKey' => 'version_id',
            'associationForeignKey' => 'permission_id',
        ),
    );

    public $validate = array(
        'name' => array(
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

    private function _conditionId($id){
        return array('Version.id' => $id);
    }

    private function _conditionName( $name ){
        return array('Version.name LIKE' => '%' . $name . '%');
    }

    private function _conditionActive( $active ){
        return array('Version.active' => $active);
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
     * Get all versions based on received filters
     *
     * @param int $active
     * @return array
     */
    public function getVersions( $active = ConstantsBooleans::ACTIVE ){

        $conditions = array();

        $fields['active'] = $active;

        // Add conditions
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