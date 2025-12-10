<?php

class Config extends AppModel{
    public $useTable = 'config';

    public $hasMany = array(
        'ConfigSection',
    );

	public $validate = array(
		'name_en' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'tooltip_en' => array(
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
        'tooltip_fr' => array(
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
        'tooltip_de' => array(
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
        'tooltip_nl' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function get_list_config(){
        return $this->find('list', array(
                'fields' => array(
                    'id',
                    'active'
                )
            )
        );
    }

    public function get_list_config_tabs(){
        return $this->find('list', array(
                'fields' => array(
                    'id',
                    'active'
                ),
                'conditions' => array(
                    'Config.section_id' => ConstantsSections::TABS
                ),
            )
        );
    }

    public function get_list_config_modules(){
        return $this->find('all', array(
                'fields' => array(
                    'id',
                    'active'
                ),
                'conditions' => array(
                    'Config.section_id' => ConstantsSections::MODULES
                ),
            )
        );
    }
}