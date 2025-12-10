<?php

class DistributorFigureDetail extends AppModel{
    public $useTable = 'distributors_figures_details';

    public $validate = array(
        'figures_details' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );
    
}