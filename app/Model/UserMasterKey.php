<?php

App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class UserMasterKey extends AppModel{

    public $useTable = 'users_master_key';

    public $actsAs = array(
        'FechaHora' => array(
            'fields' => array(
                'modification_date',
            )
        )
    );

    public $validate = array(
        'master_key' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_master_key'
            ),
            'between' => array(
                'rule'    => array('between', 8, 50),
                'message' => 'Validation.Master_key_between_8_and_50_characters'
            ),
            'validateCamposIguales' => array(
                'rule' => array('validateCamposIguales', 'repeated_master_key'),
                'message' => 'Validation.Master_key_not_match'
            ),
            'validatePassword' => array(
                'rule' => array('validatePassword'),
                'message' => 'Validation.Master_key_must_have',
            ),
        ),
    );

    public function beforeSave($options = array()) {
        if (!empty($this->data[$this->alias]['master_key'])) {
            $passwordHasher = new SimplePasswordHasher(array('hashType' => 'sha256'));
            $this->data[$this->alias]['master_key'] = $passwordHasher->hash(
                $this->data[$this->alias]['master_key']
            );
        }
        return true;
    }

    public function edit( $master_key ){
        $fields = array(
            'UserMasterKey' => array(
                'master_key',
                'modification_date',
            )
        );
        $master_key['UserMasterKey']['id'] = 1;
        $master_key['UserMasterKey']['modification_date'] = date('Y-m-d H:i:s');
        return $this->guardar($master_key, $fields);
    }

    public function master_key_list(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'master_key'
            ),
        ));
    }

}