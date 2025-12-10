<?php

class GarageFigure extends AppModel{
    public $useTable = 'garages_figures';

    public $validate = array(
		'figures' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

}