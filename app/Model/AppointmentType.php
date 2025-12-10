<?php

class AppointmentType extends AppModel{
    public $useTable = 'appointments_types';
    public $displayField = 'name_en';
    public $hasMany = array(
        'Appointment'
    );

	public $validate = array(
		'name_en' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_fr' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_de' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
        'name_nl' => array(
			'maxLength' => array(
                'rule' => array('maxLength', ConstantsValidation::MAX_LENGTH_VARCHAR),
                'message' => 'Validation.Name_is_too_long',
            ),
		),
	);

    private $_queries = array(
        'search' =>
            array(
                'fields' => array(
                    'AppointmentType.*',
                )
            ),
    );

    public function _query( $index ){
        return $this->_queries[$index];
    }

    public function new_appointment_type( $event ){
        $fields = array(
            'AppointmentType' => array(
                'name_en',
                'name_fr',
                'name_de',
                'is_event'
            )
        );
        $this->create();
        $service_bd = $this->guardar($event, $fields);
        if ( !$service_bd ){
            return false;
        }

        $this->commit();
        return true;
    }


    public function search_list(){
        return $this->find('list',array(
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name' . __s()
            )
        ));
    }

    public function search_appointments_visit_types(){
        return $this->find('list',array(
            'conditions' => array(
                'is_event' => ConstantsBooleans::NO
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name' . __s()
            )
        ));
    }

    public function search_list_appointment(){
        return $this->find('list',array(
            'conditions' => array(
                'is_event' => ConstantsBooleans::NO
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name' . __s()
            )
        ));
    }

    public function search_list_event(){
        return $this->find('list', array(
            'conditions' => array(
                'is_event' => ConstantsBooleans::YES
            ),
            'fields' => array(
                'id',
                'name_' . __l()
            ),
            'order' => array(
                'name' . __s()
            )
        ));
    }
}