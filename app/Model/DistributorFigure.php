<?php

class DistributorFigure extends AppModel{
    public $useTable = 'distributors_figures';

    public $validate = array(
        'figures' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );
}