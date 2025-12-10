<?php

class GarageVisitFrequency extends AppModel{
    public $useTable = 'garages_visit_frequencies';
    public $displayField = 'name';

    public $validate = array(
        'name' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
    );

}