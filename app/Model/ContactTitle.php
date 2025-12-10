<?php

class ContactTitle extends AppModel{
    public $useTable = 'contacts_titles';
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