<?php

class AppointmentCode extends AppModel{
    public $useTable = 'appointments_codes';

    public $validate = array(
		'office_code' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

}