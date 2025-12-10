<?php

class AppointmentContactList extends AppModel{

    public $useTable = 'appointments_contacts_lists';

    public $hasMany = array(
        'Appointment'
    );

    public function add_appointment_contact_lists( $appointments_contact_lists , $appointment ){
        $fields = array(
            'AppointmentContactList' => array(
                'appointment_id',
                'contact_list_id',
            )
        );

        foreach($appointments_contact_lists['appointment_contact_lists'] as  $reason){
            $this->create();
            $reason_bd['appointment_id'] = isset($appointment['Appointment']['id']) ? $appointment['Appointment']['id'] : '';
            $reason_bd['contact_list_id'] = $reason;

            if(!$this->guardar( $reason_bd, $fields )){
                return false;
            }
        }

        $this->commit();
        return true;
    }

    public function remove_contact_lists( $appointment_id ){
        $contact_lists = $this->findAllByAppointmentId( $appointment_id );
        foreach ($contact_lists as $contact_list) {
            $this->delete($contact_list['AppointmentContactList']['id']);
        }
    }

	/**
	 * T001 SECURITY - It is not changed because it does not receive variable parameters per call.
	*/
    public function resetAutoIncrement(){
        $sql = "ALTER TABLE `appointments_contacts_lists` AUTO_INCREMENT = 1;";
        $this->query($sql);
    }

    public function getListByAppointmentId( $appointment_id ) {
        return $this->find('list', array(
                'conditions' => array(
                    'AppointmentContactList.appointment_id' => $appointment_id
                ),
                'fields' => array(
                    'AppointmentContactList.contact_list_id',
                    'AppointmentContactList.contact_list_id'
                ),
            )
        );
    }



}
