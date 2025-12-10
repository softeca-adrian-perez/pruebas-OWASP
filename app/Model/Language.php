<?php

class Language extends AppModel
{
    public $useTable = 'languages';

    public $validate = array(
        'code' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_DECIMALES),
                'message' => 'Validation.Name_is_too_long',
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

    public function getLanguagesCodeName()
    {
        return $this->find('list', array('fields' => array('code', 'name' . __s())));
    }

    public function getLanguagesCodeNameWithoutLoco()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'id',
                    'name' . __s()
                ),
                'conditions' => array(
                    'code != ' => ConstantsLanguages::LOCO_CODE
                )
            )
        );
    }

    public function getLanguagesIdName()
    {
        return $this->find('list', array('fields' => array('id', 'name' . __s())));
    }

    public function getLanguagesCodeWithoutLoco()
    {
        return $this->find(
            'list',
            array(
                'fields' => array(
                    'id',
                    'code'
                ),
                'conditions' => array(
                    'code != ' => ConstantsLanguages::LOCO_CODE
                )
            )
        );
    }

    public function getLanguagesWithoutLoco()
    {
        return $this->find(
            'all',
            array(
                'fields' => array(
                    'Language.*'
                ),
                'conditions' => array(
                    'code != ' => ConstantsLanguages::LOCO_CODE
                )
            )
        );
    }
}
