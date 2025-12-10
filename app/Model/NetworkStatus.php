<?php

class NetworkStatus extends AppModel{
    public $useTable = 'networks_statuses';
    public $displayField = 'name';

	public $validate = array(
		'name' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

}