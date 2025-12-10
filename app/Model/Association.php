<?php

class Association extends AppModel{
    public $useTable = 'associations';
    public $displayField = 'name';

	public $validate = array(
		'name' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    public function new_association( $association ){
        $fields = array(
            'Association' => array(
                'name',
            )
        );
        $this->create();
        $service_bd = $this->guardar($association, $fields);
        if ( !$service_bd ){
            return false;
        }

        $this->commit();
        return true;
    }

    private $_queries = array(
        'search' =>
            array(
                'fields' => array(
                    'Association.*',
                )
            ),
    );

    public function _query( $index ){
        return $this->_queries[$index];
    }

}