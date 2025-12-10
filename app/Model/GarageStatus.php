<?php

class GarageStatus extends AppModel{
    public $useTable = 'garages_statuses';
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