<?php

class AppointmentStatus extends AppModel{
    public $useTable = 'appointments_status';

    public $hasMany = array(
        'Appointment',
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

    public function search_list_appointment_edit_De(){
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'is_event' => ConstantsBooleans::NO,
                    'id' => array( ConstantsStatusAppointments::PLANNED,ConstantsStatusAppointments::RESCHEDULED ),
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

    public function search_list_appointment_status_running_De(){
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'is_event' => ConstantsBooleans::NO,
                    'id' => array( ConstantsStatusAppointmentsDe::RUNNING),
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
    public function search_list_appointment_status_completed_De(){
        return $this->find(
            'list',
            array(
                'conditions' => array(
                    'is_event' => ConstantsBooleans::NO,
                    'id' => array( ConstantsStatusAppointments::ACCOMPLISHED),
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