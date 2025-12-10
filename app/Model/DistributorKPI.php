<?php

class DistributorKPI extends AppModel{
    public $useTable = 'distributors_kpis';

    public $validate = array(
        'kpis' => array(
            'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_TEXT),
                'message' => 'Validation.Name_is_too_long',
            ),
        ),
    );

}