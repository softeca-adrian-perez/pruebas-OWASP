<?php

class ShortcutType extends AppModel
{
    public $useTable = 'shortcuts_types';

    public $validate = array(
        'name_en' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_fr' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_de' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
        'name_nl' => array(
            array(
                'rule' => 'notBlank',
                'required' => true,
                'message' => 'Validation.Mandatory_to_choose_a_name'
            ),
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

    private $_queries = array(
        'search_maintenance_types' => array(
            'fields' => array(
                '*',
            ),
        ),
        'home' =>
        array(
            'fields' => array(
                'ShortcutType.*',
            ),
            'order' => 'ShortcutType.name_en asc',
        ),
    );

    public function _query($index)
    {
        return $this->_queries[$index];
    }

    public function search_list()
    {
        return $this->find('list', array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'id'
            )
        ));
    }

    public function edit($shortcut_type)
    {
        $fields = array(
            'ShortcutType' => array(
                'name_en',
                'name_fr',
                'name_de',
                'single',
            )
        );

        $shortcut_type_bd = $this->guardar($shortcut_type, $fields);
        if (!$shortcut_type_bd) {
            return false;
        }

        return $shortcut_type_bd;
    }
}
