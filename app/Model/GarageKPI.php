<?php

class GarageKPI extends AppModel{
    public $useTable = 'garages_kpis';

    public $validate = array(
		'kpis' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

}