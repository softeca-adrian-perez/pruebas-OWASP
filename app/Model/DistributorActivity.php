<?php

class DistributorActivity extends AppModel{
    public $useTable = 'distributors_activities';

    public $validate = array(
        'start_date' => array(
            'rule' => 'date', 'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
        ),
        'end_date' => array(
            'rule' => 'date', 'dmy',
            'message' => 'Validation.Format_date',
            'allowEmpty' => true
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

    public function search_list(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'id'
            )
        ));
    }

}