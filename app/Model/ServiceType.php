<?php

class ServiceType extends AppModel{

    public $useTable = 'services_types';

    public $validate = array(
		'name' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

}?>